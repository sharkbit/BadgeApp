<?php

use yii\helpers\Html;
use kartik\export\ExportMenu;
use yii\grid\GridView;
use yii\widgets\Pjax;

use kartik\widgets\ActiveForm;
use backend\models\clubs;
use backend\models\agcEventStatus;
use backend\models\Events;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\EventsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Events List';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/events/index']];
$urlStatus = yii::$app->controller->getCurrentUrl();

if (isset($_REQUEST['EventsSearch']['pagesize'])) {
	$pagesize = $_REQUEST['EventsSearch']['pagesize'];
	$_SESSION['pagesize'] = $_REQUEST['EventsSearch']['pagesize'];
} elseif (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=20;
}
$dataProvider->pagination = ['pageSize' => $pagesize];

$newEvents = (new Events())->getNewEvents();
$missingEvents =[];
if(!empty($newEvents)) {
	foreach($newEvents as $missingCal) {
		$missingEvents[$missingCal->calendar_id] = [
			'event_name' => $missingCal->event_name,
			'event_status_id' => $missingCal->event_status_id,
			'club_name' => $missingCal->club_name,
			'allow_guests' => $missingCal->allow_guests,
			'is_volunteer' => $missingCal->is_volunteer,
			'track_wristbands' => $missingCal->track_wristbands
		];
	}
	 yii::$app->controller->createLog(true, 'trex-missingEvents', var_export($missingEvents,true));
}
?>
<input type='hidden' id='missingEvents' value='<?=htmlspecialchars(json_encode($missingEvents),ENT_QUOTES)?>' />

