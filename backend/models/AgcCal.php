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
		$found=[];
		foreach ($Facility as $fac) {
			if (in_array($fac->facility_id,$id))
				{ $found[] = $fac->name; }
		}
		sort($found);
		return implode(", ",$found);
	}

	public function getAgcRangeStatus() {
        return $this->hasOne(agcRangeStatus::className(), ['range_status_id' => 'range_status_id']);
    }

    public function getIsPublished($id=false) {
		if ($id) {
			// If event is in next year
			$recTest = AgcCal::find()->where(['calendar_id'=>$id])->one();
			if (strtotime($recTest->event_date) >strtotime(date('Y')."-12-31 23:59:00")) { return true; } else { return false; }
		} else {
			// If allowed to Show republished field?
			if (strtotime(yii::$app->controller->getNowTime()) > strtotime(date('Y').'-06-01 00:00:00') && strtotime(yii::$app->controller->getNowTime()) < strtotime(date('Y').'-10-15 00:00:00')) {
				if(!yii::$app->controller->hasPermission('calendar/all')) {
					$where = " club_id in (".ltrim(rtrim(Yii::$app->user->identity->clubs,']'),'[').") AND ";
				} else { $where = ''; }

				$sql = "SELECT distinct recurrent_calendar_id FROM associat_agcnew.agc_calendar ".
					" WHERE ".$where."  calendar_id not in (SELECT distinct recurrent_calendar_id FROM associat_agcnew.agc_calendar where calendar_id=recurrent_calendar_id AND recurrent_calendar_id >0 AND deleted=0 AND event_date > '".date('Y')."-12-31 23:59:00') AND ".
					" calendar_id=recurrent_calendar_id AND recurrent_calendar_id >0 AND deleted=0;";
				$sum =  Yii::$app->db->createCommand($sql)->queryScalar();
				if ($sum >0) { return false; } else { return true; }
			} else { return false; }
		}
	}
}

