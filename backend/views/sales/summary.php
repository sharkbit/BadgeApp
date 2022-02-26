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
<?php $form = ActiveForm::begin([
	'id'=>'SalesReportForm',
	'method' => 'post',
]); ?>
<div class="SalesSummary-form" ng-controller="SalesSummaryForm">
	<div class="row">
		<div class="col-xs-3" id='export'>
	  <!--	  <?=html::a('<i class="fa fa-download" aria-hidden="true"></i> Export as CSV',['#'],['id'=>'customExportCsv','class'=>'btn btn-primary'])?> -->
		</div>
		<div class="col-xs-1"><br />
		<?=$form->field($searchModel, 'groupby')->checkbox(['checked'=>true]);?>
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
</div>

 <?php
 echo GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'showFooter' => true,
		'columns' => [
			'cat',
			[	'attribute' => 'tx_type',
				'filter' => Html::dropDownList('tx_type', $searchModel->tx_type, ['cash'=>'Cash','check'=>'Check','creditnow'=>'Credit','online'=>'Online','other'=>'Other'],['id'=>'txsle2','class'=>'select2', 'multiple'=>true]),
			],
			'csku',
			'citem',
			'sqty',
			[	'attribute' => 'sprice',
				'format' => ['decimal', 2],
				'footer' => "$".number_format($dataProvider->query->sum('sprice'), 2, '.', ','),
			],
		],
	]);
?>
<?php ActiveForm::end(); ?>

<script>
 $("#txsle2").select2({placeholder_text_multiple:'Select',width: "100%"});
</script>