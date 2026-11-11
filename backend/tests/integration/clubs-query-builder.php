<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

$root = dirname(__DIR__, 3);
require $root . '/vendor/autoload.php';
require $root . '/vendor/yiisoft/yii2/Yii.php';
require_once $root . '/backend/tests/_schema-bootstrap.php';
Yii::setAlias('@common', $root . '/common');
Yii::setAlias('@backend', $root . '/backend');

$config = require $root . '/common/config/test-local.php';
$dbConfig = $config['components']['db'];
$app = new yii\console\Application([
	'id' => 'clubs-query-builder-tests',
	'basePath' => $root,
	'vendorPath' => $root . '/vendor',
	'components' => [
		'db' => $dbConfig,
	],
]);

$db = Yii::$app->db;
$transaction = $db->beginTransaction();
$passed = 0;
$assert = static function ($condition, $message) use (&$passed) {
	if (!$condition) {
		throw new RuntimeException($message);
	}

	$passed++;
	echo "PASS: {$message}\n";
};

try {
	$suffix = strtoupper(bin2hex(random_bytes(3)));
	$db->createCommand()->insert('clubs', [
		'club_name' => 'Query Builder Club ' . $suffix,
		'short_name' => 'QB' . $suffix,
		'status' => '0',
	])->execute();
	$clubId = (int)$db->getLastInsertID();

	$badgeNumber = random_int(1000000, 9000000);
	$db->createCommand()->insert('badges', [
		'badge_number' => $badgeNumber,
		'prefix' => 'Mx',
		'first_name' => 'Query',
		'last_name' => 'Builder',
		'address' => '1 Test Way',
		'city' => 'Testville',
		'state' => 'MD',
		'zip' => '21234',
		'email' => 'clubs-test-' . strtolower($suffix) . '@example.invalid',
		'phone' => '',
		'mem_type' => 1,
		'incep' => date('Y-m-d H:i:s'),
		'wt_date' => date('Y-m-d'),
		'remarks' => '[]',
	])->execute();

	$assert(
		backend\models\clubs::saveClub($badgeNumber, [$clubId]) === 1,
		'saveClub inserts club mappings with a Yii batch insert.'
	);
	$clubModel = new backend\models\clubs();
	$assert(
		$clubModel->getMyClubs($badgeNumber) === [(string)$clubId],
		'getMyClubs returns the mapped club IDs.'
	);
	$assert(
		$clubModel->getMyClubsNames($badgeNumber) === 'Query Builder Club ' . $suffix,
		'getMyClubsNames returns the mapped full club name.'
	);
	$assert(
		$clubModel->getMyClubsNames($badgeNumber, true)
			=== 'QB' . $suffix . ' <img src="/images/note.png" title="Query Builder Club '
				. $suffix . '" style="width:18px" />',
		'getMyClubsNames supports the short-name display format.'
	);
	$assert(
		backend\models\clubs::getClubNames([$clubId]) === 'Query Builder Club ' . $suffix,
		'getClubNames returns comma-separated names for the requested club IDs.'
	);
	$assert(
		backend\models\clubs::getClubNames(json_encode([$clubId])) === 'Query Builder Club ' . $suffix,
		'getClubNames accepts club IDs stored as JSON.'
	);
	$assert(
		backend\models\clubs::getClubNames([]) === '',
		'getClubNames returns an empty string when no IDs are supplied.'
	);
	$roster = $clubModel->getClubRoster($clubId);
	$assert(
		count($roster) === 1 && (int)$roster[0]['badge_number'] === $badgeNumber,
		'getClubRoster returns badges associated with the club.'
	);

	$secondClubId = $clubId + 100000;
	backend\models\clubs::saveClub($badgeNumber, [$secondClubId], false);
	$assert(
		$clubModel->getMyClubs($badgeNumber) === [(string)$clubId, (string)$secondClubId],
		'saveClub preserves existing mappings when delOld is false.'
	);
	$assert(
		backend\models\clubs::saveClub($badgeNumber, []) === 0
			&& $clubModel->getMyClubs($badgeNumber) === [],
		'saveClub can remove all mappings without attempting an empty insert.'
	);
} finally {
	$transaction->rollBack();
}

echo "Passed {$passed} clubs query builder assertions.\n";
