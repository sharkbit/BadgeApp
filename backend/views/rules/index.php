<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\Rules;
use kartik\grid\DataColumn;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\WorkCreditsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rules List';
$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['site/admin-menu']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['rules/index']];

if (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=20;
}
$dataProvider->pagination = ['pageSize' => $pagesize];

echo $this->render('/violations/_view-tab-menu').PHP_EOL ?>
<br />
<div class="rules-index">
<div class="row">
<div class="col-xs-12">
<style>
.text-wrap{max-width:"100px";, height:"100px"}
</style>
	<div class="btn-group pull-right">
	<?= Html::a('<i class="fa fa-plus-square" aria-hidden="true"></i> Add Range Rule', ['create'], ['class' => 'btn btn-success ']) ?>

	</div>
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[	'attribute' => 'rule_abrev','headerOptions' => ['style' => 'width:10%'],	],
			[
				'attribute' => 'vi_type',
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'vi_type',['1'=>'Class 1','2'=>'Class 2','3'=>'Class 3','4'=>'Class 4'],['class'=>'form-control','prompt' => 'All']),
                'value' => function($model, $attribute) { return $model->vi_type; },
				'headerOptions' => ['style' => 'width:10%'],
            ],
			[	'attribute'=>'rule_name','contentOptions' => ['style' => 'white-space:pre-line;'],	],
			[
				'attribute'=>'is_active',
				'value'=>function($model) { if($model->is_active) {return "Yes";} else  {return "No";} },
				'headerOptions' => ['style' => 'width:5%'],
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'is_active',['1'=>'Yes','0'=>'No'],['class'=>'form-control','prompt' => 'All']),
			],
			[
				'header' => 'Actions',
				'class' => 'yii\grid\ActionColumn',
				'headerOptions' => ['style' => 'width:5%'],
				//'template'=>yii::$app->controller->hasPermission('violations/delete') ? '{view}{update}{delete}' : '{view}{update}',
				'template'=>'{view} {update}',
				'buttons'=>[
					'view' => function ($url, $model) {
						return  Html::a(' <span class="glyphicon glyphicon-eye-open"></span> ', ['view','id'=>$model->id],
						[	'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'Edit',
							'class'=>'edit_item',
						]);
					},
					'update' => function ($url, $model) {
						return  Html::a(' <span class="glyphicon glyphicon-pencil"></span> ', ['update','id'=>$model->id],
						[	'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'Edit',
							'class'=>'edit_item',
						]);
					},
					'delete' => function($url,$model) {
						return  Html::a(' <span class="glyphicon glyphicon-trash"></span> ', $url, [
							'data-toggle'=>'tooltip',
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
</div>
</div>
