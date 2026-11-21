<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

$root = dirname(__DIR__, 3);
require $root . '/vendor/autoload.php';
require $root . '/vendor/yiisoft/yii2/Yii.php';
require_once $root . '/backend/tests/_schema-bootstrap.php';
Yii::setAlias('@common', $root . '/common');
Yii::setAlias('@backend', $root . '/backend');

class PrivilegesRawControllerTestDouble extends \backend\controllers\PrivilegesController
{
	public $allowDelete = true;
	public $logs = [];

	public function hasPermission($permission, $id = null)
	{
		return $permission !== 'privileges/delete' || $this->allowDelete;
	}

	public function render($view, $params = [])
	{
		return ['view' => $view, 'params' => $params];
	}

	public function createLog($isEnabled, $username, $activity, $name = 'activity')
	{
		$this->logs[] = $activity;
	}
}

$config = require $root . '/common/config/test-local.php';
$app = new yii\web\Application([
	'id' => 'privileges-raw-tests',
	'basePath' => $root,
	'vendorPath' => $root . '/vendor',
	'components' => [
		'db' => $config['components']['db'],
		'request' => [
			'cookieValidationKey' => 'privileges-raw-tests',
			'enableCsrfValidation' => false,
		],
		'response' => ['class' => yii\web\Response::class],
		'session' => ['class' => yii\web\Session::class],
	],
	'params' => [
		'timeZone' => 'UTC',
		'env' => 'test',
	],
]);

$db = Yii::$app->db;
$settings = \backend\models\Params::findOne(1);
if ($settings === null) {
	throw new RuntimeException('The required params record 1 is missing.');
}
$originalPrivs = $settings->privs;
$controller = new PrivilegesRawControllerTestDouble('privileges', $app);
Yii::$app->controller = $controller;
Yii::$app->session->open();

$passed = 0;
$assert = static function ($condition, $message) use (&$passed) {
	if (!$condition) {
		throw new RuntimeException($message);
	}

	$passed++;
	echo "PASS: {$message}\n";
};

$settingsSessionKey = 'paramsRecord';
$testRoleId = null;
Yii::$app->session->remove($settingsSessionKey);
$cachedSettings = \backend\models\Params::getParams();
$assert(
	$cachedSettings instanceof \backend\models\Params
		&& Yii::$app->session->get($settingsSessionKey) instanceof \backend\models\Params,
	'Params::getParams loads the settings record from the database and caches it in session.'
);

$permissionChecker = new \backend\controllers\AdminController('admin', $app);
$assert(
	$permissionChecker->hasPermission('clubs/badge-rosters', 1)
		&& !$permissionChecker->hasPermission('clubs/badge-rosters', 4),
	'hasPermission checks the requested privilege ID instead of the logged-in user.'
);

$_SESSION['badge_number'] = 0;
$_SESSION['privilege'] = [4, 5];
$expectedTimeout = (int)\backend\models\Privileges::find()->where(['id' => [4, 5]])->max('timeout');
$assert(
	$permissionChecker->setSessionTimeout() === $expectedTimeout
		&& $_SESSION['timeout'] === $expectedTimeout,
	'setSessionTimeout stores the highest user privilege timeout.'
);

try {
	$settings->privs = json_encode(['events/view', 'accounts/index']);
	$settings->save(false, ['privs']);

	$_SERVER['REQUEST_METHOD'] = 'POST';
	Yii::$app->request->setBodyParams(['permission' => ' params/update ']);
	$result = $controller->actionRaw();
	$permissions = json_decode(\backend\models\Params::findOne(1)->privs, true);
	$assert(
		$result instanceof yii\web\Response
			&& $permissions === ['accounts/index', 'events/view', 'params/update'],
		'actionRaw adds a permission to params record 1 and saves the sorted JSON array.'
	);
	$assert(
		end($controller->logs) === 'Permission Created : params/update',
		'actionRaw logs a successfully created permission.'
	);

	$logsBeforeDuplicate = count($controller->logs);
	Yii::$app->request->setBodyParams(['permission' => 'params/update']);
	$controller->actionRaw();
	$permissions = json_decode(\backend\models\Params::findOne(1)->privs, true);
	$assert(
		$permissions === ['accounts/index', 'events/view', 'params/update']
			&& count($controller->logs) === $logsBeforeDuplicate,
		'actionRaw does not add duplicate permissions.'
	);

	Yii::$app->request->setBodyParams(['permission' => 'not-a-permission']);
	$result = $controller->actionRaw();
	$assert(
		$result['view'] === 'raw'
			&& json_decode(\backend\models\Params::findOne(1)->privs, true)
				=== ['accounts/index', 'events/view', 'params/update'],
		'actionRaw rejects malformed permission values without changing stored permissions.'
	);

	Yii::$app->request->setBodyParams([]);
	$_SERVER['REQUEST_METHOD'] = 'GET';
	$result = $controller->actionRaw();
	$groups = $result['params']['groupedPermissions'];
	$assert(
		array_keys($groups) === ['accounts', 'events', 'params']
			&& $groups['accounts'] === ['accounts/index']
			&& $groups['events'] === ['events/view']
			&& $groups['params'] === ['params/update'],
		'actionRaw groups the displayed permissions by the text before the slash.'
	);

	$_SERVER['REQUEST_METHOD'] = 'POST';
	Yii::$app->request->headers->set('X-Requested-With', 'XMLHttpRequest');
	$db->createCommand()->insert('roles', [
		'role_name' => 'Permission Delete Test ' . bin2hex(random_bytes(4)),
		'r_privs' => json_encode(['events/view', 'params/update']),
	])->execute();
	$testRoleId = (int)$db->getLastInsertID();
	Yii::$app->request->setBodyParams(['deletePermission' => 'events/view']);
	$deleteResult = $controller->actionRaw();
	$permissions = json_decode(\backend\models\Params::findOne(1)->privs, true);
	$rolePermissions = json_decode(
		(string)\backend\models\Roles::findOne($testRoleId)->r_privs,
		true
	);
	$assert(
		$deleteResult === ['success' => true]
			&& $permissions === ['accounts/index', 'params/update']
			&& $rolePermissions === ['params/update'],
		'actionRaw deletes the selected permission from application permissions and club roles.'
	);
	$deleteLog = end($controller->logs);
	$assert(
		preg_match('/^Permission Deleted : events\/view \(removed from \d+ club roles?\)$/', $deleteLog) === 1,
		'actionRaw logs a successfully deleted permission and affected club-role count.'
	);

	$controller->allowDelete = false;
	Yii::$app->request->setBodyParams(['deletePermission' => 'params/update']);
	$forbidden = false;
	try {
		$controller->actionRaw();
	} catch (\yii\web\ForbiddenHttpException $exception) {
		$forbidden = true;
	}
	$assert(
		$forbidden
			&& json_decode(\backend\models\Params::findOne(1)->privs, true)
				=== ['accounts/index', 'params/update']
			&& json_decode((string)\backend\models\Roles::findOne($testRoleId)->r_privs, true)
				=== ['params/update'],
		'actionRaw blocks permission deletion without privileges/delete.'
	);
} finally {
	$db->createCommand()->update('params', ['privs' => $originalPrivs], ['id' => 1])->execute();
	if ($testRoleId !== null) {
		$db->createCommand()->delete('roles', ['role_id' => $testRoleId])->execute();
	}
	Yii::$app->session->remove($settingsSessionKey);
	Yii::$app->request->headers->remove('X-Requested-With');
}

echo "Passed {$passed} raw privileges assertions.\n";
