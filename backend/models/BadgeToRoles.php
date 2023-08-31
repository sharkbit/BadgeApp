<?php

namespace backend\models;

use Yii;
/**
 * This is the model class for table "Officers".
 *
*/
class BadgeToRoles extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
	public $full_name;
	public $pagesize;

    public static function tableName() {
        return 'badge_to_role';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
			[['badge_number','club','role'], 'required'],
			[['badge_number','club','role'], 'integer']
		];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'badge_number' => 'Badge Number',
        ];
    }
}