<div class="events-index">
<div class="row">
<?php Pjax::begin(); ?>
<?php $form = ActiveForm::begin([
	'action' => [$urlStatus['actionId']],
	'method' => 'get',
	'id'=>'EventListFilter',
]); ?>
	<div class="col-xs-12">
		<h2><?= Html::encode($this->title) ?></h2>
	</div>

	<?php
	$gridColumns = [
		[	'attribute'=>'event_date',
			'headerOptions' => ['style' => 'width:10%'],
			'format'=>'raw',
			'value'=>function($model) {
				if ($model->event_date == date('Y-m-d',strtotime(yii::$app->controller->getNowTime()))) {
					return 'Today '.$model->event_date."</style>";
				} else {
					return $model->event_date;
				}
			}
		],
		'cal_start_time',
		[ 
			'attribute' => 'club_name',
			'contentOptions' =>['style' => 'overflow: auto; word-wrap: break-word; white-space: normal;'],
			'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'club_id',(new clubs)->getClubList(),['class'=>'form-control','prompt' => 'All']),
			'headerOptions' => ['style' => 'width:25%']
		],
		[	'attribute'=>'event_name',
			'contentOptions' =>['style' => 'overflow: auto; word-wrap: break-word; white-space: normal;'],
			'format'=>'raw',
			'value'=>function($model) {
				if(strtotime($model->event_date) <= strtotime(yii::$app->controller->getNowTime())) { $send_to="view"; } else {
					if (yii::$app->controller->hasPermission('events/update')) { $send_to="update"; } else { $send_to="view"; } }
				return Html::a($model->event_name,"/events/$send_to?id=".$model->ea_calendar_id);},
			'headerOptions' => ['style' => 'width:25%']
		],
		'attended_badges',
		'attended_guests',
		[	'attribute'=>'wb_out_zero',
			'value'=>function($model) {
					if($model->track_wristbands==1) {return ($model->attended_guests-$model->wb_out_zero ?? 0 ); } else { return '-'; }
			}		
		],
		[	'attribute'=>'event_status_id',
			'contentOptions' =>['style' => 'overflow: auto; word-wrap: break-word; white-space: normal;'],
			'value'=>function($model) { return $model->event_status_name; },
			'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'event_status_id',(new agcEventStatus)->getStatusList(),['class'=>'form-control','prompt' => 'Any']),
		],
			
/*		[
			'attribute'=>'e_poc',
			'value'=>function($model) { return $model->badges?->first_name.' '.$model->badges?->last_name; },
			'headerOptions' => ['style' => 'width:10%']
		], */
		[
			'header'=>'Action',
			'class' => 'yii\grid\ActionColumn',
			'template'=>' {view} {Calendar}  ',
			'headerOptions' => ['style' => 'width:5%'],
			'buttons'=>[
				'view' => function($url,$model) {
					if ($model->event_date <= date('Y-m-d',strtotime(yii::$app->controller->getNowTime()))) {
					return  Html::a(' <span class="glyphicon glyphicon-eye-open"></span> ', ['/events/view','id'=>$model->ea_calendar_id], [
						'data-toggle'=>'tooltip',
						'data-placement'=>'top',
						'title'=>'View',
					]);}
				},
				'Calendar' => function($url,$model) {
					if (yii::$app->controller->hasPermission('calendar/update')) {
					return  Html::a(' <span class="glyphicon glyphicon-calendar"></span> ', ['/calendar/update','id'=>$model->ea_calendar_id], [
						'data-toggle'=>'tooltip',
						'data-placement'=>'top',
						'title'=>'Calendar',
					]);}
				},
				'delete' => function($url,$model) {
					if(yii::$app->controller->hasPermission('events/delete')) {
					return  Html::a(' <span class="glyphicon glyphicon-trash"></span> ', $url, [
						'data-toggle'=>'tooltip',
						'data-placement'=>'top',
						'title'=>'Delete',
						'data' => [
							'confirm' => 'Are you sure you want to delete '.$model->event_name.'?',
							'method' => 'post',
						],
					]); }
				},
			]
		],
	]; ?>

	<div class="col-xs-6 col-sm-2 pull-left">
	Export Data -
	<?=ExportMenu::widget([
		'dataProvider' => $dataProvider,
		'columns' => $gridColumns,
		'fontAwesome' => true,
		'batchSize' => 0,
		'filename'=>  $this->title,
		'target' => '_blank',
		'folder' => '@webroot/export', // this is default save folder on server
		'exportConfig' => [
			ExportMenu::FORMAT_HTML => false,
			ExportMenu::FORMAT_EXCEL => false,
			ExportMenu::FORMAT_EXCEL_X => false,
			//ExportMenu::FORMAT_PDF => false
		]
	]);?>
	</div>
	<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
		<?php $newEvents = ArrayHelper::map((new Events())->getNewEvents(), 'calendar_id', 'event_name'); ?>
		<?= Html::label("Events missing from today's event list:", 'new_event_id', ['class' => 'control-label']) ?>
		<?= Html::dropDownList(
		'new_event_id',
		null,
		$newEvents,
		[
			'id' => 'new_event_id',
			'class' => 'form-control',
			'prompt' => empty($newEvents) ? 'No unmatched events today' : 'Select an event',
		]
	) ?>
	</div>
	<div class="col-xs-6 col-sm-2 col-md-2 col-lg-1 col-xl-1">
		<br /><?= Html::button('<i class="fa fa-add" aria-hidden="true"></i> add', ['class' => 'btn btn-primary','id'=>'addEvent']) ?>
	</div>

	<div class="col-xs-6 col-sm-2 col-md-2 col-lg-1 col-xl-1">
		<?= $form->field($searchModel, 'pagesize')->dropDownlist([ 20 => 20, 50 => 50, 100 => 100, 200=>200 ],['value'=>$pagesize ,'id' => 'pagesize'])->label('Page size: ') ?>
	</div>

	<div class="btn btn-group pull-right">
		<?= Html::submitButton('<i class="fa fa-search" aria-hidden="true"></i> Search', ['class' => 'btn btn-primary']) ?>
		<?= Html::a('<i class="fa fa-eraser" aria-hidden="true"></i> Reset',[$urlStatus['actionId'].'?reset=true'], ['class' => 'btn btn-danger']) ?>
<?php if (yii::$app->controller->hasPermission('calendar/create')) { ?>
		<?= Html::a('Create Event', ['/calendar/create'], ['class' => 'btn btn-success']) ?>
<?php } ?>
	</div > 

	<div class="row">
		<div class="col-xs-12">
			<?php
			   echo GridView::widget([
					'dataProvider' => $dataProvider,
					'filterModel' => $searchModel,
					'columns' => $gridColumns,
				]);
			?>
		</div>
	</div>
	
</div>
<?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>
</div>
</div>
