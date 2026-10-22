<?php

use kartik\export\ExportMenu;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\SqlDataProvider */

$this->title = $chartMeta->title;
$this->params['breadcrumbs'][] = ['label'=>'Reports', 'url'=>['/reports']];
$this->params['breadcrumbs'][] = ['label'=>$chartMeta->title];

$reports = $dataProvider->getModels();
$months = [];
$types = [];
$counts = [];

foreach ($reports as $report) {
	$month = (string)$report[$chartMeta->x_axis_field];
	$type = (string)$report[$chartMeta->y_axis_field];
	if ($month === '' || $type === '') {
		continue;
	}

	$months[$month] = true;
	$types[$type] = true;
	$counts[$type][$month] = ($counts[$type][$month] ?? 0) + (int)$report['item_count'];
}

$labels = array_keys($months);
sort($labels, SORT_STRING);
$datasetTypes = array_keys($types);
sort($datasetTypes, SORT_NATURAL | SORT_FLAG_CASE);

$colors = [
	['background' => 'rgba(75, 192, 192, 0.7)', 'border' => 'rgba(75, 192, 192, 1)'],
	['background' => 'rgba(54, 162, 235, 0.7)', 'border' => 'rgba(54, 162, 235, 1)'],
	['background' => 'rgba(255, 99, 132, 0.7)', 'border' => 'rgba(255, 99, 132, 1)'],
	['background' => 'rgba(153, 102, 255, 0.7)', 'border' => 'rgba(153, 102, 255, 1)'],
	['background' => 'rgba(255, 206, 86, 0.7)', 'border' => 'rgba(255, 206, 86, 1)'],
	['background' => 'rgba(255, 159, 64, 0.7)', 'border' => 'rgba(255, 159, 64, 1)'],
	['background' => 'rgba(201, 203, 207, 0.7)', 'border' => 'rgba(201, 203, 207, 1)'],
	['background' => 'rgba(46, 204, 113, 0.7)', 'border' => 'rgba(46, 204, 113, 1)'],
	['background' => 'rgba(26, 188, 156, 0.7)', 'border' => 'rgba(26, 188, 156, 1)'],
	['background' => 'rgba(52, 152, 219, 0.7)', 'border' => 'rgba(52, 152, 219, 1)'],
	['background' => 'rgba(142, 68, 173, 0.7)', 'border' => 'rgba(142, 68, 173, 1)'],
	['background' => 'rgba(241, 196, 15, 0.7)', 'border' => 'rgba(241, 196, 15, 1)'],
	['background' => 'rgba(230, 126, 34, 0.7)', 'border' => 'rgba(230, 126, 34, 1)'],
	['background' => 'rgba(231, 76, 60, 0.7)', 'border' => 'rgba(231, 76, 60, 1)'],
	['background' => 'rgba(52, 73, 94, 0.7)', 'border' => 'rgba(52, 73, 94, 1)'],
];

$datasets = [];
foreach ($datasetTypes as $index => $type) {
	$color = $colors[$index % count($colors)];
	$datasets[] = [
		'label' => $type,
		'data' => array_map(function ($month) use ($counts, $type) {
			return $counts[$type][$month] ?? 0;
		}, $labels),
		'backgroundColor' => $color['background'],
		'borderColor' => $color['border'],
		'borderWidth' => 1,
	];
}

$hasY2Axis = !empty($chartMeta->y2_axis);
if ($hasY2Axis) {
	$totalChartData = [];
	foreach ($datasetTypes as $index => $type) {
		$total = 0;
		foreach ($labels as $month) {
			$total += $counts[$type][$month] ?? 0;
		}
		$totalChartData[] = [
			'label' => $type,
			'value' => $total,
			'backgroundColor' => $colors[$index % count($colors)]['background'],
			'borderColor' => $colors[$index % count($colors)]['border'],
		];
	}

	foreach ($datasetTypes as $index => $type) {
		$cumulativeTotal = 0;
		$cumulativeTotals = [];
		foreach ($labels as $month) {
			$cumulativeTotal += $counts[$type][$month] ?? 0;
			$cumulativeTotals[] = $cumulativeTotal;
		}

		$color = $colors[$index % count($colors)];
		$datasets[] = [
			'type' => 'line',
			'label' => $type . ' Total',
			'data' => $cumulativeTotals,
			'yAxisID' => 'y2',
			'borderColor' => $color['border'],
			'backgroundColor' => $color['border'],
			'borderWidth' => 2,
			'pointRadius' => 3,
			'tension' => 0.2,
			'fill' => false,
		];
	}

	$cumulativeTotal = 0;
	$cumulativeTotals = [];
	foreach ($labels as $month) {
		$monthTotal = 0;
		foreach ($datasetTypes as $type) {
			$monthTotal += $counts[$type][$month] ?? 0;
		}
		$cumulativeTotal += $monthTotal;
		$cumulativeTotals[] = $cumulativeTotal;
	}

	$datasets[] = [
		'type' => 'line',
		'label' => $chartMeta->y2_axis,
		'data' => $cumulativeTotals,
		'yAxisID' => 'y2',
		'borderColor' => 'rgba(75, 75, 75, 1)',
		'backgroundColor' => 'rgba(75, 75, 75, 1)',
		'borderWidth' => 2,
		'pointRadius' => 3,
		'tension' => 0.2,
		'fill' => false,
	];
}

