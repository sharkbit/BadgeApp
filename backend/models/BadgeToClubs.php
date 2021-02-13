<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "badge_to_club".
 *
 * @property integer $id
 * @property integer $badge_number
 * @property integer $club_id
 */
class BadgeToClubs extends \yii\db\ActiveRecord {

    public static function tableName() {
        return 'badge_to_club';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['badge_number', 'club_id'], 'required'],
            [['badge_number', 'club_id'],'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
          //  'id' => 'BtC ID',
        ];
    }
}
