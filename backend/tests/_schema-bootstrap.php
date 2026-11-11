<?php

if (getenv('BADGEAPP_TEST_SCHEMA_LOADED') === '1') {
	return;
}

$testRoot = dirname(__DIR__, 2);
require_once $testRoot . '/vendor/autoload.php';
require_once $testRoot . '/vendor/yiisoft/yii2/Yii.php';

Yii::setAlias('@common', $testRoot . '/common');
Yii::setAlias('@backend', $testRoot . '/backend');
$_SERVER['REQUEST_SCHEME'] = $_SERVER['REQUEST_SCHEME'] ?? 'https';
$_SERVER['SERVER_NAME'] = $_SERVER['SERVER_NAME'] ?? 'test.example';

if (!extension_loaded('pdo_mysql')) {
	throw new RuntimeException(
		'The pdo_mysql PHP extension is required. Run PHP with -d extension=pdo_mysql.'
	);
}

$testConfig = require $testRoot . '/common/config/test-local.php';
$dbConfig = $testConfig['components']['db'] ?? [];
$dsn = $dbConfig['dsn'] ?? '';
if (!preg_match('/dbname=([^;]+)/i', $dsn, $databaseMatch)) {
	throw new RuntimeException('The test database name is missing from common/config/test-local.php.');
}

$databaseName = $databaseMatch[1];
if (!preg_match('/_test$/i', $databaseName)) {
	throw new RuntimeException(
		"Refusing to refresh schema: database '{$databaseName}' must end in _test."
	);
}

$schemaFiles = glob($testRoot . '/BadgeDB-v*-schema.sql') ?: [];
usort($schemaFiles, static function ($left, $right) {
	preg_match('/BadgeDB-v([0-9.]+)-schema\.sql$/i', basename($left), $leftVersion);
	preg_match('/BadgeDB-v([0-9.]+)-schema\.sql$/i', basename($right), $rightVersion);
	return version_compare($rightVersion[1] ?? '0', $leftVersion[1] ?? '0');
});

if (!$schemaFiles) {
	throw new RuntimeException('No versioned BadgeDB schema dump was found in the repository root.');
}

$schemaFile = $schemaFiles[0];
$sql = file_get_contents($schemaFile);
if ($sql === false) {
	throw new RuntimeException('Could not read the latest BadgeDB schema dump.');
}

$sql = preg_replace(
	'~/\*!\d+\s+DEFINER=`[^`]+`@`[^`]+`\s+SQL SECURITY DEFINER\s*\*/~',
	'',
	$sql
);
if ($sql === null) {
	throw new RuntimeException('Could not normalize view definers in the schema dump.');
}

$pdo = new PDO(
	$dsn,
	$dbConfig['username'] ?? null,
	$dbConfig['password'] ?? null,
	[
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::MYSQL_ATTR_MULTI_STATEMENTS => true,
	]
);

$activeDatabase = $pdo->query('SELECT DATABASE()')->fetchColumn();
if (strcasecmp((string)$activeDatabase, $databaseName) !== 0) {
	throw new RuntimeException('The connected database does not match the configured test database.');
}

$pdo->exec($sql);

foreach (['user', 'badges', 'account_status', 'membership_type'] as $requiredTable) {
	$statement = $pdo->prepare('SHOW TABLES LIKE :tableName');
	$statement->execute([':tableName' => $requiredTable]);
	if (!$statement->fetchColumn()) {
		throw new RuntimeException(
			"Schema refresh completed without required table '{$requiredTable}'."
		);
	}
}

putenv('BADGEAPP_TEST_SCHEMA_LOADED=1');

preg_match('/BadgeDB-v([0-9.]+)-schema\.sql$/i', basename($schemaFile), $versionMatch);
echo 'Loaded BadgeDB schema v' . ($versionMatch[1] ?? 'unknown')
	. ' into the configured test database.' . PHP_EOL;
