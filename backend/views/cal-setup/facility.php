<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

use yii\helpers\ArrayHelper;

use backend\models\AgcCal;
$model = new AgcCal();


/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\EventsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Facility List';
$this->params['breadcrumbs'][] = ['label' => 'Calendar Setup'];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/cal-setup/facility']];

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
	<?= $this->render('_view-tab-menu',['model'=>$model]) ?>
	
	<h2><?= Html::encode($this->title) ?></h2>

<div class="cal-setup-index">

<div class="row">
<div class="col-xs-12">

	<?php Pjax::begin(); ?>
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			'facility_id',
			'name',
			'available_lanes',
			[	'attribute'=>'active',
				'value'=>function($model) {
					if($model->active) {
						return "True"; 
					} else { return "False"; }
				},
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'active',['1'=>'True','0'=>'False'],['class'=>'form-control']),
			],
			'display_order',
			[
				'header'=>'Action',
				'class' => 'yii\grid\ActionColumn',
				'template'=>'  {update}', // {view} {delete} ',
				'headerOptions' => ['style' => 'width:5%'],
				'buttons'=>[
					'view' => function($url,$model) {
						if ($model->e_date <= date('Y-m-d',strtotime(yii::$app->controller->getNowTime()))) {
						return  Html::a(' <span class="glyphicon glyphicon-eye-open"></span> ', ['/cal-setup/view','id'=>$model->facility_id], [
							'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'View',
						]);}
					},
					'update' => function($url,$model) {
						if (yii::$app->controller->hasPermission('cal-setup/updatefac')) {
						return  Html::a(' <span class="glyphicon glyphicon-pencil"></span> ', ['/cal-setup/updatefac','id'=>$model->facility_id], [
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
