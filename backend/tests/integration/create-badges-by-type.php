<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

$root = dirname(__DIR__, 3);
require $root . '/vendor/autoload.php';
require $root . '/vendor/yiisoft/yii2/Yii.php';
Yii::setAlias('@common', $root . '/common');
Yii::setAlias('@backend', $root . '/backend');

class BadgeTypeGeneratorController extends \backend\controllers\BadgesController
{
	public function hasPermission($event, $id = null)
	{
		return true;
	}

	protected function canOverrideBannedRegistration()
	{
		return false;
	}

	public function sendVerifyEmail($email, $type = 'new', $model = null)
	{
		return null;
	}

	public function createLog($isEnabled, $username, $activity, $name = 'activity')
	{
		return null;
	}
}

if (!extension_loaded('pdo_mysql')) {
	throw new RuntimeException('Run PHP with -d extension=pdo_mysql.');
}

$config = require $root . '/common/config/test-local.php';
$dbConfig = $config['components']['db'] ?? [];
if (!preg_match('/dbname=([^;]+)/i', $dbConfig['dsn'] ?? '', $databaseMatch)
	|| !preg_match('/_test$/i', $databaseMatch[1])) {
	throw new RuntimeException('Refusing to create badges: configured database must end in _test.');
}

$app = new yii\web\Application([
	'id' => 'create-badges-by-type-test',
	'basePath' => $root,
	'vendorPath' => $root . '/vendor',
	'components' => [
		'db' => $dbConfig,
		'request' => [
			'class' => yii\web\Request::class,
			'cookieValidationKey' => 'badge-type-generator-test',
			'enableCsrfValidation' => false,
		],
		'response' => ['class' => yii\web\Response::class],
		'session' => ['class' => yii\web\Session::class],
		'urlManager' => [
			'class' => yii\web\UrlManager::class,
			'hostInfo' => 'https://badge-type-generator.test',
			'baseUrl' => '',
			'enablePrettyUrl' => true,
			'showScriptName' => false,
		],
	],
	'params' => [
		'timeZone' => 'UTC',
		'env' => 'test',
	],
]);

$db = Yii::$app->db;
$controller = new BadgeTypeGeneratorController('badges', Yii::$app);
Yii::$app->controller = $controller;
Yii::$app->session->open();
$_SESSION['user'] = 'Badge Type Test';
$_SESSION['badge_number'] = 0;
$_SESSION['privilege'] = [1];

$membershipTypes = (new yii\db\Query())
	->select(['id', 'type'])
	->from('membership_type')
	->orderBy(['id' => SORT_ASC])
	->all();
$status = (new yii\db\Query())
	->select('act_short')
	->from('account_status')
	->where(['act_active' => 1, 'act_new' => 1])
	->orderBy(['act_order' => SORT_ASC])
	->scalar();

if (!$membershipTypes) {
	throw new RuntimeException('No badge types were found in membership_type.');
}
if ($status === false || $status === null) {
	throw new RuntimeException('No active account status is available for new badges.');
}

$transaction = $db->beginTransaction();
$created = 0;
$existing = 0;
$createdClubs = 0;
$now = date('Y-m-d H:i:s');
$badgeNumber = (int)(new yii\db\Query())->from('badges')->max('badge_number');

