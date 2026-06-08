<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\discountSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Discount List';
if(yii::$app->controller->hasPermission('site/admin-menu')) {
	$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']]; }
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/params/discount']];

if (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=20;
}
$dataProvider->pagination = ['pageSize' => $pagesize];
?>
<div class="discount-index">
	<div class="row">
		<div class="col-xs-12">
			<h2><?= Html::encode($this->title) ?></h2>
<?php if(yii::$app->controller->hasPermission('discount/create')) { ?>
			<div class="btn btn-group pull-right">
				<?= Html::a('Create Club', ['create'], ['class' => 'btn btn-success']) ?>
			</div >
<?php } ?>
			<?php Pjax::begin(); ?>
			<?= GridView::widget([
				'dataProvider' => $dataProvider,
				'filterModel' => $searchModel,
				'columns' => [
					//['class' => 'yii\grid\SerialColumn'],
					[
						'attribute'=>'dis_id',
						'headerOptions' => ['style' => 'width:5%'],
					],
					'dis_name',
					'dis_short',
					[   'attribute'=>'dis_active',
						'value' => function($model, $attribute){ if($model->dis_active==0) {return 'No';} else {return 'Yes';} },
						'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'dis_active', ['0' => 'No', '1' => 'Yes'],['class'=>'form-control','prompt' => 'All']),
					],
					'dis_amount',
					[   'attribute' => 'dis_allowed',
						'value' => function($model) { return implode(", ", $model->dis_allowed); },
						'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'dis_allowed', ['new' => 'New', 'renew' => 'Renew'],['class'=>'form-control','prompt' => 'All']),
						'headerOptions' => ['style' => 'width:15%'],
					],
					[   'attribute' => 'dis_def',
						'value' => function($model) { if($model->dis_def==0) {return 'No';} else {return 'Yes';} },
						'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'dis_def', ['0' => 'No', '1' => 'Yes'],['class'=>'form-control','prompt' => 'All']),
						'headerOptions' => ['style' => 'width:15%'],
					],
		[
			'header' => 'Actions',
			'class' => 'yii\grid\ActionColumn',
			'template'=>'{view} {update} {delete}',
			'buttons'=> [
				'update' => function ($url, $model) {
					if ((in_array(1, json_decode(yii::$app->user->identity->privilege))) ||
					((yii::$app->controller->hasPermission('params/discountupdate')) && (!array_intersect([1,2],json_decode(yii::$app->user->identity->privilege))))) {
					return  Html::a(' <span class="glyphicon glyphicon-pencil"></span> ', ['/params/discountupdate','id'=>$model->dis_id], [
						'data-toggle'=>'tooltip',
						'data-placement'=>'top',
						'title'=>'Edit',
						'class'=>'edit_item',
					]); }
				},
				'delete' => function($url,$model) {
					if(yii::$app->controller->hasPermission('params/discountdelete'))  {
					return  Html::a(' <span class="glyphicon glyphicon-trash"></span> ', ['/params/discountdelete','id'=>$model->dis_id], [
						'data-toggle'=>'tooltip',
						'data-placement'=>'top',
						'title'=>'Delete',
						'data' => [
							'confirm' => 'Are you sure you want to delete club '.$model->club_name.'?',
							'method' => 'post',
						],
					]); }
				},
				'view' => function($url,$model) {
					return  Html::a(' <span class="glyphicon glyphicon-eye-open"></span> ', ['/params/discountview','id'=>$model->dis_id], [
						'data-toggle'=>'tooltip',
						'data-placement'=>'top',
						'title'=>'View',
					]);
				},
			]
		],
				],
			]); ?>
			<?php Pjax::end(); ?>
		</div>
	</div>
</div>
