<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

$root = dirname(__DIR__, 3);
require $root . '/vendor/autoload.php';
require $root . '/vendor/yiisoft/yii2/Yii.php';
require_once $root . '/backend/tests/_schema-bootstrap.php';
Yii::setAlias('@common', $root . '/common');
Yii::setAlias('@backend', $root . '/backend');
Yii::setAlias('@webroot', $root . '/backend/web');
Yii::setAlias('@web', '/');
$_SERVER['REQUEST_SCHEME'] = 'https';
$_SERVER['SERVER_NAME'] = 'test.example';
$_SERVER['HTTP_HOST'] = 'test.example';
$_SERVER['REQUEST_URI'] = '/clubs/role-create';
$_SERVER['SCRIPT_NAME'] = '/index.php';

class ClubRolePermissionsTestView extends yii\web\View
{
	public function registerAssetBundle($name, $position = null)
	{
		return null;
	}
}

class ClubRolePermissionsControllerTestDouble extends \backend\controllers\ClubsController
{
	public $logs = [];

	public function hasPermission($permission, $id = null)
	{
		return true;
	}

	public function createLog($isEnabled, $username, $activity, $name = 'activity')
	{
		$this->logs[] = $activity;
	}

	public function getNowTime($offset = null)
	{
		return date('Y-m-d H:i:s');
	}

	public function render($view, $params = [])
	{
		return ['view' => $view, 'params' => $params];
	}
}

$config = require $root . '/common/config/test-local.php';
$app = new yii\web\Application([
	'id' => 'club-role-permissions-tests',
	'basePath' => $root,
	'vendorPath' => $root . '/vendor',
	'components' => [
		'db' => $config['components']['db'],
		'request' => [
			'cookieValidationKey' => 'club-role-permissions-tests',
			'enableCsrfValidation' => false,
		],
		'response' => ['class' => yii\web\Response::class],
		'session' => ['class' => yii\web\Session::class],
		'view' => ['class' => ClubRolePermissionsTestView::class],
		'urlManager' => [
			'enablePrettyUrl' => true,
			'showScriptName' => false,
		],
	],
	'params' => ['rootUrl' => 'https://test.example'],
]);

$db = Yii::$app->db;
$settings = \backend\models\Params::findOne(1);
if ($settings === null) {
	throw new RuntimeException('The required params record 1 is missing.');
}
$originalPermissions = $settings->privs;
$controller = new ClubRolePermissionsControllerTestDouble('clubs', $app);
Yii::$app->controller = $controller;
Yii::$app->session->open();
$_SESSION['user'] = 'Role Permission Test';

$passed = 0;
$assert = static function ($condition, $message) use (&$passed) {
	if (!$condition) {
		throw new RuntimeException($message);
	}

	$passed++;
	echo "PASS: {$message}\n";
};
$roleId = null;
$roleName = 'Role Permission Test ' . strtoupper(bin2hex(random_bytes(5)));

try {
	$settings->privs = json_encode([
		'clubs/role-update',
		'clubs/roles',
		'events/view',
	], JSON_UNESCAPED_SLASHES);
	$settings->save(false, ['privs']);

	Yii::$app->request->setBodyParams([]);
	$_SERVER['REQUEST_METHOD'] = 'GET';
	$createForm = $controller->actionRoleCreate();
	$permissionGroups = $createForm['params']['permissionGroups'];
	$assert(
		$createForm['view'] === 'role-create'
			&& array_keys($permissionGroups) === ['clubs', 'events']
			&& $permissionGroups['clubs'] === ['clubs/role-update', 'clubs/roles'],
		'actionRoleCreate groups current permissions by section.'
	);

	$roleModel = new \backend\models\Roles();
	$roleHtml = Yii::$app->view->renderFile(
		$root . '/backend/views/clubs/_form_role.php',
		['model' => $roleModel, 'permissionGroups' => $permissionGroups],
		$controller
	);
	$assert(
		str_contains($roleHtml, 'data-permission="clubs/roles"')
			&& str_contains($roleHtml, 'aria-pressed="false"')
			&& str_contains($roleHtml, 'clubs/roles:'),
		'_form_role renders current permissions as off/on toggle buttons.'
	);

	$createPost = [
		'Roles' => [
			'role_name' => $roleName,
			'disp_order' => 77,
			'r_privs' => json_encode(['events/view', 'clubs/roles']),
		],
	];
	$_SERVER['REQUEST_METHOD'] = 'POST';
	Yii::$app->request->setBodyParams($createPost);
	$result = $controller->actionRoleCreate();
	$role = \backend\models\Roles::find()->where(['role_name' => $roleName])->one();
	$roleId = $role ? (int)$role->role_id : null;
	$assert(
		$result instanceof yii\web\Response
			&& $role
			&& $role->r_privs === '["clubs/roles","events/view"]',
		'actionRoleCreate saves selected permissions as a sorted JSON array.'
	);

	$updatePost = [
		'Roles' => [
			'role_name' => $roleName,
			'disp_order' => 77,
			'r_privs' => json_encode(['clubs/role-update']),
		],
	];
	Yii::$app->request->setBodyParams($updatePost);
	$controller->actionRoleUpdate($roleId);
	$role = \backend\models\Roles::findOne($roleId);
	$updateLog = end($controller->logs);
	$roleIdPosition = strpos($updateLog, 'Club Role Updated : '.$roleId);
	$changesPosition = strpos($updateLog, 'Privileges:');
	$assert(
		$role->r_privs === '["clubs/role-update"]'
			&& $roleIdPosition !== false
			&& $changesPosition !== false
			&& $roleIdPosition < $changesPosition
			&& str_contains($updateLog, '["clubs/roles","events/view"] -> ["clubs/role-update"]'),
		'actionRoleUpdate persists selected permissions and logs the changes after the role ID.'
	);

	$invalidPost = [
		'Roles' => [
			'role_name' => $roleName,
			'disp_order' => 77,
			'r_privs' => json_encode(['accounts/delete']),
		],
	];
	Yii::$app->request->setBodyParams($invalidPost);
	$invalidResult = $controller->actionRoleUpdate($roleId);
	$role = \backend\models\Roles::findOne($roleId);
	$assert(
		$invalidResult['view'] === 'role-update'
			&& $invalidResult['params']['model']->hasErrors('r_privs')
			&& $role->r_privs === '["clubs/role-update"]',
		'actionRoleUpdate rejects permissions outside the current permission list.'
	);
} finally {
	if ($roleId) {
		$db->createCommand()->delete('roles', ['role_id' => $roleId])->execute();
	}
	$db->createCommand()->update('params', ['privs' => $originalPermissions], ['id' => 1])->execute();
}

echo "Passed {$passed} club role permission assertions.\n";
