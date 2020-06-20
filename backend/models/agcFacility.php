<?php

namespace backend\models;

use Yii;
/**
 * This is the model class for table "work_credits".
 *
*/
class agcFacility extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
	public $pagesize;

    public static function tableName() {
        return 'associat_agcnew.facilities';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
			[['name'], 'required'],
			[['active','available_lanes','display_order','facility_id'], 'integer'],
			[['name'], 'safe'],
			];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'name' => 'Facility Name'
        ];
    }
}

