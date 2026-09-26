<?php

use yii\helpers\Html;
use kartik\export\ExportMenu;
use yii\grid\GridView;
use yii\widgets\Pjax;

use kartik\widgets\ActiveForm;
use backend\models\clubs;
use backend\models\agcEventStatus;
use backend\models\Events;
use backend\models\Event_Att;
use yii\helpers\Json;
use yii\helpers\Url;

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
$missingEvents = [];
$newEventOptions = [];
foreach ($newEvents as $missingCal) {
	$missingEvents[$missingCal->calendar_id] = [
		'event_name' => $missingCal->event_name,
		'event_status_name' => $missingCal->event_status_name,
		'club_name' => $missingCal->club_name,
		'allow_guests' => (int) $missingCal->allow_guests,
		'is_volunteer' => (int) $missingCal->is_volunteer,
		'track_wristbands' => (int) $missingCal->track_wristbands,
	];
	$newEventOptions[$missingCal->calendar_id] = $missingCal->club_name.' - '.$missingCal->event_name;
}
$eventAttendee = new Event_Att();
?>

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
		<?= Html::label("Events missing from today's event list:", 'new_event_id', ['class' => 'control-label']) ?>
		<?= Html::dropDownList(
		'new_event_id',
		null,
		$newEventOptions,
		[
			'id' => 'new_event_id',
			'class' => 'form-control',
			'prompt' => empty($newEventOptions) ? 'No unmatched events today' : 'Select an event',
		]
	) ?>
	</div>
	<div class="col-xs-6 col-sm-2 col-md-2 col-lg-1 col-xl-1">
		<br /><?= Html::button('<i class="fa fa-plus" aria-hidden="true"></i> Add', ['class' => 'btn btn-primary','id'=>'addEvent', 'disabled' => empty($newEventOptions)]) ?>
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

<div id="eventRegistrationModal" class="event-register-modal">
	<div class="event-register-modal-content">
		<span class="event-register-close" role="button" aria-label="Close">&times;</span>
		<?php $registrationForm = ActiveForm::begin([
			'id' => 'Event-Reg-form',
			'action' => ['/events/reg'],
			'method' => 'post',
			'options' => [
				'onsubmit' => 'return jsReg()',
				'data-pjax' => 0,
			],
		]); ?>
		<?= Html::hiddenInput('event_id', '', ['id' => 'event_id']) ?>
		<p id="event_name">Register for:</p>
		<p id="event_notes"></p>
		<div class="row">
			<div class="col-xs-6 col-sm-3">
				<?= $registrationForm->field($eventAttendee, 'ea_badge')->textInput() ?>
				<p id="badge_name"></p>
			</div>
			<div id="by_name" style="display:none;">
				<div class="col-xs-12 col-sm-1"><h2>OR</h2></div>
				<div class="col-xs-6 col-sm-3 col-md-2"><?= $registrationForm->field($eventAttendee, 'ea_f_name')->textInput() ?></div>
				<div class="col-xs-6 col-sm-3 col-md-2"><?= $registrationForm->field($eventAttendee, 'ea_l_name')->textInput() ?></div>
				<div class="col-xs-6 col-sm-3 col-md-2" id="e_serial" style="display:none;"><?= $registrationForm->field($eventAttendee, 'ea_wb_serial')->textInput() ?></div>
			</div>
		</div>
		<div class="col-xs-12" id="waver" style="display:none;"><?php yii::$app->controller->getWaver(); ?></div>
		<div class="row" id="iagree" style="display:none;">
			<div class="col-xs-12">
				<input type="checkbox" id="terms" name="terms" onclick="toggleSubmit()">
				<label for="terms">
					I understand the above Conditions and agree to the
					<a href="<?= Html::encode(yii::$app->params['wp_site'].'/waiver') ?>" target="_blank" rel="noopener">Waiver of Liability</a>.
				</label>
			</div>
		</div>
		<div class="row">
			<div id="reg_notes"></div>
			<div class="col-xs-3">
				<button id="reg_button" type="submit" class="btn btn-success">Register <i class="fa fa-child"></i></button>
			</div>
		</div>
		<?php ActiveForm::end(); ?>
	</div>
</div>
<?php Pjax::end(); ?>
</div>
</div>

<style>
.event-register-modal {
	display: none;
	position: fixed;
	z-index: 1050;
	padding-top: 100px;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	overflow: auto;
	background-color: rgba(0, 0, 0, 0.4);
}
.event-register-modal-content {
	background-color: #fefefe;
	margin: auto;
	padding: 20px;
	border: 1px solid #888;
	width: 80%;
}
.event-register-close {
	color: #aaa;
	float: right;
	font-size: 28px;
	font-weight: bold;
	cursor: pointer;
}
</style>

