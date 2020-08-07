<?php

use backend\models\clubs;
use backend\models\User;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Authorized Users';
$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/accounts/index']];

if (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=20;
}
$dataProvider->pagination = ['pageSize' => $pagesize];
?>
<div class="user-index">
<div class="row">
<div class="col-xs-12">
<h2><?= Html::encode($this->title) ?></h2>
<p> <?= Html::a('Create User', ['create'], ['class' => 'btn btn-success pull-right']) ?> </p>
<?= GridView::widget([
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
	'columns' => [
		['class' => 'yii\grid\SerialColumn'],
		[
			'attribute'=>'username',
			'format' => 'raw',
			'value'=>function($model) { 
				return Html::a($model->username,'/accounts/update?id='.$model->id); 
			}
		],
		'email:email',
		'full_name',
		'privilege' => [   'header'=>'Privilege',
			'value' => function($model, $attribute){ return (new User)->getPrivilege_Names($model->privilege); },
			'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'privilege', (new User)->getPrivList(),['class'=>'form-control','prompt' => 'All']),
		],
		[
			'attribute' => 'clubs',
			'value'=> function($model) {
				if(is_array(json_decode($model->clubs))) {
					$clubList = (new clubs)->getClubList(true);	$clubStr='';
					foreach(json_decode($model->clubs) as $club) { $clubStr.=$clubList[$club].', '; }
					return rtrim($clubStr, ', ');
				}
			}
		],
		[
			'attribute'=>'badge_number',
			'format' => 'raw',
			'value'=>function($model) { 
				if($model->badge_number >0){
					return Html::a($model->badge_number,'/badges/view?badge_number='.$model->badge_number); 
				} else {return $model->badge_number;} 
			}
		],
		[
			'header' => 'Actions',
			'class' => 'yii\grid\ActionColumn',
			'template'=>'{view} {update} {delete} {reset}',
			'buttons'=> [
				'reset' => function($url,$model) {
					if(!$model->id==0){ if(!$model->badge_number) { if(yii::$app->controller->hasPermission('is_root')) {
					return  Html::a(' <span class="glyphicon glyphicon-refresh"></span> Reset Password <br />', ['/accounts/request-password-reset','id'=>$model->id], [
						'target'=>'_blank',
						'data-toggle'=>'tooltip',
						'data-placement'=>'top',
						'title'=>'Reset',
					]); }}}
				},
				'update' => function ($url, $model) {
					if(!$model->id==0){
						if((yii::$app->controller->hasPermission('is_root')) ||
					((yii::$app->controller->hasPermission('accounts/update')) && (!in_array(1,json_decode($model->privilege))))) {
					return  Html::a(' <span class="glyphicon glyphicon-pencil"></span> Edit <br />', ['/accounts/update','id'=>$model->id], [
						'data-toggle'=>'tooltip',
						'data-placement'=>'top',
						'title'=>'Edit',
						'class'=>'edit_item',
					]); }}
				},
				'delete' => function($url,$model) {
					if(!$model->id==0){
						if(yii::$app->controller->hasPermission('accounts/delete'))  {
					return  Html::a(' <span class="glyphicon glyphicon-trash"></span> Delete <br />', ['/accounts/delete','id'=>$model->id], [
						'data-toggle'=>'tooltip',
						'data-placement'=>'top',
						'title'=>'Delete',
						'data' => [
							'confirm' => 'Are you sure you want to delete this item?',
							'method' => 'post',
						],
					]); }}
				},
				'view' => function($url,$model) {
					return  Html::a(' <span class="glyphicon glyphicon-eye-open"></span> View <br />', ['/accounts/view','id'=>$model->id], [
						'data-toggle'=>'tooltip',
						'data-placement'=>'top',
						'title'=>'View',
					]); 
				},
			]
		],
	],
]); ?>

</div>
</div>
<?php // echo $this->render('_search', ['model' => $searchModel]); ?>
</div>
