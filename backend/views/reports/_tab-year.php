<?php

use yii\helpers\Html;
use yii\helpers\Url;

$startYear = 2017;
$currentYear = (int)date('Y');
$yearParam = Yii::$app->request->get('year', $currentYear);
$selectedYear = $yearParam === 'all' ? null : filter_var($yearParam, FILTER_VALIDATE_INT);
$selectedYear = ($selectedYear !== false && $selectedYear >= $startYear && $selectedYear <= $currentYear)
	? $selectedYear
	: null;

?>
<ul class="nav nav-tabs">
	<li class="<?= $selectedYear === null ? 'active' : '' ?>">
		<?= Html::a('All', Url::to(['/reports/index', 'year' => 'all'])) ?>
	</li>
	<?php for ($year = $startYear; $year <= $currentYear; $year++) { ?>
		<li class="<?= $year === $selectedYear ? 'active' : '' ?>">
			<?= Html::a((string)$year, Url::to(['/reports/index', 'year' => $year])) ?>
		</li>
	<?php } ?>
</ul>
