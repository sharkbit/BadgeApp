<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "work_credits".
 *
 * @property integer $id
 * @property integer $badge_number
 * @property string $g_first_name
 * @property string $g_last_name
 * @property string $g_city
 * @property string $g_state
 * @property integer $g_yob
 * @property integer $tmp_badge
 * @property date $time_in
 * @property date $time_out 
 */

class Violations extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
    public $reporter_name;
	public $involved_name;
	public $witness_name;
	public $club_id;

    public static function tableName() {
        return 'violations';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
			[['badge_reporter','badge_involved','vi_date','vi_sum','vi_loc'], 'required'],
			[['badge_involved','badge_witness','vi_sum','vi_rules','vi_report','vi_action','hear_date','hear_sum','vi_loc'], 'safe'],
			[['badge_reporter','vi_type','was_guest', 'vi_override'], 'integer'],
			[['badge_involved','badge_witness','vi_sum'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
		return [
            'badge_reporter' => 'RSO Badge#',
			'reporter_name' => 'RSO Name',
            'badge_involved' => 'Badge#',
			'badge_witness' => 'Witness Badge#',
            'vi_date' => 'Date of Incident',
			'involved_name' => 'Badge Holder Name',
            'vi_type' => 'Class',
			'vi_override' => 'Class 4 Override',
			'vi_loc' => 'Location',
            'vi_sum' => 'Comments',
			'vi_rules' => 'Range Rule Violation(s)',
			'vi_report' => 'Detailed Report',
            'vi_action' => 'Action Taken',
			'was_guest' => 'Guest Involved',
			'hear_date' => 'Hearing Date',
			'hear_sum' => 'Hearing Summary',
			'club_id' => 'Club Name'
        ];
    }

	public function getLocations($loc=null) {
		if($loc) {
			switch ($loc) {
				case 50:	return  "50yd"; break;
				case 100:	return "100yd"; break;
				case 200:	return "200yd"; break;
				case 'trap':	return "Trap 1,2,3"; break;
				case 'w':	return "Wobble"; break;
				case 'p':	return "Pattern"; break;
				case 'o':	return "Other"; break;
			}
		} else {
			return ['50' => "50yd", '100' => "100yd", '200' => "200yd",'trap' => "Trap 1,2,3", 'w' => "Wobble", 'p' => "Pattern", 'o' => "Other"];
		}
	}
}

