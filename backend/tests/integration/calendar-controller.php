<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

$root = dirname(__DIR__, 3);
require $root . '/vendor/autoload.php';
require $root . '/vendor/yiisoft/yii2/Yii.php';
require_once $root . '/backend/tests/_schema-bootstrap.php';
Yii::setAlias('@common', $root . '/common');
Yii::setAlias('@backend', $root . '/backend');

class CalendarControllerTestDouble extends \backend\controllers\CalendarController
{
	public function getNowTime($offset = null)
	{
		return date('Y-m-d H:i:s');
	}

	public function createCalLog($isEnabled, $username, $activity)
	{
		return null;
	}

	public function hasPermission($event)
	{
		return true;
	}

	public function getCurrentUrl()
	{
		return [
			'controllerId' => 'calendar',
			'actionId' => 'rss',
			'requestUrl' => '/calendar/rss',
		];
	}

	public function render($view, $params = [])
	{
		return ['view' => $view, 'params' => $params];
	}
}

$config = require $root . '/common/config/test-local.php';
$app = new yii\web\Application([
	'id' => 'calendar-controller-tests',
	'basePath' => $root,
	'vendorPath' => $root . '/vendor',
	'components' => [
		'db' => $config['components']['db'],
		'request' => [
			'cookieValidationKey' => 'calendar-controller-tests',
			'enableCsrfValidation' => false,
		],
		'response' => ['class' => yii\web\Response::class],
		'session' => ['class' => yii\web\Session::class],
		'urlManager' => [
			'class' => yii\web\UrlManager::class,
			'hostInfo' => 'https://calendar.test',
			'baseUrl' => '',
			'enablePrettyUrl' => true,
			'showScriptName' => false,
		],
	],
	'params' => ['timeZone' => 'UTC'],
]);

$db = Yii::$app->db;
$controller = new CalendarControllerTestDouble('calendar', $app);
Yii::$app->controller = $controller;
Yii::$app->session->open();
$_SESSION['user'] = 'Calendar Test';
$_SESSION['privilege'] = [1];

$passed = 0;
$assert = static function ($condition, $message) use (&$passed) {
	if (!$condition) {
		throw new RuntimeException($message);
	}

	$passed++;
	echo "PASS: {$message}\n";
};
$insertEvent = static function ($name, array $overrides = []) use ($db) {
	$attributes = array_merge([
		'event_date' => date('Y-m-d'),
		'cal_start_time' => '09:00:00',
		'cal_end_time' => '10:00:00',
		'club_id' => 0,
		'facility_id' => '[]',
		'event_name' => $name,
		'key_words' => '',
		'event_status_id' => 1,
		'range_status_id' => 1,
		'poc_badge' => 0,
		'cal_inst' => 0,
		'remarks' => '[]',
	], $overrides);
	$db->createCommand()->insert('cal_calendar', $attributes)->execute();
	return (int)$db->getLastInsertID();
};

$suffix = strtoupper(bin2hex(random_bytes(5)));
$eventPrefix = 'CalendarControllerTest-' . $suffix . '-';
$eventIds = [];
$legacyIds = [];
$clubId = null;
$facilityId = null;
$statusId = 1900;

