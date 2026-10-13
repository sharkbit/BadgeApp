<?php

use backend\models\agcFacility;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

$facilityNamesById = ArrayHelper::map(
	agcFacility::find()->select(['facility_id', 'name'])->asArray()->all(),
	'facility_id',
	'name'
);
$Req_Lanes = (new agcFacility)->getFacilRequiresLanes();

?>
<style>
body {
  font-size: 12px;
}
.grid-row-border td {
    border-bottom: 1px solid #dee2e6 !important; /* Adjust color/thickness here */
}
</style>
<div class="container" >
<div class="row">
	<div class="col-xs-12" style="width: 50%; margin: 0 auto;">
		<h2> Events For  <?=date("Y-m-d") ?> </h2>
	</div>
</div>

<?php
$gridColumns = [
	[	'attribute'=>'cal_start_time',
		'headerOptions' => ['style' => 'width:7%'],
		'label'=>'Start',
		'value' => function ($model) { return date('H:i', strtotime($model->cal_start_time)); }
	],
	[	
		'headerOptions' => ['style' => 'width:7%'],
		'label'=>'Showed Up',
		'value' => function ($model) { return ''; }
	],
	[	'attribute' => 'club_name',
		'headerOptions' => ['style' => 'width:25%'],
		'value'=> function($model) {
			return $model['club_name'].' ('.$model['short_name'].')';
		},
	],
	[	'attribute'=>'event_name',
		'contentOptions' =>['style' => 'overflow: auto; word-wrap: break-word; white-space: normal;'],
		'headerOptions' => ['style' => 'width:25%'],
	],
	[	'attribute'=>'Event Director',
		'headerOptions' => ['style' => 'width:15%'],
		'value'=>function($model) {
			if(!empty($model->cal_inst)) {
				$poc_name = yii::$app->controller->decodeBadgeName((int)$model->cal_inst);
				if (!empty($poc_name)) { return $poc_name; } else { return $model->cal_inst;}
			} else {
				return yii::$app->controller->decodeBadgeName((int)$model->poc_badge);
			}
		},
	],
	[	
		'attribute'=>'facility_id',
		'contentOptions' => ['style' => 'white-space:pre-line;'],
		'headerOptions' => ['style' => 'width:20%'],
		'label' => 'Facility',
		'value'=>function($model) use ($facilityNamesById,$Req_Lanes) {
			$facilityIds = json_decode($model->facility_id, true);
			$lanes_req = json_decode($model->lanes_req, true);
			if (!is_array($facilityIds)) {
				return '';
			}
			$facilityNames = [];
			foreach ($facilityIds as $facilityId) {
				if (isset($facilityNamesById[$facilityId])) {
					if (in_array($facilityId,array_column($Req_Lanes,'facility_id'))) {
						$lanesRequested = ' ('.$lanes_req[$facilityId].')';
					} else {
						$lanesRequested = '';
					}
					$facilityNames[] = $facilityNamesById[$facilityId].$lanesRequested;
				}
			}
			sort($facilityNames);
			return implode(', ', $facilityNames);
		},
	],
]; 
?>

<div class="row">
	<div class="col-xs-12">
		<?php
		   echo GridView::widget([
				'dataProvider' => $dataProvider,
				'columns' => $gridColumns,
				'rowOptions' => ['class' => 'grid-row-border'],
			]);
		?>
	</div>
</div>

</div>