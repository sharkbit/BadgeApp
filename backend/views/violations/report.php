<?php

use backend\models\clubs;
use backend\models\Violations;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\WorkCreditsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Violations Report';
$this->params['breadcrumbs'][] = ['label' => 'Violations', 'url' => ['violations/index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['violations/report']];

echo $this->render('_view-tab-menu').PHP_EOL; ?>

<br />
<div class="violations-index" ng-controller="ViolationsReport">
<div class="row">
	<div class="col-sm-6">
		<?=html::a('<i class="fa fa-download" aria-hidden="true"></i> Export as XLS',['#'],['id'=>'customExportBtn','class'=>'btn btn-primary'])?>
	</div>
	<div class="col-sm-4">
		 <?php $form = ActiveForm::begin([
			'id'=>'ViolationsReport',
			'action' => ['/violations/report'],
			'method' => 'get',
		]); ?>

	<?=  $form->field($searchModel, 'vi_date', [
	'options'=>['class'=>'drp-container form-group']
	])->widget(DateRangePicker::classname(), [
		'presetDropdown'=>true,
		'hideInput'=>true,
		'pluginOptions' => [
			'opens'=>'left',
			'locale'=>['format'=>'MM/DD/YYYY'],
		]])->label(false); ?>
	</div>
	<div class="col-sm-2">
		<?= Html::submitButton('<i class="fa fa-search pull-right" aria-hidden="true"></i> Search', ['class' => 'btn btn-primary']) ?>
		<?php ActiveForm::end(); ?>
	</div>
</div>

<div class="row">
<div class="col-xs-12">
<?php  $gridColumns = [
			[
				'attribute'=>'vi_date',
				'contentOptions' =>  function($model) {
						if($model->vi_type =='4') {$color=" color:red;";} else {$color="";}
						return ['style' => 'width:8%;'.$color];}, 
				'value' => function($model) {
					return date('Y-m-d',strtotime($model->vi_date));}
			],
			[
				'attribute' => 'badge_involved',
				'contentOptions' =>  function($model) {
						if($model->vi_type =='4') {$color=" color:red;";} else {$color="";}
						return ['style' => 'width:5%;'.$color];}, 
			],
			[
				'attribute' => 'club_id',
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
			[
				'attribute' => 'vi_type',
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'vi_type',
					['1'=>'Class 1','2'=>'Class 2','3'=>'Class 3','4'=>'Class 4'],['class'=>'form-control','prompt' => 'All']),
				'contentOptions' =>  function($model) {
					if($model->vi_type =='4') {$color=" color:red;";} else {$color="";}
					return ['style' => 'width:5%;'.$color];},
				'value'=> 'vi_type',
			],
			[
				'attribute' => 'vi_loc',
				'contentOptions' =>  function($model) {
						if($model->vi_type =='4') {$color=" color:red;";} else {$color="";}
						return ['style' => 'width:5%; white-space:pre-line;'.$color];},
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'vi_loc',(new Violations)->getLocations(),['class'=>'form-control','prompt' => 'All']),
				'value'=> function($model, $attribute) {
					return $model->getLocations($model->vi_loc);
				},
			],
			[
				'attribute' => 'vi_sum',
				'contentOptions' =>  function($model) {
					if($model->vi_type =='4') {$color=" color:red;";} else {$color="";}
					return ['style' => 'width:40%; overflow: auto; word-wrap: break-word; white-space: normal;'.$color];},
			]
		];

		echo ExportMenu::widget([
			'dataProvider' => $dataProvider,
			'columns' => $gridColumns,
			'fontAwesome' => true,
			'batchSize' => 10,
			'filename'=>  $this->title,
			'target' => '_blank',
			'folder' => '@webroot/export', // this is default save folder on server
		]) . "<hr>\n";
		echo GridView::widget([
			'dataProvider' => $dataProvider,
			'columns' => $gridColumns,
		]);
		
?>
</div>
</div>
</div>