<script>
const missingEvents = <?= Json::htmlEncode($missingEvents) ?>;
const registrationModal = document.getElementById('eventRegistrationModal');
const registrationButton = document.getElementById('reg_button');
const registrationAgreement = document.getElementById('iagree');
const registrationUrl = <?= Json::htmlEncode(Url::to(['/events/reg'])) ?>;

$('#addEvent').on('click', function() {
	const eventId = $('#new_event_id').val();
	const event = missingEvents[eventId];
	if (!event) {
		window.alert('Select an event first.');
		return;
	}
	jsRegister(eventId, event.club_name, event.event_name, event.event_status_name,
		event.allow_guests, event.track_wristbands, event.is_volunteer);
});

function jsRegister(id, clubName, eventName, eventStatus, allowGuests, trackWristbands, isVolunteer) {
	registrationModal.style.display = 'block';
	document.getElementById('terms').checked = false;
	registrationButton.disabled = false;
	registrationAgreement.style.display = 'none';
	$('#event_att-ea_badge, #event_att-ea_f_name, #event_att-ea_l_name, #event_att-ea_wb_serial').val('');
	$('#reg_notes, #badge_name').empty();
	document.getElementById('event_id').value = id;

	if (Number(isVolunteer) === 1) {
		$('#by_name, #e_serial, #waver').hide();
		$('#event_notes').text('AGC Volunteer Events are Range Members only.');
	} else if (Number(allowGuests) === 1) {
		$('#by_name, #waver').show();
		$('#e_serial').toggle(Number(trackWristbands) === 1);
		$('#event_notes').html('Enter Badge Number <b>or</b> First and Last Name.');
	} else {
		$('#by_name, #e_serial, #waver').hide();
		$('#event_notes').text('Enter Badge Number.');
	}
	$('#event_name').text('Register for: ' + clubName + ' ' + eventName + ' (' + eventStatus + ')');
}

function toggleSubmit() {
	registrationButton.disabled = !document.getElementById('terms').checked;
}

$('.event-register-close').on('click', function() {
	registrationModal.style.display = 'none';
});

window.addEventListener('click', function(event) {
	if (event.target === registrationModal) {
		registrationModal.style.display = 'none';
	}
});

$('#event_att-ea_badge').on('input', function() {
	$('#event_att-ea_f_name, #event_att-ea_l_name, #event_att-ea_wb_serial').val('');
	const badgeNumber = $(this).val();
	if (badgeNumber && badgeNumber !== '0') {
		$('#badge_name').text('Searching');
		$.getJSON(<?= Json::htmlEncode(Url::to(['/badges/get-badge-name'])) ?>, {
			badge_number: badgeNumber,
		}).done(function(response) {
			if (response.success && !response.isExpired) {
				$('#badge_name').text(response.first_name + ' ' + response.last_name);
			} else {
				$('#badge_name').text(response.isExpired ? 'No Active Member Found' : 'Valid Badge holder not found');
			}
		}).fail(function() {
			$('#badge_name').text('Unable to look up badge.');
		});
	} else {
		$('#badge_name').empty();
	}
});

$('#event_att-ea_f_name, #event_att-ea_l_name').on('input', function() {
	$('#badge_name, #event_att-ea_badge').val('');
	registrationAgreement.style.display = 'block';
	registrationButton.disabled = !document.getElementById('terms').checked;
});

$('#event_att-ea_wb_serial').on('input', function() {
	$('#event_att-ea_badge').val('');
});

function jsReg() {
	const eventId = document.getElementById('event_id').value;
	const badge = $('#event_att-ea_badge').val();
	const actionParams = {id: eventId};
	if (Number(badge) > 1) {
		actionParams.badge = badge;
	} else {
		const firstName = $('#event_att-ea_f_name').val().trim();
		const lastName = $('#event_att-ea_l_name').val().trim();
		if (!firstName || !lastName) {
			window.alert('Please check that first and last name are specified.');
			return false;
		}
		actionParams.f_name = firstName;
		actionParams.l_name = lastName;
		actionParams.e_wb = $('#event_att-ea_wb_serial').val().trim();
	}
	$('#Event-Reg-form').attr('action', registrationUrl + '?' + $.param(actionParams));
	return Boolean(eventId);
}
</script>
