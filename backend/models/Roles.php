<?php

namespace backend\models;

use Yii;
/**
 * This is the model class for table "work_credits".
 *
*/
class Roles extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
	public $pagesize;

    public static function tableName() {
        return 'roles';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
			[['role_name'], 'required'],
			[['role_id'], 'integer'],
			[['role_name'], 'safe'],
		];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'role_name' => 'Role Name'
        ];
    }
}

