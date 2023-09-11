<?php

namespace backend\models;

use Yii;
/**
 * This is the model class for table "Officers".
 *
*/
class Officers extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
	public $pagesize;

    public static function tableName() {
        return 'officers';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
			[['badge_number','club','role'], 'required'],
			[['badge_number','club','role'], 'integer'],
			[['club_name','role_name','short_name'], 'safe'],
		];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'badge_number' => 'Badge Number',
			'club_name' => 'Club',
			'role_name' => 'Role',
			'short_name' => 'Club Short Name'			
        ];
    }
}

