<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\roles */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Club Roles';
if(yii::$app->controller->hasPermission('site/admin-menu')) {
	$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']]; }
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/clubs/roles']];

if (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=20;
}
$dataProvider->pagination = ['pageSize' => $pagesize];
?>
<div class="roles-index">
	<div class="row">
	<div class="col-xs-12">
		<?= $this->render('_view-tab-menu') ?>
	</div>
	
	<div class="col-xs-12">
		<h2><?= Html::encode($this->title) ?></h2>

		<?php if(yii::$app->controller->hasPermission('clubs/role-create')) { ?>
		<div class="btn btn-group pull-right"> 
			<?= Html::a('Add Role', ['role-create'], ['class' => 'btn btn-success']) ?> 
		</div >
		<?php } ?>

		<?php Pjax::begin(); ?>	
		<?= GridView::widget([
			'dataProvider' => $dataProvider,
			'filterModel' => $searchModel,
			'columns' => [
				[	'attribute'=>'role_id',
					'headerOptions' => ['style' => 'width:15%'],
				],
				'role_name',
				'disp_order',
				[	'attribute' => 'privs',
					'visible' => (yii::$app->controller->hasPermission('privileges/create')),
				],
				[	'header'=>'Action',
					'class' => 'yii\grid\ActionColumn',
					'template'=>' {update} {delete} ',
					'buttons'=>[
						'update' => function($url,$model) {
							if(yii::$app->controller->hasPermission('clubs/role-update')) {
							return  Html::a(' <span class="glyphicon glyphicon-pencil"></span> ', ['role-update','id'=>$model->role_id], [
								'data-toggle'=>'tooltip',
								'data-placement'=>'top',
								'title'=>'Edit',
								'class'=>'edit_item',
							]);}
						},
						'delete' => function($url,$model) {
							if(yii::$app->controller->hasPermission('clubs/role-delete')) {
							return  Html::a(' <span class="glyphicon glyphicon-trash"></span> ', ['role-delete','id'=>$model->role_id], [
								'data-toggle'=>'tooltip',
								'data-placement'=>'top',
								'title'=>'Delete',
								'data' => [
									'confirm' => 'Are you sure you want to delete '.$model->role_name.'?',
									'method' => 'post',
								],
							]); }
						}
					],
				],
			],
		]); ?>
		<?php Pjax::end(); ?>
	</div>
	</div>
</div>
