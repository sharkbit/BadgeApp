<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use backend\models\clubs;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\EventsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

if (isset($_REQUEST['EventsSearch']['pagesize'])) {
	$pagesize = $_REQUEST['EventsSearch']['pagesize'];
	$_SESSION['pagesize'] = $_REQUEST['EventsSearch']['pagesize'];
} elseif (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=20;
}
$dataProvider->pagination = ['pageSize' => $pagesize];

$this->title = 'Events List';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/events/index']];
?>
<div class="events-index">
<div class="row">
<div class="col-xs-12">
	<h2><?= Html::encode($this->title) ?></h2>

	<?php if (yii::$app->controller->hasPermission('events/create')) { ?>
	<div class="btn btn-group pull-right">
		<?= Html::a('Create Event', ['create'], ['class' => 'btn btn-success']) ?>
	</div > <?php } ?>

	<?php Pjax::begin(); ?>
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			//['class' => 'yii\grid\SerialColumn'],
			[	'attribute'=>'e_date',
				'headerOptions' => ['style' => 'width:10%'],
				'format'=>'raw',
				'value'=>function($model) {
					if ($model->e_date == date('Y-m-d',strtotime(yii::$app->controller->getNowTime()))) {
						return 'Today '.$model->e_date."</style>";
					} else {
						return $model->e_date;
					}
				}
			],
			[ 
				'attribute' => 'sponsor',
				'contentOptions' =>['style' => 'overflow: auto; word-wrap: break-word; white-space: normal;'],
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'sponsor',(new clubs)->getClubList(),['class'=>'form-control','prompt' => 'All']),
				'format' => 'raw',
				'value'=>function($model) {
					if(isset($model->sponsor)) { return $model->clubs->club_name; } else { return ''; }
				},
				'headerOptions' => ['style' => 'width:25%']
            ],
			[	'attribute'=>'e_name',
				'format'=>'raw',
				'value'=>function($model) {
					if(strtotime($model->e_date) <= strtotime(yii::$app->controller->getNowTime())) { $send_to="view"; } else {
						if (yii::$app->controller->hasPermission('events/update')) { $send_to="update"; } else { $send_to="view"; } }
					return Html::a($model->e_name,"/events/$send_to?id=".$model->e_id);},
				'headerOptions' => ['style' => 'width:25%']
			],
			[	'attribute'=>"Student #'s",
				'value'=>function($model) {return $model->getEventdata($model->e_id);},
				'headerOptions' => ['style' => 'width:5%'],
			],
			[	'attribute'=>'e_status',
				'value'=>function($model) {
					if($model->e_status=='0') {return 'Open';}
					elseif($model->e_status=='2') {return 'Canceled';} else { return 'Closed';}
				},
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'e_status',[0=>'Open',1=>'Closed',2=>'Canceled'],['class'=>'form-control','prompt' => 'All']),
				'headerOptions' => ['style' => 'width:10%'] ],
			[
				'attribute'=>'e_poc',
				'value'=>function($model) { return $model->badges->first_name.' '.$model->badges->last_name; },
				'headerOptions' => ['style' => 'width:10%']
			],
			[
				'attribute'=>'e_type',
				'value'=>function($model) { return strtoupper($model->e_type); },
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'e_type',[ 'cio'=>'CIO', 'club'=>'Club', 'vol'=>'Volunteer' ],['class'=>'form-control','prompt' => 'All']),
				'headerOptions' => ['style' => 'width:10%']
			],
			[	'attribute'=>'wbOut',
				'label'=>'WB Out',
				'value'=>function($model) { return $model->event_Att; },
				'headerOptions' => ['style' => 'width:5%']
			],
			[
				'header'=>'Action',
				'class' => 'yii\grid\ActionColumn',
				'template'=>' {view} {update} {delete} ',
				'headerOptions' => ['style' => 'width:5%'],
				'buttons'=>[
					'view' => function($url,$model) {
						if ($model->e_date <= date('Y-m-d',strtotime(yii::$app->controller->getNowTime()))) {
						return  Html::a(' <span class="glyphicon glyphicon-eye-open"></span> ', ['/events/view','id'=>$model->e_id], [
							'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'View',
						]);}
					},
					'update' => function($url,$model) {
						if (yii::$app->controller->hasPermission('events/update')) {
						if ($model->e_date < date('Y-m-d',strtotime(yii::$app->controller->getNowTime()))) { } else {
						return  Html::a(' <span class="glyphicon glyphicon-pencil"></span> ', ['/events/update','id'=>$model->e_id], [
							'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'Update',
						]);}}
					},
					'delete' => function($url,$model) {
						if(yii::$app->controller->hasPermission('events/delete')) {
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
