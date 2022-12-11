<?php

namespace backend\models;

use Yii;
use backend\models\clubs;
use backend\models\BadgeSubscriptionsDate;


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
	public $pagesize;
	public $show_club;

    public static function tableName() {
        return 'cc_receipts_date';
    }

	public static function primaryKey() {
		return ["id"];
	}
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
          // [['type', 'status'], 'required'],
           [['authCode','cardNum','cc_c_date','cardType','cart','status','tx_type','tx_date','id','name','cashier'], 'string'],
		   [['badge_number','expYear','expMonth','cashier_badge'], 'integer'],
           [['amount','tax'], 'number'],
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
			'transaction_type' => 'New / Renew',
			'tx_type' => 'TX Type',
			'name' => 'Name',
			'amount' => 'Amount'
       ];
    }

	public function getbadges(){
		 return $this->hasOne(\backend\models\Badges::className(), ['badge_number' => 'cashier_badge']);
	}

	public function getbadge_subscriptions_date(){
		return $this->hasOne(\backend\models\BadgeSubscriptionsDate::className(), ['badge_number' => 'badge_number','bs_c_date'=>'cc_c_date']);
	}

	public function getClubNames() {
		return (new clubs)->getMyClubsNames($this->badge_number,true);
	}
}

