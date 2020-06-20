<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "AGC.event_status".

 */
class agcEventStatus extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */

    public static function tableName() {
        return 'associat_agcnew.event_status';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
			[['name'], 'required'],
			//[[''], 'safe'],
			[['active','display_order','event_status_id'], 'integer'],
			[['name'], 'string'],
			];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'name' => 'Event Status'
        ];
    }

    public function getStatusList() {
		$statusArray = $this->find()
			->where(['active'=>'1'])
			->orderBy(['name'=> SORT_ASC ])
			->all();
		return ArrayHelper::map($statusArray,'event_status_id','name');
    }
}

