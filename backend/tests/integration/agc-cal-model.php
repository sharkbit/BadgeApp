<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

$root = dirname(__DIR__, 3);
require $root . '/vendor/autoload.php';
require $root . '/vendor/yiisoft/yii2/Yii.php';
require_once $root . '/backend/tests/_schema-bootstrap.php';
Yii::setAlias('@common', $root . '/common');
Yii::setAlias('@backend', $root . '/backend');

$config = require $root . '/common/config/test-local.php';
$app = new yii\console\Application([
	'id' => 'agc-cal-model-tests',
	'basePath' => $root,
	'vendorPath' => $root . '/vendor',
	'components' => [
		'db' => $config['components']['db'],
	],
]);
$db = Yii::$app->db;

$passed = 0;
$assert = static function ($condition, $message) use (&$passed) {
	if (!$condition) {
		throw new RuntimeException($message);
	}

	$passed++;
	echo "PASS: {$message}\n";
};
$insertEvent = static function ($name, array $overrides = []) use ($db) {
	$db->createCommand()->insert('cal_calendar', array_merge([
		'event_date' => date('Y-m-d'),
		'club_id' => 0,
		'facility_id' => '[]',
		'event_name' => $name,
		'key_words' => '',
		'poc_badge' => 0,
		'cal_inst' => 0,
	], $overrides))->execute();
	return (int)$db->getLastInsertID();
};

$suffix = strtoupper(bin2hex(random_bytes(5)));
$eventIds = [];
$facilityIds = [];
$clubId = null;
$eventStatusId = null;
$rangeStatusId = null;

