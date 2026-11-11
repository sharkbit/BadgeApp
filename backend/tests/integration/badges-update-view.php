<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

$root = dirname(__DIR__, 3);
require $root . '/vendor/autoload.php';
require $root . '/vendor/yiisoft/yii2/Yii.php';
require $root . '/common/config/bootstrap.php';
require $root . '/backend/config/bootstrap.php';

$_SERVER['REQUEST_SCHEME'] = 'https';
$_SERVER['SERVER_NAME'] = 'test.example';
$_SERVER['HTTP_HOST'] = 'test.example';
$_SERVER['REQUEST_URI'] = '/badges/update';
$_SERVER['SCRIPT_NAME'] = '/index.php';
Yii::setAlias('@webroot', $root . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'web');
Yii::setAlias('@web', '/');
Yii::$container->set(yii\web\JqueryAsset::class, [
	'sourcePath' => null,
	'js' => [],
]);

class BadgesUpdateViewTestView extends yii\web\View {
	public function registerAssetBundle($name, $position = null) {
		return null;
	}
}

$config = require $root . '/backend/config/test-local.php';
$dbConfig = $config['components']['db'] ?? null;
$databaseName = '';
if (is_array($dbConfig) && preg_match('/dbname=([^;]+)/i', $dbConfig['dsn'] ?? '', $match)) {
	$databaseName = $match[1];
}

if ($databaseName === '' || stripos($databaseName, 'test') === false) {
	fwrite(STDERR, "Refusing to run: the configured database name must contain 'test'.\n");
	exit(1);
}

if (!extension_loaded('pdo_mysql')) {
	fwrite(STDERR, "The pdo_mysql PHP extension is required; run with -d extension=pdo_mysql.\n");
	exit(1);
}

$app = new yii\web\Application([
	'id' => 'badge-update-view-tests',
	'basePath' => $root . '/backend',
	'vendorPath' => $root . '/vendor',
	'controllerNamespace' => 'backend\\controllers',
	'components' => [
		'db' => $dbConfig,
		'request' => [
			'cookieValidationKey' => 'badge-update-view-test-key',
			'enableCsrfValidation' => false,
		],
		'user' => [
			'identityClass' => common\models\User::class,
			'enableSession' => false,
		],
		'view' => [
			'class' => BadgesUpdateViewTestView::class,
		],
		'urlManager' => [
			'enablePrettyUrl' => true,
			'showScriptName' => false,
		],
	],
	'params' => [
		'rootUrl' => 'https://test.example',
		'wp_site' => 'https://test.example',
		'timeZone' => 'UTC',
		'env' => 'test',
	],
]);

class BadgesUpdateViewTestController extends yii\web\Controller {
	public $permissions = [];

	public function hasPermission($permission) {
		return in_array($permission, $this->permissions, true);
	}

	public function getNowTime() {
		return date('Y-m-d H:i:s');
	}

	public function getStates() {
		return ['MD' => 'Maryland', 'VA' => 'Virginia'];
	}

	public function createLog($success, $location, $message) {
	}
}

$passed = 0;
$assert = static function ($condition, $message) use (&$passed) {
	if (!$condition) {
		throw new RuntimeException($message);
	}

	$passed++;
	echo "PASS: {$message}\n";
};

$db = Yii::$app->db;
$transaction = $db->beginTransaction();

