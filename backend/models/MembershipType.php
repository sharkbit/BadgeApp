<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;
use backend\models\StoreItems;

/**
 * This is the model class for table "membership_type".
 *
 * @property integer $id
 * @property string $type
 * @property string $status
 */

class MembershipType extends \yii\db\ActiveRecord{
    /**
     * @inheritdoc
     */
	
    public static function tableName() {
        return 'membership_type';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
           [['type', 'status','renew_yearly'], 'required'],
           [['status'], 'string'],
		   [['renew_yearly'], 'integer'],
           [['type'], 'string', 'max' => 25],
       ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
		   'type'=>'Badge Type',        
       ];
    }

    public function getFullprice() {
		$sku_data = StoreItems::findOne(['sku'=>$this->sku_full]);
		if(!isset($sku_data->price)){
			$sku_data = new \stdClass();
			$sku_data->price='0.00';
			$sku_data->item='Free';
			$sku_data->sku='00000';
		}
		return $sku_data;
    }

    public function getHalfprice() {
		$sku_data = StoreItems::findOne(['sku'=>$this->sku_half]);
		if(!isset($sku_data->price)){
			$sku_data = new \stdClass();
			$sku_data->price='0.00';
			$sku_data->item='Free';
			$sku_data->sku='00000';
		}
		return $sku_data;
    }

    public function getMembershipList() {
        $MembershipType = MembershipType::find()->all();
        $membershipList = ArrayHelper::map($MembershipType,'id','type');

        return $membershipList;
    }
}
