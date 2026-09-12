<?php

use backend\models\AgcCal;
use backend\models\clubs;
use backend\models\MembershipStatus;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model backend\models\Badges */

$this->title = $model->event_name;
$this->params['breadcrumbs'][] = ['label' => 'Calendar', 'url' => '/calendar/list'];
$this->params['breadcrumbs'][] = $this->title;
$this->params['hideHomeLink'] = true;
?>
<div class="badges-view">
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
							'value'=>function($model) { return $model->event_date . " (". date("h:i A",strtotime($model->cal_start_time)) .' - '.date("h:i A",strtotime($model->end_time)).')' ; }
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
