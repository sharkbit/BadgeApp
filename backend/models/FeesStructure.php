<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;
use backend\models\MembershipType;

/**
 * This is the model class for table "fees_structure".
 *
 * @property integer $id
 * @property string $label
 * @property integer $membership_id
 * @property double $fee
 * @property string $status
 */
class FeesStructure extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'fees_structure';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['fee', 'status'], 'required'],
            [['membership_id'], 'integer'],
            [['type'],'safe'],
            [['fee'], 'number'],
            [['status','sku_full','sku_half'], 'string'],
            [['label'], 'string', 'max' => 255],
           // ['membership_id', 'unique', 'targetAttribute' => ['membership_id'], 'message' => 'alredy have a price for this membership type'],

            ['membership_id', 'required', 'when' => function ($model) {
                    return $model->type == 'badge_fee';},
                    'whenClient' => "function (attribute, value) {
                        return $('div#feesstructure-type input:checked').val() == 'badge_fee'; 
                    }"
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'label' => 'Label',
            'membership_id' => 'Membership ID',
            'fee' => 'Fee',
            'status' => 'Status',
            'type'=>'Fee Type',
			'sku_full' => 'Full Year SKU', 
			'sku_half' => 'Half Year SKU',
        ];
    }

    public function getMembershipType() {
        return $this->hasOne(MembershipType::className(),['id'=>'membership_id']);
    }

    public function getMembershipList() {
        $MembershipType = MembershipType::find()->all();
        $membershipList = ArrayHelper::map($MembershipType,'id','type');

        return $membershipList;
    }
}