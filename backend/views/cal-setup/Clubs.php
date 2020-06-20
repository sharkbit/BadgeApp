<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\EventsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Range Status List';
$this->params['breadcrumbs'][] = ['label' => 'Calendar Setup'];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/cal-setup/rangestatus']];

if (isset($_REQUEST['AgcCal']['pagesize'])) { 
	$pagesize = $_REQUEST['AgcCal']['pagesize']; 
	$_SESSION['pagesize'] = $_REQUEST['AgcCal']['pagesize'];
} elseif (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=100;
}
$dataProvider->pagination = ['pageSize' => $pagesize];
?>
	<?= $this->render('_view-tab-menu')  ?>
<div class="cal-setup-index">	
	<h2><?= Html::encode($this->title) ?></h2>

<div class="row"><div class="col-xs-12"><h4>
<ol>
<li> Add missing Clubs / Businesses to BadgeApp</li>
<li> Figure out User Permissions</li>
</ol>
</h4></div></div>



<div class="row">
<div class="col-xs-12">

	<?php Pjax::begin(); ?>
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			'club_id',
			[	'attribute'=>'migrated',
				'value'=>function($model) { if($model->club_id < 1000) {return 'Yes';} else { return 'no';}},
			],
			
			'name',
			'nick_name',
			[	'attribute'=>'active',
				'value'=>function($model) { if($model->active) { return "True"; } else { return "False"; } },
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'active',['1'=>'True','0'=>'False'],['class'=>'form-control']),
			],
			[	'attribute'=>'display_in_administration',
				'value'=>function($model) { if($model->display_in_administration) { return "True"; } else { return "False"; } },
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'display_in_administration',['1'=>'True','0'=>'False'],['class'=>'form-control','prompt' => 'All']),
			],
			[	'attribute'=>'display_in_badges_administration',
				'value'=>function($model) { if($model->display_in_badges_administration) { return "True"; } else { return "False"; } },
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'display_in_badges_administration',['1'=>'True','0'=>'False'],['class'=>'form-control','prompt' => 'All']),
			],
			[	'attribute'=>'is_cio',
				'value'=>function($model) { if($model->is_cio) { return "True"; } else { return "False"; } },
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'is_cio',['1'=>'True','0'=>'False'],['class'=>'form-control','prompt' => 'All']),
			],
			'ca',
			'contact_first_name',
			'contact_last_name',
			'display_order',
			[
				'header'=>'Action',
				'class' => 'yii\grid\ActionColumn',
				'template'=>'  {update}', // {view} {delete} ',
				'headerOptions' => ['style' => 'width:5%'],
				'buttons'=>[
					'view' => function($url,$model) {
						if ($model->e_date <= date('Y-m-d',strtotime(yii::$app->controller->getNowTime()))) {
						return  Html::a(' <span class="glyphicon glyphicon-eye-open"></span> ', ['/cal-setup/view','id'=>$model->club_id], [
							'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'View',
						]);}
					},
					'update' => function($url,$model) {
						if (yii::$app->controller->hasPermission('cal-setup/updateclu')) {
						return  Html::a(' <span class="glyphicon glyphicon-pencil"></span> ', ['/cal-setup/updateclu','id'=>$model->club_id], [
							'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'Update',
						]);}
					},
					'delete' => function($url,$model) {
						if(yii::$app->controller->hasPermission('cal-setup/delete')) {
						return  Html::a(' <span class="glyphicon glyphicon-trash"></span> ', $url, [
							'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'Delete',
							'data' => [
								'confirm' => 'Are you sure you want to delete '.$model->e_name.'?',
								'method' => 'post',
							],
						]); }
					},
				]
			],
		],
	]); ?>
	<?php Pjax::end(); ?>
</div>
</div>
</div>

