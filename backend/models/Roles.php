<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;
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
			[['disp_order','role_id'], 'integer'],
			[['role_name'], 'safe'],
		];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'role_name' => 'Role Name',
			'disp_order' => 'Display Order'
        ];
    }

	public function getRoles() {
		$groups = Roles::find()->orderBy(['disp_order' => SORT_ASC])->all();
		return ArrayHelper::map($groups,'role_id','role_name');
	}
}

