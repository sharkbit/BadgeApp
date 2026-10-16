<?php

namespace backend\models;

class Reports extends \yii\db\ActiveRecord {

    public static function tableName() {
        return 'view_rpt_1';
    }

    public function rules() {
        return [
            [['tx_month', 'tx_type','item_count'], 'safe'],
        ];
    }

    public function attributeLabels() {
        return [
        //    'bg_first_name' => 'First Name',
          
        ];
    }
}
