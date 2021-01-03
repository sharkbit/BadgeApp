<?php
namespace backend\models;

use Yii;
use DateTime;
use backend\models\BadgeSubscriptions;
use backend\models\clubs;
use backend\models\MembershipType;
use backend\models\Params;
use backend\models\StoreItems;
use backend\models\WorkCredits;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "badges".
 */
class Badges extends \yii\db\ActiveRecord {
	/**
	 * @inheritdoc
	 */

	public $amount_paid;
	public $amt_due;
	public $badge_fee;
	public $cat;
	public $cc_num;
	public $cc_cvc;
	public $cc_exp_mo;
	public $cc_exp_yr;
	public $cc_x_id;
	public $club_name;
	public $discounts;
	public $item_name;
	public $item_sku;
	public $pagesize;
	public $payment_method;
	public $sticker;
	public $subcat;
	public $work_credits;
	public $remarks_temp;


	public static function tableName() {
		return 'badges';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['first_name', 'last_name', 'address', 'city','club_name','state', 'zip', 'gender', 'mem_type', 'incep', 'expires','wt_date','discounts','amt_due','badge_fee','payment_method','wt_instru'], 'required'],
			[['address', 'gender', 'qrcode','status','club_name','cc_num','cc_x_id'], 'string'],
			[['incep', 'expires', 'wt_date','prefix','suffix','ice_phone','ice_contact','remarks','payment_method','remarks_temp','created_at','updated_at','status', 'club_id'], 'safe'],
			[['badge_number','zip','club_id', 'mem_type','sticker','cc_cvc','cc_exp_yr','cc_exp_mo','email_vrfy','yob'], 'integer'],
			[['badge_fee', 'discounts', 'amt_due'], 'number'],
			[['prefix', 'suffix'], 'string', 'max' => 15],
			['email', 'string', 'max' => 60],
			['email', 'filter', 'filter' => 'trim'],
			['email', 'email'],
			[['email'],'unique','message'=>'Email already exist. Please try another one.'],
			[['first_name','last_name'], 'string', 'max' => 35],
			[['phone_op','ice_phone'], 'match', 'pattern' => '/^[- 0-9() +]+$/', 'message' => 'Not a valid phone number.'],
			[['city', 'phone', 'phone_op', 'ice_phone'], 'string', 'max' => 25],
			[['zip'], 'string', 'max' => 10],
			[['wt_instru'], 'string', 'max' => 255],
			[['badge_number'],'unique'],
			['primary', 'required', 'when' => function ($model) {
					return $model->mem_type == '51';},
				'whenClient' => "function (attribute, value) { return $('#badges-mem_type').val() == '51'; }"
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'badge_number' => 'Badge Number',
			'prefix' => 'Prefix',
			'first_name' => 'First Name',
			'last_name' => 'Last Name',
			'suffix' => 'Suffix',
			'address' => 'Address',
			'city' => 'City',
			'state' => 'State',
			'zip' => 'Zip',
			'gender' => 'Gender',
			'yob' => 'YOB',
			'email' => 'Email',
			'email_vrfy' => 'Verified',
			'phone' => 'Phone',
			'phone_op' => 'Phone Optional',
			'ice_contact' => 'Emergency Contact',
			'ice_phone' => 'Emergency Contact Phone',
			'club_name' => 'Club Name',
			'club_id' => 'Clubs',
			'mem_type' => 'Badge Type',
			'primary' => 'Primary',
			'incep' => 'Incep',
			'expires' => 'Expires',
			'qrcode' => 'qrcode',
			'wt_date' => 'WT Date',
			'wt_instru' => 'WT Instructor',
			'remarks' => 'Remarks',
			'badge_fee' => 'Badge Fee',
			'discounts' => 'Discounts',
			'amt_due' => 'Amt Due',
			'payment_method' => 'Payment Method',
			'status'=>'Account Status',
			'amount_paid'=>'Paid Amount',
			'cc_num'=>'Card Number',
			'cc_cvc'=>'CVC',
			'cc_exp_yr'=>'Exp Year',
			'cc_exp_mo'=>'Exp Month' 
		];
	}

	//public function getclubs() {
    //    return $this->hasMany(Clubs::className(), ['club_id' => 'club_id']);
    //}

	public function getMembershipType($mem_type='') {
		if($mem_type<>"") {
			$memberShip = MembershipType::find()->where(['id'=>$mem_type])->one();
			return $memberShip['type'];
		} else {
			return $this->hasOne(MembershipType::className(),['id'=>'mem_type']);
		}
	}

	public function getMemberShipList($limit=null) {
		if ($limit) {
			$memberShip = MembershipType::find()->where(['status'=>'1','self_service'=>'1'])->all();
		} else {
			$memberShip = MembershipType::find()->where(['status'=>'1'])->all();
		}
		return ArrayHelper::map($memberShip,'id','type');
	}

	public function getMemberStatus($status=false) {
		if($status) {
			switch ($status) {
				case 'approved': return 'Approved'; break;
				case 'pending': return 'Pending'; break;
				case 'prob': return 'Probation'; break;
				case 'suspended': return 'Suspended'; break;
				case 'revoked': return 'Revoked'; break;
				case 'retired': return 'Retired'; break;
			}
		} else {
			return ['approved'=>'Approved','pending'=>'Pending','prob'=>'Probation','suspended'=>'Suspended','revoked'=>'Revoked','retired'=>'Retired'];
		}
	}
	
	public function canRenew($status) {
		if ($status=='approved' || $status=='pending' || $status=='prob') {
			return true; }
		else { return false; }
		
	}

	public function gtActiveSubscriptionModel() {
		return $this->hasOne(BadgeSubscriptions::className(),['id'=>'badge_subscription_id']);
	}

	public function getActiveClub() {
		return $this->hasOne(Clubs::className(),['club_id'=>'club_id']);
	}

	public function getWorkCredits($badge_number) {
		$command = Yii::$app->db->createCommand("SELECT sum(work_hours) FROM work_credits where badge_number='$badge_number' ");
		$sum = $command->queryScalar();
		return $sum;
	}

	public function getAllWorkCredits() {
		return $this->hasMany(WorkCredits::className(),['badge_number'=>'badge_number']);
	}
}
