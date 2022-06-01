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
		<div class="col-xs-0 col-sm-0 col-md-0 col-lg-2" id='export'>
	  <!--	  <?=html::a('<i class="fa fa-download" aria-hidden="true"></i> Export as CSV',['#'],['id'=>'customExportCsv','class'=>'btn btn-primary'])?> -->
		</div>
		<div class="col-xs-12 col-sm-2 col-md-1 col-lg-1"><br />
		<?=$form->field($searchModel, 'groupby')->checkbox(['checked'=>($searchModel->groupby)?true:false,'title'=>'Group By Catagory -> Type -> Sku']);?>
		</div>
		<div class="col-xs-6 col-sm-5 col-md-3 col-lg-3">
		<?=$form->field($searchModel, 'date_start', [ 'options'=>['class'=>'drp-container form-group'] ])
			->widget(DateTimePicker::classname(), [ ]); ?>
		</div>
		<div class="col-xs-6 col-sm-5 col-md-3 col-lg-3">
			<?=$form->field($searchModel, 'date_stop', [ 'options'=>['class'=>'drp-container form-group'] ])
			->widget(DateTimePicker::classname(), [ ]); ?>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-2 col-lg-1">
			<?= $form->field($searchModel, 'pagesize')->dropDownlist([ 20 => 20, 50 => 50, 100 => 100, 200=>200],['value'=>$pagesize ,'id' => 'pagesize'])->label('Page size: ') ?>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-3 col-lg-2"><br />
			<?= Html::submitButton('<i class="fa fa-search" aria-hidden="true"></i> Search ', ['class' => 'btn btn-primary']) ?>
			<?= Html::a('<i class="fa fa-eraser"></i> Reset', ['/sales/summary?reset=true'],['class' => 'btn btn-danger']) ?>
		</div>
	</div>
</div>

<?php
		$gridColumns = [
			[	'attribute' => 'tx_date',
				'visible' => ($searchModel->groupby) ? false : true,
			],
			'cat',
			[	'attribute' => 'tx_type',
				'filter' => Html::dropDownList('tx_type', $searchModel->tx_type, ['cash'=>'Cash','check'=>'Check','creditnow'=>'Credit','online'=>'Online','other'=>'Other'],['id'=>'txsle2','class'=>'select2', 'multiple'=>true]),
			],
			'csku',
			'citem',
			'sqty',
			[	'attribute' => 'sprice',
				'format' => ['decimal', 2],
				'footer' => ($searchModel->groupby)?"$".number_format($dataProvider->query->sum('sprice'), 2, '.', ','):"$".number_format($dataProvider->query->sum('cprice'), 2, '.', ','),
			],
		];?>
	<div class="row">
		<div class="col-sm-6 pull-right">
		Export Data -
		<?=ExportMenu::widget([
			'dataProvider' => $dataProvider,
			'columns' => $gridColumns,
			'fontAwesome' => true,
			'batchSize' => 0,
			'filename'=>  $this->title,
			'target' => '_blank',
			'folder' => '@webroot/export', // this is default save folder on server
			'exportConfig' => [
				ExportMenu::FORMAT_HTML => false,
				ExportMenu::FORMAT_EXCEL => false,
				ExportMenu::FORMAT_EXCEL_X => false,
				//ExportMenu::FORMAT_PDF => false
			]
		]) . "<hr>\n";?>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
		<?php
		echo GridView::widget([
			'dataProvider' => $dataProvider,
			'columns' => $gridColumns,
			'filterModel' => $searchModel,
			'showFooter' => true,
		]);
			?>
		</div>
	</div>

<?php ActiveForm::end(); ?>
<script>
 $("#txsle2").select2({placeholder_text_multiple:'Select',width: "100%"});
</script>
