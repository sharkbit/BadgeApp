<?php

use backend\models\AgcCal;
use backend\models\agcEventStatus;
use backend\models\agcFacility;
use backend\models\agcRangeStatus;
use kartik\daterange\DateRangePicker;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;

$model = new AgcCal();

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\EventsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Calendar List';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/calendar/index']];
$urlStatus = yii::$app->controller->getCurrentUrl();

if (isset($_REQUEST['AgcCal']['pagesize'])) {
	$pagesize = $_REQUEST['AgcCal']['pagesize'];
	$_SESSION['pagesize'] = $_REQUEST['AgcCal']['pagesize'];
} elseif (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=20;
}
$dataProvider->pagination = ['pageSize' => $pagesize];

if (yii::$app->controller->hasPermission('calendar/shoot')) {
	$sql="SELECT facility_id FROM associat_agcnew.facilities WHERE name like '%shoot%'";
	$result = Yii::$app->getDb()->createCommand($sql)->queryAll();
	$shoot = json_encode(ArrayHelper::getColumn($result, 'facility_id'));
	$shoot = json_decode(str_replace('"','',$shoot));
} else { $shoot =[]; }
?>
	<?= $this->render('_index-tab-menu',['model'=>$model]) ?>

	<h2><?= Html::encode($this->title) ?></h2>

<div class="calendar-index">
<?php $form = ActiveForm::begin([
	//'action' => ['index'],
	'method' => 'post',
	'id'=>'calendarFilter',
]); ?>
<div class="row">

	<div class="col-xs-12 col-sm-3" <?php if(($urlStatus['actionId']=='recur')||($urlStatus['actionId']=='conflict')) {echo ' style="display: none"'; } ?>>
		<?=  $form->field($searchModel, 'SearchTime', [
		'options'=>['class'=>'drp-container form-group']
		])->widget(DateRangePicker::classname(), [
		//'presetDropdown'=>true,
		//'hideInput'=>true,
		'convertFormat'=>true,
		'pluginOptions' => [
			'opens'=>'left',
			'locale'=>['format'=>'Y-m-d','separator'=>' - ',],
		]])->label('Date:'); ?>
	</div>
	<div class="col-xs-4 col-sm-2">
		<?= $form->field($model, 'pagesize')->dropDownlist([ 20 => 20, 50 => 50, 100 => 100, 200=>200 ],['value'=>$pagesize ,'id' => 'pagesize'])->label('Page size: ') ?>
	</div>
	<div class="col-xs-4 col-sm-2"><br />
		<?= Html::submitButton('<i class="fa fa-search" aria-hidden="true"></i> Search', ['class' => 'btn btn-primary']) ?>
		<?= Html::a('<i class="fa fa-eraser" aria-hidden="true"></i> Reset',['index?reset=true'], ['class' => 'btn btn-danger']) ?>
	</div>
	<div class="col-xs-4  col-sm-2 pull-right">
		<?php if (yii::$app->controller->hasPermission('calendar/create')) { ?>
		<div class="btn btn-group pull-right">
		<?php if($urlStatus['actionId']=='recur') {$extra='?recur=1';} else {$extra='';} ?>
			<?= Html::a('Create Event', ['create'.$extra], ['class' => 'btn btn-success']) ?>
		</div > <?php } ?>
	</div>
