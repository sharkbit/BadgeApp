<?php

use backend\models\AgcCal;
use backend\models\Badges;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\cal_calendar */

$this->title = $model->event_name;
$this->params['breadcrumbs'][] = ['label' => 'Calendar', 'url' => '/calendar/list'];
$this->params['breadcrumbs'][] = $this->title;
$this->params['hideHomeLink'] = true;
?>
<div class="ext_calendar-view">
	<div class="row" >
		<div class="col-xs-12">

			<h3>Calendar Event Details </h3>

			<div class="col-xs-12 col-md-8">
				<div class="block-badge-view">

				   <?= DetailView::widget([
					'model' => $model,
					'attributes' => [
						
						[	'attribute'=>'club_id',
							'value'=>function($model) { return $model->clubs->club_name; }
						],
						'event_name',
						[	'attribute'=>'facility_id',
							'value'=>function($model) { return (New AgcCal)->getAgcFacility_Names($model->facility_id); }
						],
						[	'attribute'=>'Time',
							'value'=>function($model) {
								$startTime = !empty($model->cal_start_time) ? strtotime($model->cal_start_time) : false;
								$endTime = !empty($model->cal_end_time) ? strtotime($model->cal_end_time) : false;
								$timeRange = $startTime !== false && $endTime !== false
									? date('h:i A', $startTime).' - '.date('h:i A', $endTime) : '';
								return $model->event_date.($timeRange !== '' ? ' ('.$timeRange.')' : '');
							}								],
						[	'attribute' => 'poc_badge',
							'label' => 'Calendar POC',
							'value'=>function($model) {
								if((!empty($model->poc_badge)) && (filter_var($model->poc_badge, FILTER_VALIDATE_INT))) {
									$poc_badge_search = Badges::find()->where(['badge_number' => $model->poc_badge])->one();
									if(!empty($poc_badge_search)) {
										return $poc_badge_search->first_name . ' ' . $poc_badge_search->last_name;
									}
								}
								return '';
							}
						],
						[	'attribute' => 'cal_inst',
							'label' => 'Event Director',
							'value'=>function($model) {
								if((!empty($model->cal_inst)) && (filter_var($model->cal_inst, FILTER_VALIDATE_INT))) {
									$inst_badge_search = Badges::find()->where(['badge_number' => $model->cal_inst])->one();
									if(!empty($inst_badge_search)) {
										return $inst_badge_search->first_name . ' ' . $inst_badge_search->last_name;
									}
								}
								return '';
							}
						],
						[	'attribute'=>'event_status_id',
							'value'=>function($model) { return $model->agcEventStatus->name; },
						],
						[	'attribute'=>'range_status_id',
							'value'=>function($model) { return $model->agcRangeStatus->name; },
						],
					],
				]) ?>

				</div>
			</div>
		</div>
	</div>
</div>
