<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

use backend\models\clubs;
use backend\models\Roles;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\roles */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Club Officers';
$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']];
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

		<?php if(yii::$app->controller->hasPermission('clubs/officers-create')) { ?>
		<div class="btn btn-group pull-right"> 
			<?= Html::a('Add Officer', ['officers-create'], ['class' => 'btn btn-success']) ?> 
		</div >
		<?php } ?>

		<?php Pjax::begin(); ?>	
		<?= GridView::widget([
			'dataProvider' => $dataProvider,
			'filterModel' => $searchModel,
			'columns' => [
				[	'attribute'=>'badge_number',
					'headerOptions' => ['style' => 'width:15%'],
				],
				'full_name',
				[	'attribute' => 'role_name',
					'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'role',(new Roles)->getRoles(),['class'=>'form-control','prompt' => 'All']),
				],
				[	'attribute' => 'club_name',
					'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'club',(new clubs)->getClubList(false,false,2),['class'=>'form-control','prompt' => 'All']),
				],
				'short_name',
				[	'header'=>'Action',
					'class' => 'yii\grid\ActionColumn',
					'template'=>' {update} {delete} ',
					'buttons'=>[
						'update' => function($url,$model) {
							if(yii::$app->controller->hasPermission('clubs/officers-update')) {
							return  Html::a(' <span class="glyphicon glyphicon-pencil"></span> ', ['officers-update?badge_number='.$model->badge_number.'&role='.$model->role.'&club='.$model->club], [
								'data-toggle'=>'tooltip',
								'data-placement'=>'top',
								'title'=>'Edit',
								'class'=>'edit_item',
							]);}
						},
						'delete' => function($url,$model) {
							if(yii::$app->controller->hasPermission('clubs/officers-delete')) {
							return  Html::a(' <span class="glyphicon glyphicon-trash"></span> ', ['officers-delete?badge_number='.$model->badge_number.'&role='.$model->role.'&club='.$model->club], [
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

