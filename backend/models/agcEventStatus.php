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
        return 'cal_event_status';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
			[['name'], 'required'],
			[['active','allow_guests','display_order','event_status_id','is_volunteer','track_wristbands'], 'integer'],
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

	public static function getCanVolunteer() {
		return agcEventStatus::find()
			->select(['event_status_id'])
			->where(['is_volunteer' => 1])
			->column();
	}

    public function getStatusList() {
		$statusArray = $this->find()
			->where(['active'=>'1'])
			->orderBy(['name'=> SORT_ASC ])
			->all();
		return ArrayHelper::map($statusArray,'event_status_id','name');
    }

	public static function getTrackWristbands() {
		return agcEventStatus::find()
			->select(['event_status_id'])
			->where(['track_wristbands' => 1])
			->column();
	}
}

