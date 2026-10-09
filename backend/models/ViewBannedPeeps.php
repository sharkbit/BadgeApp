<?php

namespace backend\models;

/**
 * This is the model class for view "view_banned_peeps".
 *
 * @property string $person_id
 * @property string $first_name
 * @property string $last_name
 * @property string $ban_status
 */
class ViewBannedPeeps extends \yii\db\ActiveRecord {

    public $badge_number;

    public static function tableName() {
        return 'view_banned_peeps';
    }

    public static function primaryKey() {
        return ['person_id'];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['person_id', 'first_name', 'last_name', 'ban_status'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'person_id' => 'Person ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'ban_status' => 'Ban Status',
        ];
    }
}
