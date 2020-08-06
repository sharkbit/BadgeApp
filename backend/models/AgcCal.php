<?php

namespace backend\models;

use Yii;
use backend\models\clubs;
use backend\models\agcEventStatus;
use backend\models\agcFacility;
use backend\models\agcRangeStatus;

/**
 * This is the model class for table "AGC.agc_calendar".
 */
class AgcCal extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
	public $pagesize;
	public $rec_pat;
	

    public static function tableName() {
        return 'associat_agcnew.agc_calendar';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
			[['event_name','event_date','poc_badge'], 'required'],
			[['date_requested','end_time','event_date','facility_id','recurrent_end_date','recurrent_start_date','start_time','remarks'], 'safe'],
			[['active','approved','calendar_id','conflict','deleted','range_status_id','recur_every','recurrent_calendar_id','rollover','showed_up'], 'integer'],
			[['club_id','event_status_id','lanes_requested','poc_badge'], 'integer'],
			[['event_name','keywords','recur_week_days'], 'string'],
		];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'club_id'=>'Sponsor',
			'event_name' => 'Event Name',
			'event_status_id'=>'Event Type',
			'facility_id'=>'Facility',
			'range_status_id'=>'Range Status',
			'recurrent_calendar_id'=> 'Recur ID',
			'pattern_type'=>'Pattern',
			'poc_badge'=>'POC Badge',
			'recur_week_days'=>'Recurring Pattern',
        ];
    }

	public function getClubs() {
        return $this->hasOne(Clubs::className(), ['club_id' => 'club_id']);
    }

	public function getAgcEventStatus() {
        return $this->hasOne(agcEventStatus::className(), ['event_status_id' => 'event_status_id']);
    }

	public function getAgcFacility_Names($id) {
		if(!is_array($id)) {$id = json_decode($id); }
		$Facility = (new agcFacility)->find()->all();
		$found='';
		foreach ($Facility as $fac) {
			if (in_array($fac->facility_id,$id))
				$found .= $fac->name.', ';
		}
		return rtrim ($found,", ");
	}

	public function getAgcRangeStatus() {
        return $this->hasOne(agcRangeStatus::className(), ['range_status_id' => 'range_status_id']);
    }
	
    public function getIsPublished($id) {
		$command = Yii::$app->db->createCommand("SELECT count(*) FROM associat_agcnew.agc_calendar where recurrent_calendar_id = $id ".
			" and event_date > '".date('Y')."-12-31 23:59:00'");
		$sum = $command->queryScalar();
		if ($sum >0) {
			return true;
		} else {
			return false; 
		}
	}	
}

