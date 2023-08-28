<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for view "bn_to_by".
 *
 * @property integer $badge_number
 * @property integer $badge_year
 */
class BadgeToYear extends \yii\db\ActiveRecord {

    public static function tableName() {
        return 'bn_to_by';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['badge_number', 'badge_year'], 'required'],
            [['badge_number', 'badge_year'], 'number'],
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
