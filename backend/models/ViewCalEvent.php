<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "view_cal_event".
 */
class ViewCalEvent extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
    public $file;

    public static function tableName() {
        return 'view_cal_event';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['club_name','cal_start_time','cal_end_time','event_date','event_name','event_status_name','short_name'],'safe'],
			[['allow_guests','is_volunteer'],'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
			'cal_start_time' => 'Start Timer',
			'cal_end_time' => 'End Time'
        ];
    }

	public static function getActiveEventsQuery() {
        return self::find()
            ->select([
				'calendar_id',
				'event_date',
                'cal_start_time',
                'cal_end_time',
                'event_name',
                'event_status_name',
                'club_name',
                'short_name',
				'allow_guests',
				'track_wristbands',
				'is_volunteer'
            ])
            ->where([
				'event_date' => date('Y-m-d',strtotime(yii::$app->controller->getNowTime()))
			])
			// Event starts anytime up to 1 hour into the future
			->andWhere(['<=',
                new \yii\db\Expression('CONCAT(event_date, " ", cal_start_time)'),
                new \yii\db\Expression('NOW() + INTERVAL 1 HOUR')
            ])
            // Event ends no earlier than 1 hour in the past
            ->andWhere(['>=',
                new \yii\db\Expression('CONCAT(event_date, " ", cal_end_time)'),
                new \yii\db\Expression('NOW() - INTERVAL 1 HOUR')
            ])
			->orderBy(['cal_start_time' => SORT_ASC])
			->all();
    }



}