try {
	$clubId = $db->createCommand()->insert('clubs', [
		'club_name' => 'Calendar Test ' . $suffix,
		'short_name' => 'CT' . substr($suffix, 0, 5),
		'status' => 0,
		'is_club' => 1,
	])->execute();
	$clubId = (int)$db->getLastInsertID();

	$facilityId = $db->createCommand()->insert('cal_facilities', [
		'name' => 'Calendar Test ' . $suffix,
		'available_lanes' => 0,
		'active' => 1,
	])->execute();
	$facilityId = (int)$db->getLastInsertID();

	$db->createCommand()->insert('cal_event_status', [
		'event_status_id' => $statusId,
		'name' => 'Calendar Test ' . $suffix,
		'active' => 1,
	])->execute();

	$_SESSION['badge_number'] = 0;
	$createName = $eventPrefix . 'created';
	$createData = [
		'AgcCal' => [
			'event_name' => $createName,
			'event_date' => date('Y-m-d', strtotime('+3 days')),
			'cal_start_time' => '11:00',
			'cal_end_time' => '12:00',
			'club_id' => $clubId,
			'facility_id' => [(string)$facilityId],
			'event_status_id' => 1,
			'range_status_id' => 1,
			'poc_badge' => 0,
			'cal_inst' => 0,
			'key_words' => 'controller-test',
		],
	];
	Yii::$app->request->setBodyParams($createData);
	$_POST = $createData;
	$controller->actionCreate();
	$createdId = (int)$db->createCommand(
		'SELECT calendar_id FROM cal_calendar WHERE event_name = :event_name',
		[':event_name' => $createName]
	)->queryScalar();
	$assert(
		$createdId > 0
			&& \backend\models\AgcCal::findOne($createdId)->facility_id === '[' . $facilityId . ']',
		'actionCreate saves a single calendar event with normalized facility IDs.'
	);
	$eventIds[] = $createdId;

	Yii::$app->request->setBodyParams([]);
	$_POST = [];
	$updatedView = $controller->actionUpdate($createdId);
	$assert(
		$updatedView['view'] === 'update'
			&& $updatedView['params']['model']->calendar_id == $createdId,
		'actionUpdate renders the selected calendar event.'
	);
	$itemView = $controller->actionViewitem($createdId);
	$assert(
		$itemView['view'] === 'viewitem'
			&& $itemView['params']['model']->calendar_id == $createdId,
		'actionViewitem renders the selected calendar event.'
	);

	$invalidRecurringName = $eventPrefix . 'invalid-recurring';
	$invalidRecurringData = [
		'AgcCal' => [
			'event_name' => $invalidRecurringName,
			'event_date' => date('Y-m-d', strtotime('+3 days')),
			'cal_start_time' => '11:00',
			'cal_end_time' => '12:00',
			'club_id' => $clubId,
			'facility_id' => [(string)$facilityId],
			'event_status_id' => 1,
			'range_status_id' => 1,
			'poc_badge' => 0,
			'cal_inst' => 0,
			'key_words' => 'controller-test',
			'recur_every' => 1,
			'recurrent_start_date' => '01-01',
			'recurrent_end_date' => '01-07',
		],
	];
	Yii::$app->request->setBodyParams($invalidRecurringData);
	$_POST = $invalidRecurringData;
	$controller->actionCreate();
	$invalidRecurringCount = (int)$db->createCommand(
		'SELECT COUNT(*) FROM cal_calendar WHERE event_name = :event_name',
		[':event_name' => $invalidRecurringName]
	)->queryScalar();
	$assert(
		$invalidRecurringCount === 0,
		'actionCreate rejects recurring events without a valid recurrence pattern.'
	);
	Yii::$app->request->setBodyParams([]);
	$_POST = [];

	$todayId = $insertEvent($eventPrefix . 'rso-today');
	$eventIds[] = $todayId;
	Yii::$app->request->setBodyParams(['comment' => '  RSO update  ']);
	Yii::$app->response->statusCode = 200;
	$result = $controller->actionRsoComment($todayId);
	$today = \backend\models\AgcCal::findOne($todayId);
	$remarks = json_decode($today->remarks, true);
	$assert(
		$result === ['success' => true, 'comment' => 'RSO update']
			&& ($remarks[0]['data'] ?? null) === 'RSO update',
		'actionRsoComment trims comments and persists event-day remarks.'
	);

	Yii::$app->request->setBodyParams(['comment' => '']);
	Yii::$app->response->statusCode = 200;
	$result = $controller->actionRsoComment('bad-id');
	$assert(
		Yii::$app->response->statusCode === 400 && $result['success'] === false,
		'actionRsoComment rejects invalid event IDs.'
	);

	Yii::$app->request->setBodyParams(['comment' => '']);
	Yii::$app->response->statusCode = 200;
	$result = $controller->actionRsoComment($todayId);
	$assert(
		Yii::$app->response->statusCode === 400 && $result['message'] === 'Comment cannot be empty.',
		'actionRsoComment rejects empty comments.'
	);

	$oldDateId = $insertEvent($eventPrefix . 'rso-old', [
		'event_date' => date('Y-m-d', strtotime('-1 day')),
	]);
	$eventIds[] = $oldDateId;
	Yii::$app->request->setBodyParams(['comment' => 'Too late']);
	Yii::$app->response->statusCode = 200;
	$result = $controller->actionRsoComment($oldDateId);
	$assert(
		Yii::$app->response->statusCode === 403 && $result['success'] === false,
		'actionRsoComment rejects comments outside the event date.'
	);

	$deletedId = $insertEvent($eventPrefix . 'delete');
	$eventIds[] = $deletedId;
	$controller->actionDelete($deletedId);
	$assert(
		(int)\backend\models\AgcCal::findOne($deletedId)->deleted === 1,
		'actionDelete soft-deletes a single calendar event.'
	);

	$conflictActionId = $insertEvent($eventPrefix . 'conflict-delete');
	$eventIds[] = $conflictActionId;
	Yii::$app->request->setBodyParams(['selection' => [$conflictActionId]]);
	$_POST = ['selection' => [$conflictActionId]];
	$controller->actionConflict('index');
	$assert(
		(int)\backend\models\AgcCal::findOne($conflictActionId)->deleted === 1,
		'actionConflict soft-deletes selected events.'
	);
	Yii::$app->request->setBodyParams([]);
	$_POST = [];

	$masterId = $insertEvent($eventPrefix . 'recurring-master', [
		'recur_every' => 1,
	]);
	$eventIds[] = $masterId;
	$db->createCommand()->update('cal_calendar', ['recurrent_calendar_id' => $masterId], [
		'calendar_id' => $masterId,
	])->execute();
	$occurrenceId = $insertEvent($eventPrefix . 'recurring-occurrence', [
		'event_date' => date('Y-m-d', strtotime('+2 days')),
		'recur_every' => 1,
		'recurrent_calendar_id' => $masterId,
	]);
	$eventIds[] = $occurrenceId;
	$controller->actionDelete($masterId, 'r');
	$assert(
		(int)\backend\models\AgcCal::findOne($masterId)->deleted === 1
			&& (int)\backend\models\AgcCal::findOne($occurrenceId)->deleted === 1,
		'actionDelete marks the recurring series and its future occurrences as deleted.'
	);

	$showedId = $insertEvent($eventPrefix . 'showed');
	$eventIds[] = $showedId;
	$controller->actionShowed($showedId, 1);
	$assert(
		(int)\backend\models\AgcCal::findOne($showedId)->showed_up === 1,
		'actionShowed records event attendance.'
	);

	$clubModelId = $clubId;
	$_SESSION['privilege'] = [1];
	$eventTypes = $controller->actionGetEventTypes($clubModelId, true);
	$assert(
		isset($eventTypes[$statusId]) && $eventTypes[$statusId] === 'Calendar Test ' . $suffix,
		'actionGetEventTypes returns active event types for administrators.'
	);

	$_SESSION['privilege'] = [4];
	$eventTypes = $controller->actionGetEventTypes($clubModelId, true);
	$assert(
		!isset($eventTypes[$statusId]),
		'actionGetEventTypes excludes restricted event types for regular users.'
	);
	$_SESSION['privilege'] = [1];

	$invalidFacilityWasRejected = false;
	try {
		$controller->actionOpenRange(date('Y-m-d'), '09:00', '10:00', 'invalid', '{}', 0, '', 1, true);
	} catch (\yii\web\BadRequestHttpException $exception) {
		$invalidFacilityWasRejected = true;
	}
	$assert($invalidFacilityWasRejected, 'actionOpenRange rejects requests without a valid facility.');

	$open = $controller->actionOpenRange(
		date('Y-m-d'),
		'09:00',
		'10:00',
		(string)$facilityId,
		'{}',
		0,
		'',
		1,
		true
	);
	$assert($open === true, 'actionOpenRange reports an unbooked facility as available.');

	$busyId = $insertEvent($eventPrefix . 'busy', [
		'event_date' => date('Y-m-d', strtotime('+1 day')),
		'cal_start_time' => '09:00:00',
		'cal_end_time' => '10:00:00',
		'facility_id' => json_encode([$facilityId]),
	]);
	$eventIds[] = $busyId;
	$busy = $controller->actionOpenRange(
		date('Y-m-d', strtotime('+1 day')),
		'09:00',
		'10:00',
		json_encode([$facilityId]),
		'{}',
		0,
		'',
		1,
		true
	);
	$assert($busy === false, 'actionOpenRange detects overlapping bookings for the same facility.');

	$laneFacilityId = (int)$db->createCommand()->insert('cal_facilities', [
		'name' => 'Calendar Lane Test ' . $suffix,
		'available_lanes' => 4,
		'active' => 1,
	])->execute();
	$laneFacilityId = (int)$db->getLastInsertID();
	$laneBookingId = $insertEvent($eventPrefix . 'lane-booking', [
		'event_date' => date('Y-m-d', strtotime('+2 days')),
		'cal_start_time' => '09:00:00',
		'cal_end_time' => '10:00:00',
		'facility_id' => json_encode([$laneFacilityId]),
		'lanes_req' => json_encode([$laneFacilityId => 3]),
	]);
	$eventIds[] = $laneBookingId;
	$laneRequest = $controller->actionOpenRange(
		date('Y-m-d', strtotime('+2 days')),
		'09:00',
		'10:00',
		[$laneFacilityId],
		json_encode([$laneFacilityId => 2]),
		0,
		'',
		1,
		true
	);
	$assert($laneRequest === false, 'actionOpenRange rejects lane requests exceeding capacity.');
	$laneRequest = $controller->actionOpenRange(
		date('Y-m-d', strtotime('+2 days')),
		'09:00',
		'10:00',
		[$laneFacilityId],
		json_encode([$laneFacilityId => 1]),
		0,
		'',
		1,
		true
	);
	$assert($laneRequest === true, 'actionOpenRange permits lane requests within capacity.');

	Yii::$app->request->setQueryParams(['year' => '2028', 'month' => '13']);
	Yii::$app->response->statusCode = 200;
	$result = $controller->actionRecheckFutureConflicts();
	$assert(
		Yii::$app->response->statusCode === 400 && $result['success'] === false,
		'actionRecheckFutureConflicts rejects invalid calendar parameters.'
	);

	Yii::$app->request->setQueryParams(['year' => '2098', 'month' => '2', 'span' => '2']);
	Yii::$app->response->statusCode = 200;
	$result = $controller->actionRecheckFutureConflicts();
	$assert(
		$result['success'] === true
			&& $result['Search Start'] === '2098-02-01'
			&& $result['Search End'] === '2098-04-01'
			&& $result['checked'] === 0,
		'actionRecheckFutureConflicts returns the requested month range when no events match.'
	);
	$conflictCheckId = $insertEvent($eventPrefix . 'recheck', [
		'event_date' => '2098-02-15',
		'facility_id' => json_encode([$facilityId]),
		'conflict' => 1,
	]);
	$eventIds[] = $conflictCheckId;
	$result = $controller->actionRecheckFutureConflicts();
	$assert(
		$result['checked'] === 1
			&& $result['clearedConflicts'] === 1
			&& (int)\backend\models\AgcCal::findOne($conflictCheckId)->conflict === 0,
		'actionRecheckFutureConflicts clears stale conflict flags.'
	);

	$emptyMerge = $controller->runMergeOldEvents([], new stdClass());
	$assert(
		$emptyMerge === ['success' => true, 'merged' => [], 'skipped' => [], 'errors' => []],
		'runMergeOldEvents treats an empty selection as a successful no-op.'
	);

	Yii::$app->response->statusCode = 200;
	$invalidMerge = $controller->runMergeOldEvents(['not-an-id'], new stdClass());
	$assert(
		Yii::$app->response->statusCode === 400 && $invalidMerge['success'] === false,
		'runMergeOldEvents rejects malformed event selections.'
	);

	$legacyEventId = null;
	$db->createCommand()->insert('events_old', [
		'e_name' => $eventPrefix . 'legacy',
		'e_date' => date('Y-m-d', strtotime('+2 days')),
		'e_poc' => 0,
		'sponsor' => $clubId,
		'e_status' => 'Test',
		'e_type' => 'Test',
		'e_hours' => 1,
		'e_inst' => '',
	])->execute();
	$legacyEventId = (int)$db->getLastInsertID();
	$legacyIds[] = $legacyEventId;
	$defaults = (object)[
		'facility_id' => json_encode([$facilityId]),
		'lanes_req' => '{}',
		'event_status_id' => 1,
	];
	$merged = $controller->runMergeOldEvents([$legacyEventId, $legacyEventId], $defaults);
	$calendarId = (int)$db->createCommand(
		'SELECT calendar_id FROM events_old WHERE e_id = :event_id',
		[':event_id' => $legacyEventId]
	)->queryScalar();
	$assert(
		$merged['success'] === true
			&& count($merged['merged']) === 1
			&& $calendarId > 0
			&& (int)\backend\models\AgcCal::findOne($calendarId)->poc_badge === 0,
		'runMergeOldEvents creates one calendar event and links the legacy record.'
	);
	$eventIds[] = $calendarId;

	$db->createCommand()->insert('events_old', [
		'e_name' => $eventPrefix . 'legacy-linked',
		'e_date' => date('Y-m-d'),
		'e_poc' => 0,
		'calendar_id' => $calendarId,
		'e_status' => 'Test',
		'e_type' => 'Test',
	])->execute();
	$linkedLegacyId = (int)$db->getLastInsertID();
	$legacyIds[] = $linkedLegacyId;
	$skipped = $controller->runMergeOldEvents([$linkedLegacyId], $defaults);
	$assert(
		$skipped['success'] === true && count($skipped['skipped']) === 1 && empty($skipped['merged']),
		'runMergeOldEvents skips legacy records that are already linked.'
	);

	$missing = $controller->runMergeOldEvents([2147483000], $defaults);
	$assert(
		$missing['success'] === false && ($missing['errors'][0]['message'] ?? '') === 'Old event not found.',
		'runMergeOldEvents reports selected legacy records that no longer exist.'
	);

	$startYear = (int)date('Y') + 1;
	$dates = $controller->getEvents(
		$startYear . '-01-01',
		$startYear . '-01-07',
		'{"daily":"1"}',
		false,
		true
	);
	$assert(
		$dates === [
			$startYear . '-01-01',
			$startYear . '-01-02',
			$startYear . '-01-03',
			$startYear . '-01-04',
			$startYear . '-01-05',
			$startYear . '-01-06',
			$startYear . '-01-07',
		],
		'getEvents generates every date in a daily recurrence range.'
	);

	$weeklyDates = $controller->getEvents(
		$startYear . '-01-01',
		$startYear . '-01-14',
		'{"weekly":"1","days":["mon"]}',
		false,
		true
	);
	$expectedWeeklyDates = [];
	for ($day = 1; $day <= 14; $day++) {
		$date = sprintf('%04d-01-%02d', $startYear, $day);
		if (date('D', strtotime($date)) === 'Mon') {
			$expectedWeeklyDates[] = $date;
		}
	}
	$assert(
		$weeklyDates === $expectedWeeklyDates,
		'getEvents emits only selected weekdays for a weekly recurrence.'
	);

	$patternMethod = new ReflectionMethod($controller, 'GetPattern');
	$patternMethod->setAccessible(true);
	$weeklyPattern = json_decode($patternMethod->invoke($controller, [
		'pat_type' => 'weekly',
		'pat_week_n' => '2',
		'pat_da_mon' => '1',
		'pat_da_fri' => '1',
	]), true);
	$assert(
		$weeklyPattern === ['weekly' => '2', 'days' => ['mon', 'fri']],
		'GetPattern preserves selected weekly days and interval.'
	);

	Yii::$app->request->setQueryParams([]);
	$rendered = $controller->actionView($todayId);
	$assert(
		$rendered['view'] === 'view' && $rendered['params']['model']->calendar_id == $todayId,
		'actionView renders the requested calendar record.'
	);
	$deletedSeriesId = $insertEvent($eventPrefix . 'deleted-series', [
		'recurrent_calendar_id' => 1,
		'deleted' => 1,
	]);
	$eventIds[] = $deletedSeriesId;
	$republishResult = json_decode($controller->actionRepublish($deletedSeriesId), true);
	$assert(
		$republishResult['status'] === 'error',
		'actionRepublish refuses to republish a deleted recurring event.'
	);

	$invalidFeedRejected = false;
	Yii::$app->request->setQueryParams([]);
	try {
		$controller->actionRss();
	} catch (\yii\web\BadRequestHttpException $exception) {
		$invalidFeedRejected = true;
	}
	$assert($invalidFeedRejected, 'actionRss requires a valid club filter.');

	$feedEventName = 'Feed & <Test>,;\\Archive';
	$feedEventId = $insertEvent($feedEventName, [
		'event_date' => date('Y-m-d', strtotime('+4 days')),
		'cal_start_time' => '13:00:00',
		'cal_end_time' => '14:00:00',
		'club_id' => $clubId,
		'facility_id' => json_encode([$facilityId]),
	]);
	$eventIds[] = $feedEventId;
	Yii::$app->request->setQueryParams([
		'AgcCalSearch' => ['club_id' => (string)$clubId],
		'format' => 'rss',
	]);
	$rss = $controller->actionRss();
	$assert(
		str_contains($rss, '<title>Feed &amp; &lt;Test&gt;,;\\Archive</title>')
			&& Yii::$app->response->format === yii\web\Response::FORMAT_RAW,
		'actionRss emits escaped RSS for the selected club.'
	);

	Yii::$app->request->setQueryParams([
		'AgcCalSearch' => ['club_id' => (string)$clubId],
	]);
	$ical = $controller->actionRss();
	$assert(
		str_contains($ical, 'SUMMARY:Feed & <Test>\\,\\;\\\\Archive')
			&& str_contains($ical, 'DTSTART:')
			&& str_contains($ical, 'END:VCALENDAR'),
		'actionRss emits escaped iCalendar events by default.'
	);

	$foldMethod = new ReflectionMethod($controller, 'foldICalendarLine');
	$foldMethod->setAccessible(true);
	$folded = $foldMethod->invoke($controller, str_repeat('x', 76));
	$foldedLines = explode("\r\n", $folded);
	$assert(
		strlen($foldedLines[0]) === 75 && str_starts_with($foldedLines[1], ' '),
		'foldICalendarLine wraps long lines at the RFC 75-octet limit.'
	);

} finally {
	if ($legacyIds) {
		$db->createCommand()->delete('events_old', ['e_id' => $legacyIds])->execute();
	}
	if ($eventIds) {
		$db->createCommand()->delete('cal_calendar', ['calendar_id' => $eventIds])->execute();
	}
	if ($statusId) {
		$db->createCommand()->delete('cal_event_status', ['event_status_id' => $statusId])->execute();
	}
	if ($facilityId) {
		$db->createCommand()->delete('cal_facilities', ['facility_id' => $facilityId])->execute();
	}
	if (isset($laneFacilityId) && $laneFacilityId) {
		$db->createCommand()->delete('cal_facilities', ['facility_id' => $laneFacilityId])->execute();
	}
	if ($clubId) {
		$db->createCommand()->delete('clubs', ['club_id' => $clubId])->execute();
	}
}

echo "Passed {$passed} CalendarController assertions.\n";
