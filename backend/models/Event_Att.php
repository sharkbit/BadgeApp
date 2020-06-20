<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "Events".
 */

class Event_Att extends \yii\db\ActiveRecord{

	public $ea_type;
	
    public static function tableName() {
        return 'event_attendee';
    }

    public function rules() {
        return [
           [['ea_f_name','ea_l_name','ea_wb_serial','ea_wb_out'], 'safe'],
           [['ea_badge','ea_event_id','ea_wc_logged'], 'number'],
           //[['type'], 'string', 'max' => 25],
       ];
    }

    public function attributeLabels() {
        return [
			'ea_id' => 'ID',
			'ea_badge' => 'Badge Number',
			'ea_f_name' => 'First Name',
			'ea_l_name' => 'Last Name',
			'ea_wb_serial' => 'Wrist Band',
       ];
    }
}
