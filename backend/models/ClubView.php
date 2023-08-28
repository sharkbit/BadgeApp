<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "bn_to_cl".
 */
class ClubView extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
    public $file;

    public static function tableName() {
        return 'bn_to_cl';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['badge_number','club_id'],'number'],
            [['club_name'], 'string', 'max' => 255],
            [['short_name'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
			'badge_number' => 'Badge Number',
			'club_id' => 'Club ID',
			'club_name' => 'Club Name',
            'short_name' => 'Short Name'		
        ];
    }

}
