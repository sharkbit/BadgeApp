<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

$root = dirname(__DIR__, 3);
require $root . '/vendor/autoload.php';
require $root . '/vendor/yiisoft/yii2/Yii.php';

class BadgesCreateViewProbe {
	public $title;
	public $params = ['breadcrumbs' => []];
	public $renderCalls = [];

	public function render($view, $params = []) {
		$this->renderCalls[] = [$view, $params];
		return '<form id="badgeCreate"></form>';
	}

	public function renderCreateView($file, $model, $confParams) {
		ob_start();
		include $file;
		return ob_get_clean();
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

$view = new BadgesCreateViewProbe();
$model = new stdClass();
$confParams = ['sell_date' => '06-01'];
$file = $root . '/backend/views/badges/create.php';
$output = $view->renderCreateView($file, $model, $confParams);

$assert(
	$view->title === 'Issue New Range Badge',
	'The page sets the expected title.'
);
$assert(
	$view->params['breadcrumbs'] === [
		['label' => 'Range Badges', 'url' => ['index']],
		'Issue New Range Badge',
	],
	'The page adds the index and current-page breadcrumbs.'
);
$assert(
	str_contains($output, 'class="badges-create"')
		&& str_contains($output, 'ng-controller="CreateBadgeController"'),
	'The page renders its wrapper and create controller.'
);
$assert(
	str_contains($output, '<h2>Issue New Range Badge</h2>'),
	'The page renders the escaped title as its heading.'
);
$assert(
	str_contains($output, '<form id="badgeCreate"></form>'),
	'The page includes the badge form output.'
);
$assert(
	count($view->renderCalls) === 1 && $view->renderCalls[0][0] === '_form',
	'The page renders the _form partial exactly once.'
);
$assert(
	$view->renderCalls[0][1]['model'] === $model,
	'The page passes the badge model unchanged to _form.'
);
$assert(
	$view->renderCalls[0][1]['confParams'] === $confParams,
	'The page passes configuration parameters unchanged to _form.'
);

echo "Passed {$passed} badge create view assertions.\n";
