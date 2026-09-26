<?php

namespace backend\models;


use yii\db\Expression;
use backend\models\clubs;
use backend\models\agcEventStatus;

/**
 * This is the model class for table "Events".
 */

class Events extends \yii\db\ActiveRecord{
	public $pagesize;

    public static function tableName() {
        return 'view_events';
    }

    public function rules() {
        return [
           [['event_date','cal_start_time','cal_end_time'], 'safe'],
           [['attended_badges','attended_guests','club_id','ea_calendar_id','allow_guests','track_wristbands','is_volunteer','wb_out_zero'], 'number'],
		   [['event_name','event_status_name','club_name','short_name'], 'string'],
       ];
    }

    public function attributeLabels() {
        return [
			'cal_start_time' => 'Start Time',
			'cal_end_time' => 'End Time',
			'event_status_name'=>'Event Status',
			'wb_out_zero' => 'Wristbands Out'
       ];
    }

	 public function getNewEvents() {
		$eventsToday = Events::find()
			->select('ea_calendar_id')
			->where(['event_date' => new Expression('CURRENT_DATE')]);

		$calendarsWithoutEventsToday = AgcCal::find()
			->alias('cal')
			->select([
				'cal.calendar_id',
				'cal.event_name',
				'cal.club_id',
				'cal.event_status_id',
				'club_name' => 'club.club_name',
				'allow_guests',
				'is_volunteer',
				'track_wristbands'
			])
			->leftJoin(['club' => clubs::tableName()], 'club.club_id = cal.club_id')
			->leftJoin(['agcEventStatus' => agcEventStatus::tableName()], 'agcEventStatus.event_status_id = cal.event_status_id')
			->where(['cal.event_date' => new Expression('CURRENT_DATE')])
			->andWhere(['not in', 'cal.calendar_id', $eventsToday])
			->all();
		return $calendarsWithoutEventsToday;
	 }
}
