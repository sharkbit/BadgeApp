<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\SqlDataProvider */

$this->title = 'Reports';
$reports = $dataProvider->getModels();
$months = [];
$types = [];
$counts = [];
$title = 'Monthly Sales count by transaction type';
$x_axis = 'Month';
$y_axis = 'Number of Sales';
$y2_axis = 'Cumulative Totals';


foreach ($reports as $report) {
	$month = (string)$report['tx_month'];
	$type = (string)$report['tx_type'];
	if ($month === '' || $type === '') {
		continue;
	}

	$months[$month] = true;
	$types[$type] = true;
	$counts[$type][$month] = ($counts[$type][$month] ?? 0) + (int)$report['item_count'];
}

$labels = array_keys($months);
$datasetTypes = array_keys($types);
sort($datasetTypes, SORT_NATURAL | SORT_FLAG_CASE);

$colors = [
	['background' => 'rgba(54, 162, 235, 0.7)', 'border' => 'rgba(54, 162, 235, 1)'],
	['background' => 'rgba(255, 99, 132, 0.7)', 'border' => 'rgba(255, 99, 132, 1)'],
	['background' => 'rgba(255, 206, 86, 0.7)', 'border' => 'rgba(255, 206, 86, 1)'],
	['background' => 'rgba(75, 192, 192, 0.7)', 'border' => 'rgba(75, 192, 192, 1)'],
	['background' => 'rgba(153, 102, 255, 0.7)', 'border' => 'rgba(153, 102, 255, 1)'],
	['background' => 'rgba(255, 159, 64, 0.7)', 'border' => 'rgba(255, 159, 64, 1)'],
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
	. 'sum += value;'
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
	. 'title: {display: true, text: "'.$title.'"},'
	. 'legend: {position: "top"},'
	. 'tooltip: {enabled: false, external: externalTooltipHandler}'
	. '},'
	. 'scales: {'
	. 'x: {stacked: true, title: {display: true, text: "'.$x_axis.'"}},'
	. 'y: {stacked: true, beginAtZero: true, title: {display: true, text: "'.$y_axis.'"}, ticks: {precision: 0}}'
	. '}'
		. '}'
	. '});'
	. '}',
	View::POS_READY
);


?>

<div class="reports-index">
	<h1><?= Html::encode($this->title ?: 'Reports') ?></h1>
	<div class="chart-container" style="position: relative; width: 100%; min-height: 420px;">
		<div class="chartjs-custom-tooltip" aria-hidden="true"></div>
		<canvas id="txChart" role="img" aria-label="<?=$title?>"></canvas>
	</div>
	<?php if (empty($reports)) { ?>
		<p>No report data is available.</p>
	<?php } ?>
</div>
