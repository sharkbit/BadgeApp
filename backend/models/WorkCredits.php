<?php

namespace backend\models;

use Yii;
use backend\models\Badges;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "work_credits".
 *
 * @property integer $id
 * @property integer $badge_number
 * @property string $work_date
 * @property double $work_hours
 * @property string $project_name
 * @property string $remarks
 * @property string $authorized_by
 * @property string $status
 * @property string $updated_at
 */
class WorkCredits extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $badge_holder_name;
    public $work_hours_new;

    public static function tableName()
    {
        return 'work_credits';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['badge_number', 'work_hours', 'project_name', 'supervisor', 'status', 'updated_at','created_by'], 'required'],
            [['work_date', 'updated_at','work_hours_new','created_by','authorized_by'], 'safe'],
            [['work_hours'], 'number'],
			[['id','status'], 'integer'],
			[['remarks'],'safe'],
            [['remarks'], 'string'],
            [['project_name', 'authorized_by','supervisor'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'badge_number' => 'Badge Number',
            'work_date' => 'Work Date',
            'work_hours' => 'Work Hours',
            'project_name' => 'Project Name',
            'remarks' => 'Remarks',
            'authorized_by' => 'Authorized By',
			'supervisor'=> 'Supervised By',
            'status' => 'Status',
            'updated_at' => 'Updated At',
            'work_hours_new'=>'Work Hours',
			'created_by'=>'Created by',
        ];
    }

    public function getBadgeNumbers() {

        $badgesArray = Badges::find()->where(['status'=>'active'])->all();
        $badgesList = ArrayHelper::map($badgesArray,'badge_number','badge_number');
        return $badgesList;
    }
}
