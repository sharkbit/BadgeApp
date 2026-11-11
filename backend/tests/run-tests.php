<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

$root = dirname(__DIR__, 2);
$suites = [
	'unit' => [
		['php', $root . '/backend/tests/unit/badges-create-view.php'],
	],
	'integration' => [
		['php', $root . '/backend/tests/integration/initial-admin-setup.php'],
		['php', $root . '/backend/tests/integration/badges-update-view.php'],
	],
	'js' => [
		['node', $root . '/backend/tests/js/badge-form-logic.test.js'],
	],
];
$suites['all'] = array_merge($suites['unit'], $suites['integration'], $suites['js']);

$suite = $argv[1] ?? 'all';
if (!isset($suites[$suite])) {
	fwrite(STDERR, 'Usage: php backend/tests/run-tests.php [all|unit|integration|js]' . PHP_EOL);
	exit(2);
}

if ($suite !== 'js') {
	try {
		require $root . '/backend/tests/_schema-bootstrap.php';
	} catch (Throwable $exception) {
		fwrite(STDERR, 'Test schema setup failed: ' . $exception->getMessage() . PHP_EOL);
		exit(1);
	}
}

$environment = getenv();
$environment['BADGEAPP_TEST_SCHEMA_LOADED'] = $suite === 'js' ? '0' : '1';
$failed = false;

foreach ($suites[$suite] as [$runtime, $testFile]) {
	if ($runtime === 'php') {
		$command = [PHP_BINARY, '-d', 'extension=pdo_mysql', $testFile];
	} else {
		$command = ['node', $testFile];
	}

	echo PHP_EOL . 'Running ' . basename($testFile) . PHP_EOL;
	$process = proc_open(
		$command,
		[
			0 => STDIN,
			1 => STDOUT,
			2 => STDERR,
		],
		$pipes,
		$root,
		$environment,
		['bypass_shell' => true]
	);

	if (!is_resource($process)) {
		fwrite(STDERR, "Could not start test process.\n");
		$failed = true;
		continue;
	}

	$exitCode = proc_close($process);
	if ($exitCode !== 0) {
		fwrite(STDERR, basename($testFile) . " failed with exit code {$exitCode}.\n");
		$failed = true;
	}
}

exit($failed ? 1 : 0);