$this->registerJsFile(
	'https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js',
	['position' => View::POS_HEAD]
);
$this->registerCss(
	'.chartjs-custom-tooltip { position: absolute; z-index: 10; pointer-events: none; '
	. 'background: #fff; border: 1px solid #bbb; border-radius: 4px; padding: 8px 10px; '
	. 'box-shadow: 0 2px 8px rgba(0, 0, 0, .2); transform: translate(-50%, -100%); white-space: nowrap; }'
	. '.chartjs-custom-tooltip[aria-hidden="true"] { display: none; }'
	. '.chartjs-custom-tooltip-title { font-weight: bold; margin-bottom: 5px; }'
	. '.chartjs-custom-tooltip-row { display: flex; align-items: center; gap: 8px; }'
	. '.chartjs-custom-tooltip-swatch { width: 10px; height: 10px; border: 1px solid #777; }'
	. '.chartjs-custom-tooltip-total { border-top: 1px solid #ddd; margin-top: 5px; padding-top: 5px; font-weight: bold; }'
);
$this->registerJs(
	'const chartCanvas = document.getElementById("txChart");'
	. 'const externalTooltipHandler = function (context) {'
	. 'const chart = context.chart;'
	. 'const tooltip = context.tooltip;'
	. 'const tooltipElement = chart.canvas.parentNode.querySelector(".chartjs-custom-tooltip");'
	. 'if (!tooltipElement) { return; }'
	. 'if (tooltip.opacity === 0 || !tooltip.dataPoints.length) {'
	. 'tooltipElement.setAttribute("aria-hidden", "true");'
	. 'return;'
	. '}'
	. 'const dataIndex = tooltip.dataPoints[0].dataIndex;'
	. 'tooltipElement.replaceChildren();'
	. 'const title = document.createElement("div");'
	. 'title.className = "chartjs-custom-tooltip-title";'
	. 'title.textContent = chart.data.labels[dataIndex];'
	. 'tooltipElement.appendChild(title);'
	. 'let sum = 0;'
	. 'chart.data.datasets.forEach(function (dataset) {'
	. 'const value = Number(dataset.data[dataIndex]) || 0;'
	. 'if (dataset.yAxisID !== "y2") { sum += value; }'
	. 'const row = document.createElement("div");'
	. 'row.className = "chartjs-custom-tooltip-row";'
	. 'const swatch = document.createElement("span");'
	. 'swatch.className = "chartjs-custom-tooltip-swatch";'
	. 'swatch.style.backgroundColor = Array.isArray(dataset.backgroundColor) ? dataset.backgroundColor[dataIndex] : dataset.backgroundColor;'
	. 'row.appendChild(swatch);'
	. 'const label = document.createElement("span");'
	. 'label.textContent = dataset.label;'
	. 'row.appendChild(label);'
	. 'const amount = document.createElement("strong");'
	. 'amount.textContent = String(value);'
	. 'row.appendChild(amount);'
	. 'tooltipElement.appendChild(row);'
	. '});'
	. 'const total = document.createElement("div");'
	. 'total.className = "chartjs-custom-tooltip-total";'
	. 'total.textContent = "Sum: " + sum;'
	. 'tooltipElement.appendChild(total);'
	. 'tooltipElement.style.left = (chart.canvas.offsetLeft + tooltip.caretX) + "px";'
	. 'tooltipElement.style.top = (chart.canvas.offsetTop + tooltip.caretY) + "px";'
	. 'tooltipElement.setAttribute("aria-hidden", "false");'
	. '};'
	. 'if (chartCanvas) {'
	. 'new Chart(chartCanvas, {'
	. 'type: "bar",'
	. 'data: {labels: ' . Json::htmlEncode($labels) . ', datasets: ' . Json::htmlEncode($datasets) . '},'
	. 'options: {responsive: true, maintainAspectRatio: false,'
	. 'plugins: {'
	. 'title: {display: true, text: ' . Json::htmlEncode($chartMeta->title) . ', font: {size: 24}},'
	. 'legend: {position: "top"},'
	. 'tooltip: {enabled: false, external: externalTooltipHandler}'
	. '},'
	. 'scales: {'
	. 'x: {stacked: true, title: {display: true, text: ' . Json::htmlEncode($chartMeta->x_axis_title) . '}},'
	. 'y: {stacked: true, beginAtZero: true, title: {display: true, text: ' . Json::htmlEncode($chartMeta->y_axis_title) . '}, ticks: {precision: 0}}'
	. ($hasY2Axis
		? ',y2: {type: "linear", position: "right", beginAtZero: true, title: {display: true, text: ' . Json::htmlEncode($chartMeta->y2_axis) . '}, grid: {drawOnChartArea: false}, ticks: {precision: 0}}'
		: '')
	. '}'
		. '}'
	. '});'
	. '}',
	View::POS_READY
);
if ($hasY2Axis) {
	$this->registerJs(
		'const totalsCanvas = document.getElementById("totalsChart");'
		. 'if (totalsCanvas) {'
		. 'const totals = ' . Json::htmlEncode($totalChartData) . ';'
		. 'new Chart(totalsCanvas, {'
		. 'type: "doughnut",'
		. 'data: {'
		. 'labels: totals.map(function (item) { return item.label; }),'
		. 'datasets: [{'
		. 'data: totals.map(function (item) { return item.value; }),'
		. 'backgroundColor: totals.map(function (item) { return item.backgroundColor; }),'
		. 'borderColor: totals.map(function (item) { return item.borderColor; }),'
		. 'borderWidth: 1'
		. '}]'
		. '},'
		. 'options: {responsive: true, maintainAspectRatio: false,'
		. 'plugins: {'
		. 'title: {display: true, text: ' . Json::htmlEncode($chartMeta->title . ' Totals') . ', font: {size: 24}},'
		. 'legend: {position: "top"},'
		. 'tooltip: {callbacks: {label: function (context) {'
		. 'const values = context.dataset.data;'
		. 'const total = values.reduce(function (sum, value) { return sum + Number(value || 0); }, 0);'
		. 'const value = Number(context.raw) || 0;'
		. 'const percentage = total > 0 ? (value / total * 100).toFixed(1) : "0.0";'
		. 'return context.label + ": " + value + " (" + percentage + "%)";'
		. '}}}'
		. '}'
		. '}'
		. '});'
		. '}',
		View::POS_READY
	);
}

