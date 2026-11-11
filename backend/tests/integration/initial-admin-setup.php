<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

$root = dirname(__DIR__, 3);
require $root . '/vendor/autoload.php';
require $root . '/vendor/yiisoft/yii2/Yii.php';
require $root . '/common/config/bootstrap.php';
Yii::setAlias('@backend', $root . '/backend');

$config = require $root . '/common/config/test-local.php';
$dbConfig = $config['components']['db'];
preg_match('/dbname=([^;]+)/i', $dbConfig['dsn'], $databaseMatch);
$databaseName = $databaseMatch[1] ?? '';

if ($databaseName === '' || stripos($databaseName, 'test') === false) {
	fwrite(STDERR, "Refusing to run: configured database name must contain 'test'.\n");
	exit(1);
}

$app = new yii\console\Application([
	'id' => 'badgeapp-integration-tests',
	'basePath' => $root,
	'vendorPath' => $root . '/vendor',
	'components' => [
		'db' => $dbConfig,
		'security' => [
			'class' => yii\base\Security::class,
		],
	],
]);

use backend\models\InitialAdminForm;
use common\models\User;

$passed = 0;
$createdUserId = null;
$assert = static function ($condition, $message) use (&$passed) {
	if (!$condition) {
		throw new RuntimeException($message);
	}

	$passed++;
	echo "PASS: {$message}\n";
};

try {
	if (!InitialAdminForm::isSetupRequired()) {
		throw new RuntimeException(
			'Refusing to run: a root account already exists in the configured test database.'
		);
	}

	$assert(
		InitialAdminForm::isSetupRequired(),
		'Setup is required when the database has no root user.'
	);

	$invalidForm = new InitialAdminForm([
		'username' => 'test_invalid_' . bin2hex(random_bytes(4)),
		'email' => 'invalid@example.invalid',
		'full_name' => 'Invalid Test User',
		'password' => 'short',
		'confirmPassword' => 'short',
	]);
	$assert(
		$invalidForm->createFirstAdmin() === null && $invalidForm->hasErrors('password'),
		'An invalid password prevents account creation.'
	);
	$assert(
		InitialAdminForm::isSetupRequired(),
		'Invalid input does not create a root user.'
	);

	$suffix = bin2hex(random_bytes(6));
	$form = new InitialAdminForm([
		'username' => 'test_root_' . $suffix,
		'email' => 'test-root-' . $suffix . '@example.invalid',
		'full_name' => 'Initial Admin Integration Test',
		'password' => 'TestPassword-' . $suffix,
		'confirmPassword' => 'TestPassword-' . $suffix,
	]);
	$user = $form->createFirstAdmin();
	$assert($user instanceof User, 'Valid input creates the initial administrator.');
	$createdUserId = $user->id;
	$assert((int)$user->status === User::STATUS_ACTIVE, 'The created administrator is active.');
	$assert(
		json_decode($user->privilege, true) === [1],
		'The created administrator receives only root privilege.'
	);
	$assert(
		$user->validatePassword($form->password),
		'The created administrator password is correctly hashed.'
	);
	$assert(
		!empty($user->auth_key),
		'The created administrator has an authentication key.'
	);
	$assert(
		!InitialAdminForm::isSetupRequired(),
		'Setup is no longer required after a root user exists.'
	);

	$secondForm = new InitialAdminForm([
		'username' => 'test_second_' . $suffix,
		'email' => 'test-second-' . $suffix . '@example.invalid',
		'full_name' => 'Second Test User',
		'password' => 'SecondPassword-' . $suffix,
		'confirmPassword' => 'SecondPassword-' . $suffix,
	]);
	$assert(
		$secondForm->createFirstAdmin() === null,
		'A second initial administrator cannot be created.'
	);
} finally {
	if ($createdUserId !== null) {
		User::deleteAll(['id' => $createdUserId]);
	}
}

$assert(
	InitialAdminForm::isSetupRequired(),
	'Cleanup removes only the account created by this test.'
);

echo "Passed {$passed} integration assertions.\n";
