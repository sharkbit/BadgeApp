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
	public $poc_name;
	
    public static function tableName() {
        return 'events';
    }

    public function rules() {
        return [
           [['e_name','e_date','e_poc','e_status','e_type','sponsor'], 'required'],
           [['e_id','e_hours','e_poc','sponsor'], 'number'],
		   [['e_inst','e_rso'], 'string'],
		   [['e_id'], 'safe'],
       ];
    }

    public function attributeLabels() {
        return [
			'e_id' => 'ID',
			'e_date' => 'Date',
			'e_inst' => 'Instructor Name(s)',
			'e_name' => 'Event Name',
			'e_poc' => 'POC',
			'e_rso' => 'RSO',
			'e_status' => 'Status',
			'e_type' => 'Event Type',
			'e_hours' => 'Hours'
       ];
    }

	public function getBadges() {
		return $this->hasOne(\backend\models\Badges::classname(),['badge_number'=>'e_poc']);
	}

	public function getClubs() {
		return $this->hasOne(clubs::classname(),['club_id'=>'sponsor']);
	}

	public function getEvent_Att() {
		return (New Event_Att)->find()->where(['ea_event_id'=>$this->e_id,'ea_wb_out'=>1])->andwhere(['>','ea_wb_serial',0])->count();
    }
	
	public function getEventdata ($event_id) {
		$query = $this->getWhere($event_id,'e_id');
		$count=Yii::$app->db->createCommand('SELECT COUNT(*) FROM BadgeDB.events e WHERE e.`e_status`=1')->queryScalar();
		//$sql="SELECT (SELECT count(*) FROM event_attendee WHERE ".$query.") AS num_ea_att, ";
		//$sql="(SELECT SUM( (select count(*) FROM BadgeDB.event_attendee WHERE ea_event_id=e_id)) as sum ";		
		$sql="SELECT SUM( (select count(*) FROM BadgeDB.event_attendee ".$query.")) as sum ";
			$mydata = Yii::$app->db->createCommand($sql)->queryall();
yii::$app->controller->createLog(true, 'trex-event', 'Raw Sql: '.var_export($query->createCommand()->getRawSql(),true));
			$dataProvider = new \yii\data\SqlDataProvider([
				'sql' => $sql,
				'totCount' => $count,
			]);
			return $dataProvider;
	}
}
