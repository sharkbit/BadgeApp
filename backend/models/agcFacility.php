<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;
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
        return 'cal_facilities';
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

	public function getFacilityList() {
		$FacilityList = agcFacility::find()
			->where(['active'=>1])
			->orderBy(['name'=>SORT_ASC])
			->asArray()
			->all();	
		return ArrayHelper::map($FacilityList, 'facility_id', 'name');
	}

	public function getFacilityNames($id) {
		if(!is_array($id)) {$id = json_decode($id); }
		$Facility = (new agcFacility)->find()->all();
		$found=[];
		foreach ($Facility as $fac) {
			if (in_array($fac->facility_id,$id))
				{ $found[] = $fac->name; }
		}
		sort($found);
		return implode(", ",$found);
	}

	public function getFacilRequiresLanes() {
		return ArrayHelper::index(agcFacility::find('facility_id')
			->where(['active'=>1])->andwhere('available_lanes>0')
			->orderBy(['name'=>SORT_ASC])->asArray()->all(),'facility_id');
	}
}

