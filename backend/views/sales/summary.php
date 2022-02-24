<?php

use kartik\datetime\DateTimePicker;
use kartik\export\ExportMenu;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Sales Summary';
$this->params['breadcrumbs'][] = ['label' => 'store', 'url' => ['/sales']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/payment/inventory']];

if (isset($_REQUEST['SalesSummary']['pagesize'])) {
	$pagesize = $_REQUEST['SalesSummary']['pagesize'];
	$_SESSION['pagesize'] = $_REQUEST['SalesSummary']['pagesize'];
} elseif (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=200;
}
$dataProvider->pagination = ['pageSize' => $pagesize];

echo $this->render('_view-tab-menu').PHP_EOL;
?>

<h2><?= Html::encode($this->title) ?></h2>
<div class="clubs-index" ng-controller="SalesReportForm">
<?php $form = ActiveForm::begin([
				'id'=>'SalesReportForm',
				'method' => 'get',
			]); ?>
	<div class="row">
		 <div class="col-xs-4" id='export'>
	  <!--	  <?=html::a('<i class="fa fa-download" aria-hidden="true"></i> Export as CSV',['#'],['id'=>'customExportCsv','class'=>'btn btn-primary'])?> -->
		</div>

		<div class="col-xs-3">
		<?=$form->field($searchModel, 'date_start', [ 'options'=>['class'=>'drp-container form-group'] ])
			->widget(DateTimePicker::classname(), [ ]); ?>
		</div>
		<div class="col-xs-3">
			<?=$form->field($searchModel, 'date_stop', [ 'options'=>['class'=>'drp-container form-group'] ])
			->widget(DateTimePicker::classname(), [ ]); ?>
		</div>

		<div class="col-xs-2"><br />
			<?= Html::submitButton('<i class="fa fa-search" aria-hidden="true"></i> Search ', ['class' => 'btn btn-primary']) ?>
			<?= Html::a('<i class="fa fa-eraser"></i> Reset', ['/sales/summary?reset=true'],['class' => 'btn btn-danger']) ?>
		</div>
	</div>
<?php ActiveForm::end(); ?>
</div>

 <?php
 echo GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			'cat',
			[	'attribute' => 'tx_type',
				'value' => function ($model, $key, $index, $widget) {
					return $model->tx_type;
				},
				// 'filterType' => GridView::FILTER_SELECT2,
				// 'filter' => ArrayHelper::map(Categories::find()->orderBy('category_name')->asArray()->all(), 'id', 'category_name'),
			],
			'csku',
			'citem',
			'sqty',
			[	'attribute' => 'sprice',
				'format' => ['decimal', 2],
			],
		],
	]);
?>
