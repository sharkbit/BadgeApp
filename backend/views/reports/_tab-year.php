<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\models\Params;

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
$selectedYear = ($selectedYear !== false && $selectedYear >= $firstYear && $selectedYear <= $throughYear)
	? $selectedYear
	: null;
$reportRoute = '/reports/' . Yii::$app->controller->action->id;
$studentParams = !empty($student) ? ['student' => 1] : [];
?>
<ul class="nav nav-tabs">
	<li class="<?= $selectedYear === null ? 'active' : '' ?>">
		<?= Html::a('All', Url::to(array_merge([$reportRoute, 'year' => 'all'], $studentParams))) ?>
	</li>
	<?php for ($year = $firstYear; $year <= $throughYear; $year++) { ?>
		<li class="<?= $year === $selectedYear ? 'active' : '' ?>">
			<?= Html::a((string)$year, Url::to(array_merge([$reportRoute, 'year' => $year], $studentParams))) ?>
		</li>
	<?php } ?>
</ul>
