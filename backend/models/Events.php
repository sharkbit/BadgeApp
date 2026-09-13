<?php

namespace backend\models;

use Yii;
use backend\models\Event_Att;
use backend\models\clubs;

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
           [['ea_calendar_id','allow_guests','track_wristbands','is_volunteer'], 'number'],
		   [['event_name','event_status_name','club_name','short_name'], 'string'],
       ];
    }

    public function attributeLabels() {
        return [
			'cal_start_time' => 'Start Time',
			'cal_end_time' => 'End Time',
			'event_status_name'=>'Event Status'
       ];
    }
/*
	public function getBadges() {
		return $this->hasOne(\backend\models\Badges::classname(),['badge_number'=>'e_poc']);
	}

	public function getClubs() {
		return $this->hasOne(clubs::classname(),['club_id'=>'sponsor']);
	}

	public function getEvent_Att() {
		return (New Event_Att)->find()->where(['ea_calendar_id'=>$this->e_id,'ea_wb_out'=>1])->andwhere(['>','ea_wb_serial',0])->count();
    }
	
	public function getEventdata ($event_id) {
		$sql="select (select count(*) FROM BadgeDB.event_attendee where ea_badge > 0 and ea_calendar_id=$event_id) as badge, ".
			"(select count(*) FROM BadgeDB.event_attendee where ea_badge is null and ea_calendar_id=$event_id) as student ";
	   	$command = Yii::$app->db->createCommand($sql);
		$event_attend = $command->queryAll();
		if(isset($event_attend[0]['badge'])) {
			return "b: ".$event_attend[0]['badge'].", s: ".$event_attend[0]['student'];
		} else {
			return 'no data';
		}
	}
	*/
}
