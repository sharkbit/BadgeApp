<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "cc_receipts".
 *
 * @property integer $authCode
 * @property string $type
 * @property string $status
 */

class CardReceipt extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'cc_receipts';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
          // [['type', 'status'], 'required'],
           [['authCode','cardNum','cardType','cart','status','tx_type','tx_date','id','name'], 'string'],
		   [['badge_number','expYear','expMonth'], 'integer'],
           [['amount'], 'number'],
       ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
			'authCode' => 'authCode',
			'badge_number'=>'Badge Number',
			'cardNum'=>'cardNum',
			'cardType'=>'cardType',
			'cart' => 'Cart',
			'expMonth'=>'expMonth',
			'expYear'=>'expYear',
			'id' => 'ID',
			'status' => 'Card Status',
			'tx_date' => 'Date',
			'tx_type' => 'TX Type',
			'name' => 'Name',
			'amount' => 'Amount'
       ];
    }
}

