<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "AGC.range_status".
 */
class agcRangeStatus extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */

    public static function tableName() {
        return 'associat_agcnew.range_status';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
			[['name'], 'required'],
			//[[''], 'safe'],
			[['active','display_order','range_status_id','restricted'], 'integer'],
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
		return ArrayHelper::map($statusArray,'range_status_id','name');
    }
}