?>
<?= $this->render('_tab-year', [
	'firstYear' => $chartMeta->firstYear,
	'student' => !empty($chartMeta->student),
]) ?>

<div class="row">
	<div class="col-xs-6 col-sm-4">
		<div class="reports-export">
	<?= ExportMenu::widget([
		'dataProvider' => $dataProvider,
		'columns' => [
			[
				'attribute' => $chartMeta->x_axis_field,
				'label' => $chartMeta->x_axis_title,
			],
			[
				'attribute' => $chartMeta->y_axis_field,
				'label' => ucfirst(str_replace('_', ' ', $chartMeta->y_axis_field)),
			],
			[
				'attribute' => 'item_count',
				'label' => $chartMeta->y_axis_title,
			],
		],
		'fontAwesome' => true,
		'batchSize' => 0,
		'clearBuffers' => true,
		'filename' => $this->title,
		'target' => '_blank',
		'exportConfig' => [
			ExportMenu::FORMAT_HTML => false,
			ExportMenu::FORMAT_EXCEL => false,
			ExportMenu::FORMAT_EXCEL_X => false,
			ExportMenu::FORMAT_PDF => false
		],
	]) ?>
		</div>
	</div>
	<div class="col-xs-4 col-sm-4">
		<?php if (isset($chartMeta->student)) { ?>
			<?= Html::beginForm(['/reports/' . Yii::$app->controller->action->id], 'get') ?>
			<?= Html::hiddenInput('year', Yii::$app->request->get('year', date('Y'))) ?>
			<div class="text-center">
				<?= Html::checkbox('student', (bool)$chartMeta->student, [
					'value' => 1,
					'label' => 'Only events with attendees',
				]) ?>
				<?= Html::submitButton('Update Chart', ['class' => 'btn btn-primary']) ?>
			</div>
			<?= Html::endForm() ?>
		<?php } ?>
	</div>
	<div class="col-xs-4 col-sm-4 text-right">
<p>Check out all the <a href="https://www.chartjs.org/docs/latest/samples/information.html" target="charts">Charts </a></p>
	</div>
</div>

<div class="reports-index">
	<!-- <h1><?= Html::encode($this->title ?: 'Reports') ?></h1> -->
	<div class="chart-container" style="position: relative; width: 100%; min-height: 420px;">
		<div class="chartjs-custom-tooltip" aria-hidden="true"></div>
		<canvas id="txChart" role="img" aria-label="<?= Html::encode($chartMeta->title) ?>"></canvas>
	</div>
	<?php if (empty($reports)) { ?>
		<p>No report data is available.</p>
	<?php } ?>
	<?php if ($hasY2Axis) { ?>
		<div class="chart-container" style="position: relative; width: 100%; min-height: 420px; margin-top: 24px;">
			<canvas id="totalsChart" role="img" aria-label="<?= Html::encode($chartMeta->title . ' Totals') ?>"></canvas>
		</div>
	<?php } ?>
</div>