</div>
<?php ActiveForm::end(); ?>
<div class="row">
<div class="col-xs-12">

	<?php Pjax::begin(); ?>
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[	'attribute'=>'club_id',
				'format'=>'raw',
				'value'=>function($model) {
		//yii::$app->controller->createLog(false, 'trex:V_C_i', var_export($model->calendar_id,true));

					return @$model->clubs->short_name.' <img src="/images/note.png" title="'.@$model->clubs->club_name.'" style="width:18px" />'; },
				'headerOptions' => ['style' => 'width:10%']
			],
			[	'attribute'=>'event_name',
				'format' => 'raw',
				'headerOptions' => ['style' => 'width:20%'],
				'contentOptions' => ['style' => 'white-space:pre-line;'],
				'value'=>function ($model) {
					if($model->recurrent_calendar_id>0) {$mas=" *";} else {$mas="";}
					if ((yii::$app->controller->hasPermission('calendar/update')) && ((array_intersect([1,2],$_SESSION['privilege'])) || (in_array($model->club_id, json_decode(yii::$app->user->identity->clubs))))) {
						return Html::a($model->event_name,'/calendar/update?id='.$model->calendar_id).$mas; }
					else { return $model->event_name.$mas; }
				},
			],
			[	'attribute'=>'facility_id',
				'value'=>function($model) { return (New AgcCal)->getAgcFacility_Names($model->facility_id); },
				'contentOptions' => ['style' => 'white-space:pre-line;'],
				'headerOptions' => ['style' => 'width:15%'],
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'facility_id', ArrayHelper::map(agcFacility::find()->where(['active'=>1])->orderBy(['name'=>SORT_ASC])->asArray()->all(), 'facility_id', 'name'),['class'=>'form-control','prompt' => 'All']),
			],
			[	'attribute'=>'lanes_requested',
				'value'=>function($model) { if($model->lanes_requested ==0) { return '';} else { return $model->lanes_requested; } },
			],
			[	'attribute'=>'event_date',
				'visible' => ($searchModel->recur_every) ? false : true,
				'value'=>function($model) {
					return $model->event_date;
				},
			],
			[	'attribute' => 'recurrent_start_date',
				'visible' => ($urlStatus['actionId']=='recur') ? true : false,
				'value'=>function($model) { return date('M d',strtotime(date('y').'-'.$model->recurrent_start_date)); },
			],
			[	'attribute' => 'recurrent_end_date',
				'visible' => ($urlStatus['actionId']=='recur') ? true : false,
				'value'=>function($model) { return date('M d',strtotime( date('y').'-'.$model->recurrent_end_date)); },
			],
			[	'attribute' => 'recur_week_days',
				'visible' => ($urlStatus['actionId']=='recur') ? true : false,
				'value'=>function($model) {
					$wtf = json_decode($model->recur_week_days);
					if (strpos($model->recur_week_days,'daily'))   {
						if ($wtf->daily=='wd') { return 'Daily, Every WeekDay'; }
						else {return 'Daily, Every '.$wtf->daily.' days';}
					}
					else if (strpos($model->recur_week_days,'weekly'))  {
						return 'Weekly, '.ucfirst(implode(", ",$wtf->days)).' x '.$wtf->weekly;
					}
					else if (strpos($model->recur_week_days,'monthly')) {
						if ($wtf->monthly=='day') { return 'Monthly, '.ucfirst($wtf->when).' '.substr(ucfirst($wtf->day),0,3).' x '.$wtf->every.'m'; }
						return 'Monthly ... '.$model->recur_week_days;
					}
					else if (strpos($model->recur_week_days,'yearly')) {
						if ($wtf->yearly=='day') { return 'Yearly, '.ucfirst($wtf->on).' '.ucfirst($wtf->day).' of '.date("M", mktime(0, 0, 0, $wtf->of, 10)); }
						return 'Yearly, on '.date("M", mktime(0, 0, 0, $wtf->mon, 10)).' '.$wtf->day;
					}
					else return '';

				},
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'recur_week_days',['d'=>'Daily','w'=>'Weekly','m'=>'Monthly','y'=>'Yearly'],['class'=>'form-control','prompt' => 'All']),
				'contentOptions' => ['style' => 'white-space:pre-line;'],
				'headerOptions' => ['style' => 'width:10%'],
			],
			[	'attribute'=>'start_time',
				'value'=>function($model) {
					return substr(substr($model->start_time, -8),0,5);
				},
			],
			[	'attribute'=>'end_time',
				'value'=>function($model) {
					return substr(substr($model->end_time, -8),0,5);
				},
			],
			[	'attribute'=>'showed_up',
				'visible' => (yii::$app->controller->hasPermission('calendar/showed')) ? (($urlStatus['actionId']=='index') ? true : false ): false,
				'format'=>'raw',
				'value'=>function($model) {
					if ($model->event_date <= date('Y-m-d',strtotime("-8 day", strtotime(yii::$app->controller->getNowTime())))) {
						if ($model->showed_up==1) {return 'Yes';}else {return 'No';}
					}
					elseif ($model->event_date <= yii::$app->controller->getNowTime()) {
						if ($model->showed_up==1) {
							$link_y='Yes'; $link_n=Html::a('No', ['/calendar/showed','id'=>$model->calendar_id,'showed'=>0] ); $thum='up';
						} else {
							$link_y=Html::a('Yes', ['/calendar/showed','id'=>$model->calendar_id,'showed'=>1] ); $link_n='No'; $thum='down';
						}
						return $link_y. " / ".$link_n." - <i class='fa fa-thumbs-$thum' ></i>";
					} else { return "Not Yet"; }
				},
			],
			[	'attribute'=>'event_status_id',
				'value'=>function($model) { return $model->agcEventStatus->name; },
				'contentOptions' => ['style' => 'white-space:pre-line;'],
				'headerOptions' => ['style' => 'width:10%'],
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'event_status_id',(new agcEventStatus)->getStatusList(),['class'=>'form-control','prompt' => 'Any']),
			],
			[	'attribute'=>'range_status_id',
				'value'=>function($model) { return $model->agcRangeStatus->name; },
				'contentOptions' => ['style' => 'white-space:pre-line;'],
				'headerOptions' => ['style' => 'width:10%'],
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'range_status_id',(new agcRangeStatus)->getStatusList(),['class'=>'form-control','prompt' => 'Any']),
			],
			[	'attribute'=>'active',
				'value'=>function($model) { if($model->active) {return "Yes";} else  {return "No";} },
				'headerOptions' => ['style' => 'width:5%'],
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'active',['1'=>'Yes','0'=>'No'],['class'=>'form-control','prompt' => 'All']),
			],
			[	'attribute'=>'approved',
				'format'=>'raw',
				'contentOptions' => ['style' => 'white-space:pre-line;'],
				'headerOptions' => ['style' => 'width:5%'],
				'value'=>function($model) {
					if($model->approved) {
						return "True";
					} else {
						if (yii::$app->controller->hasPermission('calendar/approve')) {
							return  Html::a(' <span class="glyphicon glyphicon-ok"> </span> Approve Event', ['/calendar/approve','id'=>$model->calendar_id,'redir'=>yii::$app->controller->getCurrentUrl()['actionId']], [
							'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'Approve',	]);
						} else { return "False"; }
					}
				},
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'approved',['1'=>'True','0'=>'False'],['class'=>'form-control','prompt' => 'All']),
			],
			[	'attribute'=>'deleted',
				'value'=>function($model) { if($model->active) {return "Yes";} else  {return "No";} },
				'visible' => ($urlStatus['actionId']=='inactive') ? true : false,
			],
			[	'attribute' => 'Continue Event',
				'format' => 'raw',
				'visible' =>($urlStatus['actionId']=='recur' ? ($model->getIsPublished() ? false : true ) : false),
				'contentOptions' => ['style' => 'white-space:pre-line;'],
				'headerOptions' => ['style' => 'width:10%'],
				'value'=>function($model) {
					if ((strlen($model->recur_week_days)<2)) { return "Fix Recurring Pattern"; }
					elseif ($model->recurrent_start_date <> $model->recurrent_end_date) {
						if($model->getIsPublished($model->calendar_id)) { return ''; }
						else { return "<a href='/calendar/republish?id=".$model->recurrent_calendar_id."&force_order=1'>Publish to next Year</a>"; }
					} else { return "Fix Recurring dates"; }
				},
			],
			[	'attribute'=>'conflict',
				'value'=>function($model) { if($model->conflict==1) { return 'Yes'; } else { return 'No'; } },
				'visible' => (yii::$app->controller->hasPermission('calendar/conflict')) ? (($urlStatus['actionId']=='index') ? false : true ): false,
			],
			[	'header'=>'Action',
				'visible' => (yii::$app->controller->hasPermission('calendar/view')) ? true : ((yii::$app->controller->hasPermission('calendar/update')) ? true : ((yii::$app->controller->hasPermission('calendar/delete')) ? true : false ) ),
				'class' => 'yii\grid\ActionColumn',
				'template'=>' {view} {update} {delete}',
				'headerOptions' => ['style' => 'width:5%'],
				'buttons'=>[
					'view' => function($url,$model) {
						if (yii::$app->controller->hasPermission('calendar/view')) {
						return  Html::a(' <span class="glyphicon glyphicon-eye-open"></span> ', ['/calendar/view','id'=>$model->calendar_id], [
							'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'View',
						]);}
					},
					'update' => function($url,$model) use ($shoot) {
						if ( ( (yii::$app->controller->hasPermission('calendar/update')) && ((array_intersect([1,2],$_SESSION['privilege'])) || (in_array($model->club_id, json_decode(yii::$app->user->identity->clubs)))) ) || 
							( (yii::$app->controller->hasPermission('calendar/shoot')) && ( array_intersect($shoot, json_decode($model->facility_id)) ) ) ) {
						return  Html::a(' <span class="glyphicon glyphicon-pencil"></span> ', ['/calendar/update','id'=>$model->calendar_id], [
							'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'Update',
						]);}
					},
					'delete' => function($url,$model) use ($shoot) {
						if( ( (yii::$app->controller->hasPermission('calendar/delete')) && ($model->deleted==0) && ((array_intersect([1,2],$_SESSION['privilege'])) || (in_array($model->club_id, json_decode(yii::$app->user->identity->clubs))))) || 
						( (yii::$app->controller->hasPermission('calendar/shoot')) && ( array_intersect($shoot, json_decode($model->facility_id)) ) ) ) {
							return  Html::a(' <span class="glyphicon glyphicon-trash"></span> ',  ['/calendar/delete','id'=>$model->calendar_id,'type'=>(strpos($_SERVER['REQUEST_URI'],'recu') ? 'm' : 's'),'redir'=>yii::$app->controller->getCurrentUrl()['actionId']], [
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
		],
	]); ?>
	<?php Pjax::end(); ?>
</div>
</div>
</div>
<p>* is a Recurring Event</p>
