<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\discountSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Membership Status';
if(yii::$app->controller->hasPermission('site/admin-menu')) {
	$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']]; }
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/params/membershipstatus']];

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
						'attribute'=>'act_id',
						'headerOptions' => ['style' => 'width:5%'],
					],
					'act_short',
					'act_name',
					[   'attribute'=>'act_active',
						'value' => function($model, $attribute){ if($model->act_active==0) {return 'No';} else {return 'Yes';} },
						'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'act_active', ['0' => 'No', '1' => 'Yes'],['class'=>'form-control','prompt' => 'All']),
					],
					[   'attribute' => 'act_login',
						'value' => function($model) { if($model->act_login==0) {return 'No';} else {return 'Yes';} },
						'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'act_login', ['0' => 'No', '1' => 'Yes'],['class'=>'form-control','prompt' => 'All']),
						//'headerOptions' => ['style' => 'width:15%'],
					],
					[   'attribute' => 'act_prefill',
						'value' => function($model) { if($model->act_prefill==0) {return 'No';} else {return 'Yes';} },
						'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'act_prefill', ['0' => 'No', '1' => 'Yes'],['class'=>'form-control','prompt' => 'All']),
						//'headerOptions' => ['style' => 'width:15%'],
					],
					[   'attribute' => 'act_new',
						'value' => function($model) { if($model->act_new==0) {return 'No';} else {return 'Yes';} },
						'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'act_new', ['0' => 'No', '1' => 'Yes'],['class'=>'form-control','prompt' => 'All']),
						//'headerOptions' => ['style' => 'width:15%'],
					],
					[   'attribute' => 'act_renew',
						'value' => function($model) { if($model->act_renew==0) {return 'No';} else {return 'Yes';} },
						'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'act_renew', ['0' => 'No', '1' => 'Yes'],['class'=>'form-control','prompt' => 'All']),
						//'headerOptions' => ['style' => 'width:15%'],
					],
					[   'attribute' => 'act_signup',
						'value' => function($model) { if($model->act_signup==0) {return 'No';} else {return 'Yes';} },
						'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'act_signup', ['0' => 'No', '1' => 'Yes'],['class'=>'form-control','prompt' => 'All']),
						//'headerOptions' => ['style' => 'width:15%'],
					],
					'act_color',
					'act_order',
					
					[	'attribute' => 'act_desc',
						'value'=> function($model) { return $model->act_desc;},
						'headerOptions' => ['style' => 'width:45%'],
						'contentOptions' => ['style' => 'white-space: normal; word-wrap: break-word;'],
					],
					
		[
			'header' => 'Actions',
			'class' => 'yii\grid\ActionColumn',
			'template'=>'{update} {delete}',
			'buttons'=> [
				'update' => function ($url, $model) {
					if ((in_array(1, json_decode(yii::$app->user->identity->privilege))) ||
					((yii::$app->controller->hasPermission('params/membershipstatusupdate')) && (!array_intersect([1,2],json_decode(yii::$app->user->identity->privilege))))) {
					return  Html::a(' <span class="glyphicon glyphicon-pencil"></span> ', ['/params/membershipstatusupdate','id'=>$model->act_id], [
						'data-toggle'=>'tooltip',
						'data-placement'=>'top',
						'title'=>'Edit',
						'class'=>'edit_item',
					]); }
				},
				'delete' => function($url,$model) {
					if(yii::$app->controller->hasPermission('params/membershipstatusdelete'))  {
					return  Html::a(' <span class="glyphicon glyphicon-trash"></span> ', ['/params/membershipstatusdelete','id'=>$model->act_id], [
						'data-toggle'=>'tooltip',
						'data-placement'=>'top',
						'title'=>'Delete',
						'data' => [
							'confirm' => 'Are you sure you want to delete club '.$model->act_name.'?',
							'method' => 'post',
						],
					]); }
				},
				'view' => function($url,$model) {
					return  Html::a(' <span class="glyphicon glyphicon-eye-open"></span> ', ['/params/membershipstatusview','id'=>$model->act_id], [
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