try {
	for ($clubIndex = 1; $clubIndex <= 5; $clubIndex++) {
		$clubMarker = 'badge-type-test-club-' . $clubIndex . '@example.invalid';
		$clubExists = (new yii\db\Query())
			->from('clubs')
			->where(['poc_email' => $clubMarker])
			->exists();
		if ($clubExists) {
			$db->createCommand()->update('clubs', ['status' => 1], [
				'poc_email' => $clubMarker,
			])->execute();
			continue;
		}

		$randomSuffix = strtoupper(bin2hex(random_bytes(3)));
		$db->createCommand()->insert('clubs', [
			'club_name' => "Test Badge Club {$clubIndex} {$randomSuffix}",
			'short_name' => "TB{$clubIndex}{$randomSuffix}",
			'poc_email' => $clubMarker,
			'status' => 1,
			'is_club' => 1,
			'allow_members' => 1,
		])->execute();
		$createdClubs++;
	}

	$clubs = (new yii\db\Query())
		->select(['club_id'])
		->from('clubs')
		->where(['poc_email' => array_map(
			static fn($index) => 'badge-type-test-club-' . $index . '@example.invalid',
			range(1, 5)
		)])
		->column();
	if (count($clubs) !== 5) {
		throw new RuntimeException('Could not verify the five generated test clubs.');
	}
	$incorrectClubStatuses = (new yii\db\Query())
		->from('clubs')
		->where(['poc_email' => array_map(
			static fn($index) => 'badge-type-test-club-' . $index . '@example.invalid',
			range(1, 5)
		)])
		->andWhere(['<>', 'status', 1])
		->count();
	if ($incorrectClubStatuses > 0) {
		throw new RuntimeException('Generated test clubs must have status 1.');
	}

	foreach ($membershipTypes as $membershipType) {
		$typeId = (int)$membershipType['id'];
		$typeName = trim((string)$membershipType['type']);
		$marker = 'TEST_BADGE_BY_TYPE:' . $typeId;
		$email = 'badge-type-test-' . $typeId . '@example.invalid';
		$badge = (new yii\db\Query())
			->select(['badge_number', 'mem_type'])
			->from('badges')
			->where(['email' => $email])
			->one();

		$clubId = null;
		if ($badge !== false) {
			if ((int)$badge['mem_type'] !== $typeId) {
				throw new RuntimeException("Existing test badge marker for type {$typeId} has a different membership type.");
			}
			$clubId = (new yii\db\Query())
				->select('club_id')
				->from('badge_to_club')
				->where([
					'badge_number' => (int)$badge['badge_number'],
					'club_id' => $clubs,
				])
				->scalar();
			if ($clubId === false) {
				$clubId = (int)$clubs[array_rand($clubs)];
				$db->createCommand()->insert('badge_to_club', [
					'badge_number' => (int)$badge['badge_number'],
					'club_id' => $clubId,
				])->execute();
			}
			$badgeNumber = (int)$badge['badge_number'];
		} else {
			$clubId = (int)$clubs[array_rand($clubs)];
			$badgeNumber++;
			while ((new yii\db\Query())->from('badges')->where(['badge_number' => $badgeNumber])->exists()) {
				$badgeNumber++;
			}

			$post = [
				'Badges' => [
					'badge_number' => $badgeNumber,
					'prefix' => 'Mx',
					'first_name' => 'Person',
					'last_name' => $typeName,
					'address' => '123 Test Street',
					'city' => 'Testville',
					'state' => 'MD',
					'zip' => '21234',
					'gender' => 'X',
					'email' => $email,
					'phone' => '4105550100',
					'mem_type' => $typeId,
					'primary' => $typeId === 51 ? 'TEST' : null,
					'incep' => $now,
					'wt_date' => date('Y-m-d'),
					'wt_instru' => 'Test',
					'remarks' => $marker,
					'status' => $status,
					'club_id' => [$clubId],
					'badge_year' => date('Y'),
					'badge_fee' => 0,
					'amt_due' => 0,
					'tax' => 0,
					'discounts' => 0,
					'payment_method' => 'creditnow',
					'sticker' => 'TST' . strtoupper(bin2hex(random_bytes(3))),
				],
				'FriendCredits' => 0,
				'item_name' => 'Test Badge',
				'item_sku' => 'TEST',
			];
			Yii::$app->request->setBodyParams($post);
			$_POST = $post;
			$controller->action = $controller->createAction('create');
			$controller->actionCreate();
		}

		$createdBadge = (new yii\db\Query())
			->from('badges')
			->where(['badge_number' => $badgeNumber, 'mem_type' => $typeId, 'email' => $email])
			->one();
		$clubLink = (new yii\db\Query())
			->from('badge_to_club')
			->where(['badge_number' => $badgeNumber, 'club_id' => $clubId])
			->exists();
		$subscription = (new yii\db\Query())
			->from('badge_subscriptions')
			->where(['badge_number' => $badgeNumber])
			->one();

		if (!$createdBadge || !$clubLink) {
			throw new RuntimeException("Badge creation flow failed for membership type {$typeId}.");
		}
		if (!$subscription) {
			$subscriptionModel = new \backend\models\BadgeSubscriptions();
			$subscriptionModel->setAttributes([
				'badge_number' => $badgeNumber,
				'badge_year' => date('Y'),
				'payment_type' => 'credit',
				'status' => 'active',
				'created_at' => $now,
				'badge_fee' => 0,
				'paid_amount' => 0,
				'sticker' => 'TST' . strtoupper(bin2hex(random_bytes(3))),
				'club_id' => $clubId,
				'transaction_type' => 'NEW',
			]);
			$subscriptionModel->discount = 0;
			if (!$subscriptionModel->save(false)) {
				throw new RuntimeException("Failed to create a subscription for existing test badge {$badgeNumber}.");
			}
			$subscription = (new yii\db\Query())
				->from('badge_subscriptions')
				->where(['badge_number' => $badgeNumber])
				->one();
		}

		if (!$subscription) {
			throw new RuntimeException("Badge {$badgeNumber} was created without a subscription.");
		}
		if ($badge !== false) {
			$existing++;
			echo "EXISTS: Person {$typeName} (badge {$badgeNumber}, subscription {$subscription['id']})\n";
		} else {
			$created++;
			echo "CREATED: Person {$typeName} (badge {$badgeNumber}, subscription {$subscription['id']}, club {$clubId})\n";
		}
	}

	$transaction->commit();
} catch (\Throwable $exception) {
	if ($transaction->isActive) {
		$transaction->rollBack();
	}
	throw $exception;
}

echo "Complete: created {$createdClubs} clubs and {$created} badges; found {$existing} existing badges with subscriptions.\n";
