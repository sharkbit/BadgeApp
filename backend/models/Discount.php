<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "bn_to_cl".
 */
class Discount extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
    //public $file;

    public static function tableName() {
        return 'discount';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['dis_allowed'], 'safe'],

			[['dis_id', 'dis_active', 'dis_amount','dis_def'],'number'],
            [['dis_name'], 'string', 'max' => 45],
            [['dis_short'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
			'dis_active' =>  'Discount Active',
			'dis_allowed' => 'Discount Allowed on',
			'dis_amount' =>  'Discount Amount',
			'dis_def' =>  'Default Discount',
			'dis_id'  =>  'Discount ID',
			'dis_name' => 'Discount Name',
            'dis_short' => 'Discount Short Name',
        ];
    }

    // Convert PHP array to JSON string before saving to DB
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            if (is_array($this->dis_allowed)) {
                //$this->dis_allowed = Json::encode($this->dis_allowed);
				$this->dis_allowed = implode(",", $this->dis_allowed);
            }
            return true;
        }
        return false;
    }

    // Convert JSON string back to PHP array after retrieving from DB
    public function afterFind() {
        parent::afterFind();
        if ($this->dis_allowed) {
            // Decodes as array instead of stdClass object
            //$this->dis_allowed = Json::decode($this->dis_allowed);
			$this->dis_allowed = explode(",", $this->dis_allowed);
        } else {
            $this->dis_allowed = [];
        }
    }

    public function getDiscounts($page='new') {
		$DiscountArray = Discount::find()
			->where( ['dis_active'=>'1'] )
			->andFilterWhere(['like', 'dis_allowed', $page])
			->orderBy(['dis_name'=> SORT_ASC ])
			->all();

		$Discounts=[];
		if($DiscountArray) {
			foreach ($DiscountArray as $key=>$value) {
				$discountKey = $value->dis_short . ':' . $value->dis_amount;
				$Discounts += [$discountKey => $value->dis_name];
			}
			return $Discounts;
		} else {
			return [];
		}
    }

    public function getDiscountDefault() {
		$DiscountArray = Discount::find()
			->where( ['dis_active'=>'1','dis_def'=>1] )
			->one();
		if ($DiscountArray) {
			return $DiscountArray->dis_short.":".$DiscountArray->dis_amount;
		} else {
			return 'n:0';
		}
	}
}
