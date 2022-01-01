<?php

namespace backend\models;

use Yii;
/**
 * This is the model class for table "work_credits".
 *
*/
class RsoReports extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
	public $pagesize;

    public static function tableName() {
        return 'rso_reports';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
			[['date','rso'], 'required'],
			[['cash_bos','cash_eos','id','rso','par_50','par_100','par_200'], 'integer'],
			[['closing','notes','shift','shift_anom'], 'safe'],
		];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'rso' => "RSO's",
        ];
    }
}

