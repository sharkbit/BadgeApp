<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\ClubsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Member Club List';
$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/clubs/index']];
?>
<div class="clubs-index">
	<div class="row">
		<div class="col-xs-12">
			<h2><?= Html::encode($this->title) ?></h2>
<?php if(yii::$app->controller->hasPermission('clubs/create')) { ?>
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
						'attribute'=>'club_id',
						'headerOptions' => ['style' => 'width:15%'],
					],
					'club_name',
					'short_name',
					[	'attribute'=>'avoid',
						'header'=>'Words to Avoid',
					],
					'poc_email',
					[   'attribute'=>'is_club',
							'value' => function($model, $attribute) { if($model->is_club==0) {return 'CIO';} elseif($model->is_club==1){ return 'Club';} else { return 'AGC Sponsored'; } },
							'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'is_club', ['1' => 'Club','0' => 'CIO','2' => 'AGC Sponsored'],['class'=>'form-control','prompt' => 'All']),
					],
					[   'attribute'=>'allow_members',
							'value' => function($model, $attribute){ if($model->allow_members==0) {return 'No';} else {return 'Yes';} },
							'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'allow_members', ['0' => 'No', '1' => 'Yes'],['class'=>'form-control','prompt' => 'All']),
					],
					[   'attribute'=>'Status',
							'value' => function($model, $attribute){ if($model->status==0) {return 'Active';} else {return 'Inactive';} },
							'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'status', ['0' => 'Active', '1' => 'Inactive'],['class'=>'form-control','prompt' => 'All']),
					],
		[
			'header' => 'Actions',
			'class' => 'yii\grid\ActionColumn',
			'template'=>'{view} {update} {delete}',
			'buttons'=> [
				'update' => function ($url, $model) {
					if ((in_array(1, json_decode(yii::$app->user->identity->privilege))) ||
					((yii::$app->controller->hasPermission('clubs/update')) && (!array_intersect([1,2],json_decode(yii::$app->user->identity->privilege))))) {
					return  Html::a(' <span class="glyphicon glyphicon-pencil"></span> ', ['/clubs/update','id'=>$model->club_id], [
						'data-toggle'=>'tooltip',
						'data-placement'=>'top',
						'title'=>'Edit',
						'class'=>'edit_item',
					]); }
				},
				'delete' => function($url,$model) {
					if(yii::$app->controller->hasPermission('clubs/delete'))  {
					return  Html::a(' <span class="glyphicon glyphicon-trash"></span> ', ['/clubs/delete','id'=>$model->club_id], [
						'data-toggle'=>'tooltip',
						'data-placement'=>'top',
						'title'=>'Delete',
						'data' => [
							'confirm' => 'Are you sure you want to delete this item?',
							'method' => 'post',
						],
					]); }
				},
				'view' => function($url,$model) {
					return  Html::a(' <span class="glyphicon glyphicon-eye-open"></span> ', ['/clubs/view','id'=>$model->club_id], [
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
