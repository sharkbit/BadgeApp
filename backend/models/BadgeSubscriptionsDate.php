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
 * @property string $badge_year
 * @property string $payment_type
 * @property string $status
 * @property string $created_at
 */
class BadgeSubscriptionsDate extends \yii\db\ActiveRecord {
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
	public $tax;
    public $total_credit;
    public $wt_date;
    public $wt_instru;

    public static function tableName() {
        return 'badge_subscriptions_date';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['badge_number', 'badge_year','payment_type', 'status','sticker','created_at','badge_fee'], 'required'],
            ['amount_due', 'required', 'when' => function ($model) {
                $currentUrl = yii::$app->controller->getCurrentUrl();
                if($currentUrl['controllerId']=='badges'&&$currentUrl['actionId']=='update') {
                    return true;
                }
              }, 
            ],
            [['badge_year','bs_c_date','created_at','badge_fee','mem_id','mem_type','expires','redeemable_credit','transaction_type','club_id','wt_date','wt_instru','is_migrated'], 'safe'],
			[['amount_due','paid_amount','tax'],'number'],
			[['cc_cvc','cc_exp_yr','cc_exp_mo'],'integer'],
			[['sticker'],'string','min'=>8, 'max'=>10],
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
			'badge_year' => 'Badge Year',
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
