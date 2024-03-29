<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\Violations;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\WorkCreditsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $model->badge_number;
$this->params['breadcrumbs'][] = ['label' => 'Range Badges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$urlStatus = yii::$app->controller->getCurrentUrl();

if (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=20;
}
$dataProvider->pagination = ['pageSize' => $pagesize];

if(yii::$app->controller->hasPermission('violations/delete')) {
	$myTemplate=' {view}  {update}  {delete} ';
} elseif(yii::$app->controller->hasPermission('violations/update')) {
	$myTemplate=' {view}  {update} ';
} else {$myTemplate='{view}';}
?>
<div class="badges-view">
<div class="row" > 
<div class="col-xs-12">
	<?= $this->render('_view-tab-menu',['model'=>$model]).PHP_EOL ?>
	<h3> Violations  </h3>
	<div class="col-xs-12 col-sm-12">
	<?php if(yii::$app->controller->hasPermission('violations/create')) { ?>
	<div class="btn-group pull-right">
	<?= Html::a('<i class="fa fa-plus-square" aria-hidden="true"></i> Add Violations', ['/violations/create'], ['class' => 'btn btn-success ']) ?>
	</div>
	<?php } ?>
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'attribute'=>'badge_reporter',
				//'format' => 'raw',
				'value'=>function ($model) {
					return yii::$app->controller->decodeBadgeName((int)$model->badge_reporter).' ('.$model->badge_reporter.')';
				}
			],
			[
				'attribute' => 'vi_type',
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'vi_type',
					['1'=>'Class 1','2'=>'Class 2','3'=>'Class 3','4'=>'Class 4'],['class'=>'form-control','prompt' => 'All']),
				'value'=> 'vi_type',
			],
			[
				'attribute' => 'badge_involved',
				'value' => function($model) {
					return yii::$app->controller->decodeBadgeName((int)$model->badge_involved).' ('.$model->badge_involved.')';
				},
			],
			'vi_rules',
			[
				'attribute'=>'was_guest',
				'value' => function($model) { if($model->was_guest=='1') {return "Yes";} else { return "No";} },
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'was_guest',
					['1'=>'Yes','0'=>'No'],['class'=>'form-control','prompt' => 'All']),
			],
			[
				'attribute' => 'vi_loc',
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'vi_loc',(new Violations)->getLocations(),['class'=>'form-control','prompt' => 'All']),
				'value'=> function($model, $attribute) {
					return $model->getLocations($model->vi_loc);
				},
			],
			[
				'attribute'=>'vi_date',
				'value'=>function($model) {
					return yii::$app->controller->pretydtg($model->vi_date);
				},
			],
			[
				'header' => 'Actions',
				'class' => 'yii\grid\ActionColumn',
				'template'=>$myTemplate,
				'buttons'=>[
					'view' => function ($url, $model) {
						return  Html::a(' <span class="glyphicon glyphicon-eye-open"></span>', ['/violations/view','id'=>$model->id],
						[	'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'View',
							'class'=>'edit_item',
						]);
					},
					'update' => function ($url, $model) {
						return  Html::a(' <span class="glyphicon glyphicon-pencil"></span>', ['/violations/update','id'=>$model->id],
						[	'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'Edit',
							'class'=>'edit_item',
						]);
					},
					'delete' => function($url,$model) {
						return  Html::a(' <span class="glyphicon glyphicon-trash"></span>', ['/violations/delete','id'=>$model->id],
						[	'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'Delete',
							'data' => [
								'confirm' => 'Are you sure you want to delete this item?',
								'method' => 'post',
							],
						]);
					},

				]
			]
		]
	]); ?>
	</div>
	<div class="col-xs-12 col-sm-8">

	</div>
</div>
</div>
</div>
