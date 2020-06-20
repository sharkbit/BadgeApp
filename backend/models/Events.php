<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "Events".
 */

class Events extends \yii\db\ActiveRecord{
	
	public $poc_name;
	
    public static function tableName() {
        return 'events';
    }

    public function rules() {
        return [
           [['e_name','e_date','e_poc','e_status','e_type'], 'required'],
           [['e_id','e_hours','e_poc'], 'number'],
		   [['e_inst','e_rso'], 'string'],
       ];
    }

    public function attributeLabels() {
        return [
			'e_id' => 'ID',
			'e_date' => 'Date',
			'e_inst' => 'Instructor Name(s)',
			'e_name' => 'Event Name',
			'e_poc' => 'POC Badge #',
			'e_rso' => 'RSO',
			'e_status' => 'Status',
			'e_type' => 'Event Type',
			'e_hours' => 'Hours'
       ];
    }
}
