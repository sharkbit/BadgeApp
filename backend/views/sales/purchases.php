<?php

use kartik\daterange\DateRangePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\ActiveForm;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\CardReceiptSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Purchases';
$this->params['breadcrumbs'][] = ['label' => 'Store', 'url' => ['index']];

if (isset($_REQUEST['sales-showsku'])) {$showsku= true;} else {$showsku= false;}
if (isset($_REQUEST['sales-show_club'])) {$show_club= true; $searchModel->show_club=true;} else {$show_club= false;}
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
    <div class="col-xs-12">
	<?php
	$gridColumns = [
		[	'attribute'=>'badge_number',
			'contentOptions' =>['style' => 'width:80px'],
			'format' => 'raw',
			'value'=>function ($data) {
				return str_pad($data->badge_number, 5, '0', STR_PAD_LEFT);
			}
		],
		'name',
		[	'attribute'=>'ClubNames',
			'visible' => ($searchModel->show_club) ? true : false,
			'format' => 'raw',
			'contentOptions' =>['style' => 'width:100px'],
		],
		'tx_date',
		[	'attribute'=>'cart',
			'format' => 'raw',
			'value' => function($model, $attribute) use ($showsku) {
				$items='';
				$dcart = json_decode($model->cart);
				if(is_array($dcart)){
				foreach($dcart as &$item ) {
					if(isset($item) && isset($item->item)){
					if($showsku) {$items .=$item->sku.' - ';}
					$items .=$item->item.' ['.$item->ea.' x '.$item->qty.'] '.($item->price)."<br/>\n";
					}
				}}
				return $items;
			},
			'footer'=>'Total:',
		],
		[	'attribute'=>'New / Renew',
			'value' => function($model) {
				return @$model->badge_subscriptions->transaction_type;
			},
		],
		[	'attribute'=>'amount',
			'headerOptions' => ['width' => '100'],
			'footer' => "$".number_format($dataProvider->query->sum('amount'), 2, '.', ','),
		],
	//	[	'attribute'=>'badges',
	//		'value'=>function($model) {
	//			return @$model->badges->first_name.' '.@$model->badges->last_name;}
	//	],
		[ 	'attribute'=>'cashier_badge',
			'value' => function($model, $attribute) { return yii::$app->controller->decodeBadgeName((int)$model->cashier_badge); },
			'label' => 'Cashier (Use commas to seperate multiple Cashiers when filtering.',
			'headerOptions' => ['width' => '300'],
		],
		'tx_type',
		[	'header' => 'Actions',
			'class' => 'yii\grid\ActionColumn',
			'template'=>' {print} {delete}',
			'buttons'=>[
				'print' => function($url,$model) {
					return  Html::a(' <span class="glyphicon glyphicon-print"></span> ', ['print-rcpt','x_id'=>$model->id,'badge_number'=>$model->badge_number], [
						'target'=>'_blank',
						'data-toggle'=>'tooltip',
						'data-placement'=>'top',
						'title'=>'Print',
					]);
				},
				'delete' => function($url,$model) {
					if(yii::$app->controller->hasPermission('sales/delete-sale')) {
					return  Html::a(' <span class="glyphicon glyphicon-trash"></span> ', ['delete-sale','id'=>$model->id,'badge_number'=>$model->badge_number], [
						'data-toggle'=>'tooltip',
						'data-placement'=>'top',
						'title'=>'Delete',
						'data' => [
							'confirm' => 'Are you sure you want to delete '.$model->id.'?',
							'method' => 'post',
						],
					]); }
				},
			]
		]
	];
	?>
<div class="sales-search">
<div class="row">
	<div class="col-xs-6 col-sm-4">
		<h1><?= Html::encode($this->title) ?></h1>
	</div>
</div>

	<?php $form = ActiveForm::begin([
		'action' => ['purchases'],
		'method' => 'get',
		'id'=>'viewSalsetFilter',
	]); ?>
<div class="row">
	<div class="col-xs-6 col-sm-3 col-md-2 col-lg-3 col-xl-3" > <p> <br /></p>
		Export Data -
		<?=ExportMenu::widget([
			'dataProvider' => $dataProvider,
			'filterModel' => $searchModel,
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
		]) . "<br /> <br />\n";?>
	</div>
	<div class="col-xs-6 col-sm-2 col-md-2 col-lg-2 col-xl-3">
<?php echo Html::checkbox('sales-showsku',$showsku,['value'=>1,'id'=>'sales-showsku']), PHP_EOL; ?><b> - Show SKU</b> <br />
<br /> <?php echo Html::checkbox('sales-show_club',$show_club,['value'=>1,'id'=>'sales-show_club']), PHP_EOL; ?><b> - Show Clubs</b>
	</div>
	<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 col-xl-3">
	<?=$form->field($searchModel, 'tx_date', [
		'options'=>['class'=>'drp-container form-group']
		])->widget(DateRangePicker::classname(), [
			'presetDropdown'=>true,
			'hideInput'=>false,
			'pluginOptions' => [
				'opens'=>'left',
				'locale'=>['format'=>'YYYY/MM/DD'],
			]])->label('Date range:'); ?>
	</div>
	<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 col-xl-2">
		<?= $form->field($searchModel, 'pagesize')->dropDownlist([ 20 => 20, 50 => 50, 100 => 100, 200=>200],['value'=>$pagesize ,'id' => 'pagesize'])->label('Page size: ') ?>
	</div>
	<div class="col-xs-6 col-sm-2 col-md-3 col-lg-2 col-xl-0" "btn-group pull-right">
		<div class=" form-group btn-group ">
		<br>
			<?= Html::submitButton('<i class="fa fa-search" aria-hidden="true"></i> Search', ['class' => 'btn btn-primary']) ?>
			<?= Html::a('<i class="fa fa-eraser" aria-hidden="true"></i> Reset', ['purchases?reset=true'],['class' => 'btn btn-danger']) ?>
		</div>
	</div>
</div>
	<?php ActiveForm::end(); ?>
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
