<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\WorkCreditsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Work Credits';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/work-credits/index']];

if (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=20;
}
$dataProvider->pagination = ['pageSize' => $pagesize];

$activeUser = yii::$app->controller->getActiveUser();

if(yii::$app->controller->hasPermission('work-credits/delete')) {
	$myTemplate=' {view} {update} {delete} ';
} elseif(yii::$app->controller->hasPermission('work-credits/update')) {
	echo "update!";
	$myTemplate=' {view} {update} ';
} else {$myTemplate='{view}';}
?>

<?= $this->render('_view-tab-menu') ?>

<div class="work-credits-index">
<div class="row">
<div class="col-xs-12">
	<?php if(yii::$app->controller->hasPermission('work-credits/create')) { ?>
	<div class="btn-group pull-right"> <?= Html::a('Create Work Credit', ['create'], ['class' => 'btn btn-success ']) ?>
	<?php if(yii::$app->controller->hasPermission('work-credits/import')) {
	echo Html::a('Import', ['/work-credits/import'], ['class' => 'btn btn-primary pull-right pull-right']); } ?>
	</div>
	<?php }

	if(yii::$app->controller->hasPermission('work-credits/delete')) { $visible=true; } else { $visible=false; }
	echo GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'attribute' => 'badge_number',
				'value' => function($model) {
					return str_pad($model->badge_number, 5, '0', STR_PAD_LEFT).' - '.yii::$app->controller->decodeBadgeName((int)$model->badge_number);
				},
			],
			[
				'attribute'=>'work_date',
				'value'=>function($model) {
					return date('M d, Y',strtotime($model->work_date));
				},
			],
			'project_name',
			[
				'attribute'=>'work_hours',
				'contentOptions' => ['class' => 'text-right'],
			],
			[
				'attribute'=>'authorized_by',
				'value'=> 'authorized_by',
			],

			[	'attribute'=>'status',
				'format' => 'raw',
				'value' => function($model, $attribute){
					if($model->status==1) {return 'Approved';}
					else if($model->status==2) {
						if(yii::$app->controller->hasPermission('work-credits/approve')) {
							return Html::a('Approval needed','/work-credits/approve?id='.$model->id);} else {return 'Pending';}
					}
					else if($model->status==3) {return 'Paid';}
				},
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'status', ['1' => 'Approved', '2' => 'Pending','3' => 'Paid'],['class'=>'form-control','prompt' => 'All']),
			],
			[
				'attribute'=>'remarks',
				'value'=> function ($model) {
					if(strlen($model->remarks)>40) {
						return substr($model->remarks,0,35).'...';
					} else {return $model->remarks;}
				},
			],
			[
				'attribute'=>'updated_at',
				'value'=> function ($model) {
						return date('M d, Y',strtotime(($model->updated_at)));
				},
			],
			[
				'attribute' => 'created_by',
				'value' => function($model) {
					return yii::$app->controller->decodeBadgeName((int)$model->created_by).' ('.$model->created_by.')';
				},
			],
			[
				'header' => 'Actions',
				'class' => 'yii\grid\ActionColumn',
				'template'=>$myTemplate,
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
						return  Html::a(' <span class="glyphicon glyphicon-trash"></span> ', $url,
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
</div>
</div>