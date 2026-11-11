<?php

namespace backend\models;

use yii;
use yii\db\Expression;
use backend\models\clubs;
use backend\models\agcEventStatus;

/**
 * This is the model class for table "Events".
 */

class Events extends \yii\db\ActiveRecord{

    public static function tableName() {
        return 'events_old';
    }

    public function rules() {
        return [
           [['e_date'], 'safe'],
           [['e_id','calendar_id','e_poc','sponsor','e_hours'], 'number'],
		   [['e_name','e_status','e_type','e_inst','e_rso'], 'string'],
       ];
    }

    public function attributeLabels() {
        return [
			'club_id' => 'Club Name',
       ];
    }

}
