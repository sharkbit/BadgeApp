<?php

namespace backend\models;

use Yii;
use backend\models\Badges;
use backend\models\clubs;
/**
 * This is the model class for table "badge_payments".
 *
 * @property integer $id
 * @property integer $badge_number
 * @property string $valid_from
 * @property string $valid_true
 * @property string $payment_type
 * @property string $status
 * @property string $created_at
 */
class BadgeSubscriptions extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
    
    public $amount_due;
	public $cc_num;
	public $cc_cvc;
	public $cc_exp_mo;
	public $cc_exp_yr;
	public $expires;
	public $item_name;
	public $item_sku;
    public $mem_id;
    public $mem_type;
    public $redeemable_credit;
    public $total_credit;
    public $wt_date;
    public $wt_instru;

    public static function tableName() {
        return 'badge_subscriptions';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['badge_number', 'valid_from', 'valid_true', 'payment_type', 'status', 'created_at','badge_fee'], 'required'],
            ['amount_due', 'required', 'when' => function ($model) {
                $currentUrl = yii::$app->controller->getCurrentUrl();
                if($currentUrl['controllerId']=='badges'&&$currentUrl['actionId']=='update') {
                    return true;
                }
              }, 
            ],
            [['valid_from', 'valid_true', 'created_at','badge_fee','paid_amount','discount','mem_id','mem_type','expires','amount_due','redeemable_credit','transaction_type','club_id','wt_date','wt_instru','is_migrated'], 'safe'],
			[['cc_cvc','cc_exp_yr','cc_exp_mo'],'integer'],
            [['sticker'],'required'],
            [['payment_type','status','cc_num','cc_x_id'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
		return [
			'id' => 'ID',
			'badge_number' => 'Badge Number',
			'badge_fee' => 'Badge Fee',
			'discount' => 'Discount',
			'valid_from' => 'Valid From',
			'valid_true' => 'Valid True',
			'payment_type' => 'Payment Type',
			'paid_amount' => 'Paid Amount',
			'status' => 'Status',
			'sticker' => 'Sticker #',
			'created_at' => 'Created At',
			'mem_type'=>'Membership Type',
			'transaction_type' => 'Transaction Type',
			'cc_num'=>'Card Number',
			'cc_cvc'=>'CVC',
			'cc_exp_yr'=>'Exp Year',
			'cc_exp_mo'=>'Exp Month',
			'redeemable_credit'=>'Credit (hr)'
		];
    }

    public function getBadgeDetails() {
        return $this->hasOne(Badges::className(),['badge_number'=>'badge_number']);
    }
    public function getClubDetails() {
        return $this->hasOne(Clubs::className(),['club_id'=>'club_id']);
    }

	public function getLabel($label) {
		$labels=$this->attributeLabels();
		return $labels[$label];
	}

}
