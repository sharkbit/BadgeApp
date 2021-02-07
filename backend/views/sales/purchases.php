<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\ActiveForm;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\CardReceiptSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Purchases';
$this->params['breadcrumbs'][] = ['label' => 'Store', 'url' => ['index']];

if (isset($_REQUEST['CardReceiptSearch']['pagesize'])) { 
	$pagesize = $_REQUEST['CardReceiptSearch']['pagesize']; 
	$_SESSION['pagesize'] = $_REQUEST['CardReceiptSearch']['pagesize'];
} elseif (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=20;
}
$dataProvider->pagination = ['pageSize' => $pagesize];

echo $this->render('_view-tab-menu').PHP_EOL ?>
<style>
.vertical-center {
  margin: 0;
  position: absolute;
  top: 50%;
  -ms-transform: translateY(-50%);
  transform: translateY(-50%);
}
</style>
<p> </p>

<div class="row">

    <div class="col-xs-12"> <?php
	$gridColumns = [
		[
			'attribute'=>'badge_number',
			'contentOptions' =>['style' => 'width:100px'],
			'format' => 'raw',
			'value'=>function ($data) {
				return str_pad($data->badge_number, 5, '0', STR_PAD_LEFT); 
			}
		],
		'name',
		'tx_date',
		[
			'attribute'=>'cart',
			'format' => 'raw',
			'value' => function($model, $attribute) {
				$items='';
				$dcart = json_decode($model->cart);
				if(is_array($dcart)){
				foreach($dcart as &$item ) {
					if(isset($item) && isset($item->item)){
					$items .=$item->item.' ['.$item->ea.' x '.$item->qty.'] '.($item->price)."<br/>\n";
					}
				}}
				return $items;
			},
			'footer'=>'Total:',
		],
		[
			'attribute'=>'amount',
			'footer' => "$".number_format($dataProvider->query->sum('amount'), 2, ',', ','),
		],
		[ 
			'attribute'=>'cashier',
			//'captionOptions' => ['tooltip' => 'test test',]
		],
		'tx_type',
		[
			'header' => 'Actions',
			'class' => 'yii\grid\ActionColumn',
			'template'=>' {print} ',
			'buttons'=>[
				'print' => function($url,$model) {
					return  Html::a(' <span class="glyphicon glyphicon-print"></span> ', ['print-rcpt','x_id'=>$model->id,'badge_number'=>$model->badge_number], [
						'target'=>'_blank',
						'data-toggle'=>'tooltip',
						'data-placement'=>'top',
						'title'=>'Print',
					]);
				}
			]
		]
	];
?>
		
<div class="sales-search">
<div class="row">
	<div class="col-xs-6 col-sm-4">
		<h1><?= Html::encode($this->title) ?></h1>
	</div>

	<?php $form = ActiveForm::begin([
		'action' => ['purchases'],
		'method' => 'get',
		'id'=>'viewSalsetFilter',
	]); ?>
	<div class="col-xs-6 col-sm-3">
		Use a comma (,) to seperate miultiple Cashiers
		<?php //= $form->field($model, 'atRange_condition')->dropDownlist(['all'=>'All','atRange'=>'At Range','gone'=>'Past Visitors'],['value'=>$model->atRange_condition !=null ? $model->atRange_condition : 'atRange'])->label('Fliter by') ?>
	</div>
	<div class="col-xs-2 col-sm-1" > <p> <br /></p>
		<?=ExportMenu::widget([
			'dataProvider' => $dataProvider,
			'filterModel' => $searchModel,
			'columns' => $gridColumns,
			'fontAwesome' => true,
			'batchSize' => 10,
			'filename'=>  $this->title,
			'target' => '_blank',
			'folder' => '@webroot/export', // this is default save folder on server
		]) . "<br /> <br />\n";?>
	</div>
	<div class="col-xs-3 col-sm-2">
		<?= $form->field($searchModel, 'pagesize')->dropDownlist([ 20 => 20, 50 => 50, 100 => 100, 200=>200],['value'=>$pagesize ,'id' => 'pagesize'])->label('Page size: ') ?>
	</div>
	<div class="col-xs-6 col-sm-2">
		<div class=" form-group btn-group ">
		<br>
			<?= Html::submitButton('<i class="fa fa-search" aria-hidden="true"></i> Search', ['class' => 'btn btn-primary']) ?>
			<?= Html::a('<i class="fa fa-eraser" aria-hidden="true"></i> Reset', ['purchases?reset=true'],['class' => 'btn btn-danger']) ?>
		</div>
	</div>

	<?php ActiveForm::end(); ?>
</div>
</div>
		
		<?php
	
	echo GridView::widget([
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
	'showFooter' => ($searchModel->tx_date !='' ? true:false),
	'pager' => [
			'firstPageLabel' => 'First',
			'lastPageLabel'  => 'Last'
		],
	'columns' => $gridColumns,
        
    ]); ?>
	
    </div>
</div>
<div class="badges-index">

</div>
<script>
$("#w0-cols").hide();
</script>