try {
	$model = new \backend\models\AgcCal();
	$assert(
		\backend\models\AgcCal::tableName() === 'cal_calendar',
		'AgcCal maps to cal_calendar.'
	);
	$assert(
		!$model->validate()
			&& count(array_intersect(
				['event_name', 'event_date', 'poc_badge', 'cal_inst'],
				array_keys($model->getErrors())
			)) === 4,
		'AgcCal requires event name, event date, POC badge, and instructor.'
	);

	$model->setAttributes([
		'event_name' => 'Valid model test',
		'event_date' => date('Y-m-d'),
		'poc_badge' => 0,
		'cal_inst' => 0,
	], false);
	$assert($model->validate(), 'AgcCal accepts a valid event with zero badge numbers.');

	$model->cal_inst = -1;
	$assert(
		!$model->validate() && $model->hasErrors('cal_inst'),
		'AgcCal rejects a negative instructor badge number.'
	);

	$model->cal_inst = 1;
	$model->event_status_id = 'not-an-integer';
	$assert(
		!$model->validate() && $model->hasErrors('event_status_id'),
		'AgcCal rejects non-integer event status values.'
	);

	$firstConflictId = $insertEvent('AgcCal model ' . $suffix . ' conflict one');
	$secondConflictId = $insertEvent('AgcCal model ' . $suffix . ' conflict two');
	$untouchedId = $insertEvent('AgcCal model ' . $suffix . ' untouched');
	$eventIds = [$firstConflictId, $secondConflictId, $untouchedId];
	$assert(
		\backend\models\AgcCal::markConflicts([]) === 0,
		'markConflicts returns zero for an empty ID list.'
	);
	$affected = \backend\models\AgcCal::markConflicts([
		$firstConflictId,
		(string)$secondConflictId,
		$firstConflictId,
		0,
		-1,
		'not-an-id',
		2147483000,
	]);
	$conflictRows = \backend\models\AgcCal::find()
		->select(['calendar_id', 'conflict'])
		->where(['calendar_id' => $eventIds])
		->asArray()
		->all();
	$conflictValues = array_column($conflictRows, 'conflict', 'calendar_id');
	$assert(
		$affected === 2
			&& (int)$conflictValues[$firstConflictId] === 1
			&& (int)$conflictValues[$secondConflictId] === 1
			&& (int)$conflictValues[$untouchedId] === 0,
		'markConflicts marks only unique, positive IDs for existing events.'
	);
	$assert(
		\backend\models\AgcCal::markConflicts([$firstConflictId, $secondConflictId]) === 0,
		'markConflicts reports zero when the requested events are already marked.'
	);

	$db->createCommand()->insert('cal_facilities', [
		'name' => 'Z Model Facility ' . $suffix,
		'active' => 1,
	])->execute();
	$facilityIds[] = (int)$db->getLastInsertID();
	$db->createCommand()->insert('cal_facilities', [
		'name' => 'A Model Facility ' . $suffix,
		'active' => 1,
	])->execute();
	$facilityIds[] = (int)$db->getLastInsertID();

	$calendarModel = new \backend\models\AgcCal();
	$expectedFacilityNames = 'A Model Facility ' . $suffix . ', Z Model Facility ' . $suffix;
	$assert(
		$calendarModel->getAgcFacility_Names($facilityIds) === $expectedFacilityNames,
		'getAgcFacility_Names sorts matching facility names alphabetically.'
	);
	$assert(
		$calendarModel->getAgcFacility_Names(json_encode($facilityIds)) === $expectedFacilityNames,
		'getAgcFacility_Names accepts JSON-encoded facility IDs.'
	);
	$assert(
		$calendarModel->getAgcFacility_Names([]) === '',
		'getAgcFacility_Names returns an empty string when no facility IDs are supplied.'
	);

	$db->createCommand()->insert('clubs', [
		'club_name' => 'AgcCal Model Club ' . $suffix,
		'short_name' => 'AC' . substr($suffix, 0, 5),
		'status' => 0,
		'is_club' => 1,
	])->execute();
	$clubId = (int)$db->getLastInsertID();
	$db->createCommand()->insert('cal_event_status', [
		'name' => 'AgcCal Model Event Status ' . $suffix,
		'active' => 1,
	])->execute();
	$eventStatusId = (int)$db->getLastInsertID();
	$db->createCommand()->insert('cal_range_status', [
		'name' => 'AgcCal Model Range Status ' . $suffix,
		'active' => 1,
	])->execute();
	$rangeStatusId = (int)$db->getLastInsertID();

	$relationEventId = $insertEvent('AgcCal model ' . $suffix . ' relations', [
		'club_id' => $clubId,
		'event_status_id' => $eventStatusId,
		'range_status_id' => $rangeStatusId,
	]);
	$eventIds[] = $relationEventId;
	$relations = \backend\models\AgcCal::findOne($relationEventId);
	$assert(
		$relations->clubs->club_name === 'AgcCal Model Club ' . $suffix,
		'AgcCal resolves its related club.'
	);
	$assert(
		$relations->agcEventStatus->name === 'AgcCal Model Event Status ' . $suffix,
		'AgcCal resolves its related event status.'
	);
	$assert(
		$relations->agcRangeStatus->name === 'AgcCal Model Range Status ' . $suffix,
		'AgcCal resolves its related range status.'
	);

	$nextYearEventId = $insertEvent('AgcCal model ' . $suffix . ' next year', [
		'event_date' => ((int)date('Y') + 1) . '-01-01',
	]);
	$currentYearEventId = $insertEvent('AgcCal model ' . $suffix . ' current year', [
		'event_date' => date('Y') . '-12-30',
	]);
	$eventIds[] = $nextYearEventId;
	$eventIds[] = $currentYearEventId;
	$publicationModel = new \backend\models\AgcCal();
	$assert(
		$publicationModel->getIsPublished($nextYearEventId) === true
			&& $publicationModel->getIsPublished($currentYearEventId) === false,
		'getIsPublished identifies next-year events as published and current-year events as unpublished.'
	);
} finally {
	if ($eventIds) {
		$db->createCommand()->delete('cal_calendar', ['calendar_id' => $eventIds])->execute();
	}
	if ($facilityIds) {
		$db->createCommand()->delete('cal_facilities', ['facility_id' => $facilityIds])->execute();
	}
	if ($clubId) {
		$db->createCommand()->delete('clubs', ['club_id' => $clubId])->execute();
	}
	if ($eventStatusId) {
		$db->createCommand()->delete('cal_event_status', ['event_status_id' => $eventStatusId])->execute();
	}
	if ($rangeStatusId) {
		$db->createCommand()->delete('cal_range_status', ['range_status_id' => $rangeStatusId])->execute();
	}
}

echo "Passed {$passed} AgcCal model assertions.\n";
