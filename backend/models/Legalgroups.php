<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "Events".
 */

class Legalgroups extends \yii\db\ActiveRecord{


    public static function tableName() {
        return 'associat_agcnew.groups';
    }

    public function rules() {
        return [
           [['name','date_created','date_modified'], 'safe'],
           [['group_id','isactive','display_order'], 'number'],
       ];
    }

    public function attributeLabels() {
        return [
			'name' => 'Name',
       ];
    }
}