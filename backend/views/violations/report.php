<?php

use backend\models\clubs;
use backend\models\Violations;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\ViolationsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Violations Report';
$this->params['breadcrumbs'][] = ['label' => 'Violations', 'url' => ['violations/index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['violations/report']];

if (isset($_REQUEST['ViolationsSearch']['pagesize'])) { 
	$pagesize = $_REQUEST['ViolationsSearch']['pagesize']; 
	$_SESSION['pagesize'] = $_REQUEST['ViolationsSearch']['pagesize'];
} elseif (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=20;
}
$dataProvider->pagination = ['pageSize' => $pagesize];

echo $this->render('_view-tab-menu').PHP_EOL; ?>
<style>
.vertical-center {
  margin: 0;
  position: absolute;
  top: 50%;
  -ms-transform: translateY(-50%);
  transform: translateY(-50%);
}
</style>
<br />

<div class="row">
<div class="col-xs-12">
	<?php  
	$exportColumns = [
			[	'attribute'=>'vi_date',
				'value' => function($model) {
					return date('Y-m-d',strtotime($model->vi_date));}
			],
			[	'attribute' => 'badge_involved',
			],
			[	'attribute' => 'club_id',
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'club_id',(new clubs)->getClubList(),['class'=>'form-control','prompt' => 'All']),
				 
				'value'=> function($model, $attribute) {
					return (new clubs)->getMyClubsNames($model->badge_involved);
				},
			],
			[	'attribute'=> 'was_guest',
				'value' => function($model) {
					if ($model->was_guest) {return 'Yes';} else { return 'No';} }
			],
			[	'attribute' => 'vi_rules',
			],
			[	'attribute' => 'vi_type',
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'vi_type',
					['1'=>'Class 1','2'=>'Class 2','3'=>'Class 3','4'=>'Class 4'],['class'=>'form-control','prompt' => 'All']),
				'value'=> 'vi_type',
			],
			[	'attribute' => 'vi_loc',
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'vi_loc',(new Violations)->getLocations(),['class'=>'form-control','prompt' => 'All']),
				'value'=> function($model, $attribute) {
					return $model->getLocations($model->vi_loc);
				},
			],
			[	'attribute' => 'vi_sum',
			]
		];
		
	$gridColumns = [
			[	'attribute'=>'vi_date',
				'contentOptions' =>  function($model) {
						if($model->vi_type =='4') {$color=" color:red;";} else {$color="";}
						return ['style' => 'width:8%;'.$color];},
				'value' => function($model) {
					return date('Y-m-d',strtotime($model->vi_date));}
			],
			[	'attribute' => 'badge_involved',
				'contentOptions' =>  function($model) {
						if($model->vi_type =='4') {$color=" color:red;";} else {$color="";}
						return ['style' => 'width:5%;'.$color];},
			],
			[	'attribute' => 'club_id',
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'club_id',(new clubs)->getClubList(),['class'=>'form-control','prompt' => 'All']),
				'contentOptions' =>  function($model) {
					if($model->vi_type =='4') {$color=" color:red;";} else {$color="";}
					return ['style' => 'width:20%; overflow: auto; word-wrap: break-word; white-space: normal;'.$color];},
				'value'=> function($model, $attribute) {
					return (new clubs)->getMyClubsNames($model->badge_involved);
				},
			],
			[	'attribute'=> 'was_guest',
				'contentOptions' =>  function($model) {
						if($model->vi_type =='4') {$color=" color:red;";} else {$color="";}
						return ['style' => 'width:5%;'.$color];},
				'value' => function($model) {
					if ($model->was_guest) {return 'Yes';} else { return 'No';} }
			],
			[
				'attribute' => 'vi_rules',
				'contentOptions' =>  function($model) {
						if($model->vi_type =='4') {$color=" color:red;";} else {$color="";}
						return ['style' => 'width:12%; white-space:pre-line;'.$color];}, 
			],
			[	'attribute' => 'vi_type',
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'vi_type',
					['1'=>'Class 1','2'=>'Class 2','3'=>'Class 3','4'=>'Class 4'],['class'=>'form-control','prompt' => 'All']),
				'contentOptions' =>  function($model) {
					if($model->vi_type =='4') {$color=" color:red;";} else {$color="";}
					return ['style' => 'width:5%;'.$color];},
				'value'=> 'vi_type',
			],
			[	'attribute' => 'vi_loc',
				'contentOptions' =>  function($model) {
						if($model->vi_type =='4') {$color=" color:red;";} else {$color="";}
						return ['style' => 'width:5%; white-space:pre-line;'.$color];},
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'vi_loc',(new Violations)->getLocations(),['class'=>'form-control','prompt' => 'All']),
				'value'=> function($model, $attribute) {
					return $model->getLocations($model->vi_loc);
				},
			],
			[	'attribute' => 'vi_sum',
				'contentOptions' =>  function($model) {
					if($model->vi_type =='4') {$color=" color:red;";} else {$color="";}
					return ['style' => 'width:40%; overflow: auto; word-wrap: break-word; white-space: normal;'.$color];
				},
			]
		];
	?>
<div class="violations-index" ng-controller="ViolationsReport">
<div class="row">
	<div class="col-xs-6 col-sm-4">
		<h1><?= Html::encode($this->title) ?></h1>
	</div>
</div>

 <?php $form = ActiveForm::begin([
	'id'=>'ViolationsReport',
	'action' => ['/violations/report'],
	'method' => 'get',
]); ?>
<div class="row">
	<div class="col-xs-12 col-sm-3 col-md-2 col-lg-3 col-xl-3"> <p> <br /></p>
		Export Data - 	
		<?=ExportMenu::widget([
			'dataProvider' => $dataProvider,
			'filterModel' => $searchModel,
			'columns' => $exportColumns,
			'fontAwesome' => true,
			'batchSize' => 10,
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
	<div class="col-xs-0 col-sm-0 col-md-1 col-lg-2 col-xl-3" >
	</div>
	<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 col-xl-3" >	
	<?=  $form->field($searchModel, 'vi_date', [
	'options'=>['class'=>'drp-container form-group']
	])->widget(DateRangePicker::classname(), [
		'presetDropdown'=>true,
		'hideInput'=>false,
		'pluginOptions' => [
			'opens'=>'left',
			'locale'=>['format'=>'MM/DD/YYYY'],
		]])->label('Date range:'); ?>
	</div>
	<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 col-xl-2">
		<?= $form->field($searchModel, 'pagesize')->dropDownlist([ 20 => 20, 50 => 50, 100 => 100, 200=>200],['value'=>$pagesize ,'id' => 'pagesize'])->label('Page size: ') ?>
	</div>
	<div class="col-xs-6 col-sm-4 col-md-4 col-lg-2 col-xl-0" "btn-group pull-right">
		<div class="form-group btn-group ">
		<br>
		<?= Html::submitButton('<i class="fa fa-search" aria-hidden="true"></i> Search', ['class' => 'btn btn-primary']) ?>
		<?= Html::a('<i class="fa fa-eraser" aria-hidden="true"></i> Reset', ['violations/report?reset=true'],['class' => 'btn btn-danger']) ?>
		</div>
	</div>
</div>
<?php ActiveForm::end(); ?>
		
</div>
<?php
	echo GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => $gridColumns,
	]); ?>
</div>
</div>

<script>
$("#w0-cols").hide();
</script>
