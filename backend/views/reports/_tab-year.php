<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\models\Params;

$startYear = 2017;
$currentYear = (int)date('Y');

$params = Params::findOne('1');
$nowDate = date('Y-m-d',strtotime(yii::$app->controller->getNowTime()));
$DateChk = date("Y-".$params['sell_date'], strtotime(yii::$app->controller->getNowTime()));
if ($DateChk <= $nowDate) {
	$throughYear = (int)date('Y', strtotime("+1 years", strtotime($nowDate)));
} else {
	$throughYear = (int)date('Y', strtotime($nowDate));
}

$yearParam = Yii::$app->request->get('year', $currentYear);
$selectedYear = $yearParam === 'all' ? null : filter_var($yearParam, FILTER_VALIDATE_INT);
$selectedYear = ($selectedYear !== false && $selectedYear >= $startYear && $selectedYear <= $throughYear)
	? $selectedYear
	: null;
?>
<ul class="nav nav-tabs">
	<li class="<?= $selectedYear === null ? 'active' : '' ?>">
		<?= Html::a('All', Url::to(['/reports/index', 'year' => 'all'])) ?>
	</li>
	<?php for ($year = $startYear; $year <= $throughYear; $year++) { ?>
		<li class="<?= $year === $selectedYear ? 'active' : '' ?>">
			<?= Html::a((string)$year, Url::to(['/reports/index', 'year' => $year])) ?>
		</li>
	<?php } ?>
</ul>
