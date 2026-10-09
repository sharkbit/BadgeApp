<?php

namespace backend\models;

class BannedGuests extends \yii\db\ActiveRecord {

    public static function tableName() {
        return 'banned_guests';
    }

    public function rules() {
        return [
            [['bg_first_name', 'bg_last_name'], 'trim'],
            [['bg_first_name', 'bg_last_name'], 'required'],
            [['bg_first_name', 'bg_last_name'], 'string', 'max' => 45],
        ];
    }

    public function attributeLabels() {
        return [
            'bg_first_name' => 'First Name',
            'bg_last_name' => 'Last Name',
        ];
    }
}