try {
	if (!$db->schema->getTableSchema('badges', true)
		|| !$db->schema->getTableSchema('badge_subscriptions', true)
		|| !$db->schema->getTableSchema('account_status', true)) {
		throw new RuntimeException('The configured test database is missing BadgeDB schema tables.');
	}

	if (!$db->createCommand('SELECT 1 FROM params WHERE id = 1')->queryScalar()) {
		$db->createCommand()->insert('params', [
			'id' => 1,
			'sell_date' => '01-01',
		])->execute();
	}

	$statusCode = 'T' . strtoupper(bin2hex(random_bytes(1)));
	$db->createCommand()->insert('account_status', [
		'act_short' => $statusCode,
		'act_name' => 'View Test Active',
		'act_login' => 1,
		'act_prefill' => 0,
		'act_order' => 1,
		'act_active' => 1,
		'act_new' => 1,
		'act_renew' => 1,
		'act_signup' => 0,
	])->execute();

	$clubSuffix = bin2hex(random_bytes(4));
	$db->createCommand()->insert('clubs', [
		'club_name' => 'View Test Club ' . $clubSuffix,
		'short_name' => 'VT' . strtoupper(substr($clubSuffix, 0, 6)),
		'status' => '0',
		'is_club' => 1,
		'allow_members' => 1,
	])->execute();

	$db->createCommand()->insert('membership_type', [
		'type' => 'View Test Type',
		'status' => 1,
		'renew_yearly' => 1,
	])->execute();
	$membershipTypeId = (int)$db->getLastInsertID();

	$currentYear = (int)date('Y');
	$badgeNumbers = [
		'old' => random_int(1000000, 1900000),
		'current' => random_int(1900001, 2900000),
	];
	foreach (['old' => $currentYear - 3, 'current' => $currentYear] as $key => $year) {
		$db->createCommand()->insert('badge_subscriptions', [
			'badge_number' => $badgeNumbers[$key],
			'badge_year' => $year,
			'payment_type' => 'pos',
			'status' => 'active',
			'created_at' => date('Y-m-d H:i:s'),
			'paid_amount' => 0,
			'discount' => 0,
		])->execute();
	}

	$identity = new common\models\User();
	$identity->setAttributes([
		'id' => 1,
		'status' => common\models\User::STATUS_ACTIVE,
		'privilege' => '[1]',
	], false);
	Yii::$app->user->setIdentity($identity);

	$controller = new BadgesUpdateViewTestController('badges', Yii::$app);
	$controller->id = 'badges';
	Yii::$app->controller = $controller;

	$renderUpdateForm = static function ($badgeNumber, $permissions) use ($controller, $root) {
		$controller->permissions = $permissions;
		$model = new backend\models\Badges();
		$model->setAttributes([
			'badge_number' => $badgeNumber,
			'prefix' => 'Ms',
			'first_name' => 'Taylor',
			'last_name' => 'Tester',
			'mem_type' => (int)backend\models\MembershipType::find()
				->select('id')
				->where(['type' => 'View Test Type'])
				->scalar(),
			'club_id' => [],
			'address' => '123 Test Street',
			'city' => 'Testville',
			'state' => 'MD',
			'zip' => '21234',
			'gender' => 'f',
			'yob' => 1990,
			'email' => 'badge-view-test@example.invalid',
			'email_vrfy' => 0,
			'phone' => '4105551234',
			'phone_op' => '',
			'ice_contact' => 'Test Contact',
			'ice_phone' => '4105554321',
			'incep' => date('Y-m-d H:i:s'),
			'wt_date' => date('Y-m-d'),
			'wt_instru' => 'Test Instructor',
			'qrcode' => 'TEST123456789',
			'status' => 'ACT',
			'remarks' => '[]',
		], false);
		$model->setOldAttributes(['badge_number' => $badgeNumber]);

		$subscription = new backend\models\BadgeSubscriptions();
		$subscription->setAttributes([
			'badge_number' => $badgeNumber,
			'badge_year' => date('Y'),
			'badge_fee' => 50,
			'amount_due' => 50,
			'payment_type' => 'pos',
			'status' => 'active',
			'sticker' => 'TEST-00001',
			'created_at' => date('Y-m-d H:i:s'),
			'paid_amount' => 0,
			'discount' => 0,
			'wt_date' => '',
			'wt_instru' => '',
		], false);
		$certification = new backend\models\BadgeCertification();
		$viewParams = [
			'model' => $model,
			'confParams' => backend\models\Params::findOne(1),
			'badgeSubscriptions' => $subscription,
			'badgeCertification' => $certification,
		];

		return Yii::$app->view->renderFile(
			$root . '/backend/views/badges/_update.php',
			$viewParams,
			$controller
		);
	};

	$fullPermissions = [
		'badges/all',
		'badges/barcode',
		'badges/rename',
		'badges/renew-membership',
		'badges/add-certification',
		'badges/update',
		'badges/overideprice',
		'sales/all',
	];
	$oldBadgeHtml = $renderUpdateForm($badgeNumbers['old'], $fullPermissions);
	$assert(str_contains($oldBadgeHtml, 'id="badgeUpdate"'), 'Renders the main badge update form.');
	$assert(str_contains($oldBadgeHtml, 'id="form_badge_renew"'), 'Renders the renewal form.');
	$assert(str_contains($oldBadgeHtml, 'id="form_badge_cert"'), 'Renders the certification form.');
	$assert(str_contains($oldBadgeHtml, 'name="mem_update_btn"'), 'Renders the badge update button.');
	$assert(str_contains($oldBadgeHtml, 'name="BadgeSubscriptions[wt_date]"'), 'Requires WT date for an old badge.');
	$assert(str_contains($oldBadgeHtml, 'name="BadgeSubscriptions[wt_instru]"'), 'Requires WT instructor for an old badge.');
	$assert(str_contains($oldBadgeHtml, 'WT Date cannot be blank.'), 'Adds client validation for missing WT date.');
	$assert(str_contains($oldBadgeHtml, 'WT Instru cannot be blank.'), 'Adds client validation for missing WT instructor.');
	$assert(str_contains($oldBadgeHtml, 'id="newNumber_btn"'), 'Offers barcode regeneration to authorized users.');
	$assert(str_contains($oldBadgeHtml, 'id="badgesubscriptions-amount_override"'), 'Shows amount override to authorized users.');
	$assert(str_contains($oldBadgeHtml, 'id="badges-renew_btn"') || str_contains($oldBadgeHtml, 'id="renew_btn"'), 'Renders the renewal submit button.');
	$assert(str_contains($oldBadgeHtml, 'id="cert_add"'), 'Renders the certification submit button.');
	$assert(str_contains($oldBadgeHtml, 'BadgeFormLogic.validateRenewalFields'), 'Uses shared renewal validation logic.');
	$assert(str_contains($oldBadgeHtml, 'BadgeFormLogic.calculateCart'), 'Uses shared cart calculation logic.');

	$currentBadgeHtml = $renderUpdateForm($badgeNumbers['current'], $fullPermissions);
	$assert(!str_contains($currentBadgeHtml, 'WT Date cannot be blank.'), 'Omits WT-required validation for a recent badge.');
	$assert(!str_contains($currentBadgeHtml, 'name="BadgeSubscriptions[wt_date]"'), 'Omits WT renewal fields when training is not due.');
	$assert(str_contains($currentBadgeHtml, 'id="form_badge_cert"'), 'Keeps certification available for a current, renewable badge.');

	$restrictedHtml = $renderUpdateForm($badgeNumbers['current'], []);
	$assert(str_contains($restrictedHtml, 'name="Badges[prefix]"'), 'Renders badge identity fields for restricted users.');
	$assert(!str_contains($restrictedHtml, 'id="newNumber_btn"'), 'Hides barcode regeneration without barcode permission.');
	$assert(!str_contains($restrictedHtml, 'id="badgesubscriptions-amount_override"'), 'Hides amount override without override permission.');
	$assert(
		(bool)preg_match('/id="badge_renual_form"[^>]*style="display:none"/', $restrictedHtml),
		'Hides renewal controls without renewal permission.'
	);
	$assert(
		(bool)preg_match('/id="badges-certifications-form"[^>]*style="display:none"/', $restrictedHtml),
		'Hides certification controls without certification permission.'
	);
	$assert(
		(bool)preg_match('/id="purchases_block"[^>]*style="display:none"/', $restrictedHtml),
		'Hides purchase controls without update permission.'
	);
	$assert(str_contains($restrictedHtml, 'BadgeFormLogic.isAmountOverrideEditable'), 'Renders guarded amount override behavior.');
} finally {
	$transaction->rollBack();
}

echo "Passed {$passed} badge update form assertions.\n";
