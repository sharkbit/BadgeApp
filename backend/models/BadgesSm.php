<?php
namespace backend\models;

use Yii;
use DateTime;
use backend\models\Badges;
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
class BadgesSm extends \yii\db\ActiveRecord {
	public $club_name;
	public $email_verify;

	public static function tableName() {
		return 'badges';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['first_name','last_name','address','city','club_name','email','email_verify','state','zip','gender','ice_phone','ice_contact','incep','mem_type','expires','wt_date','wt_instru'], 'required'],
			[['address', 'gender', 'qrcode','status','club_name'], 'string'],
			[['incep', 'expires', 'wt_date','prefix','suffix','ice_phone','ice_contact','remarks','remarks_temp','created_at','updated_at','status', 'club_id'], 'safe'],
			[['badge_number','zip','club_id', 'mem_type','yob'], 'integer'],
			[['prefix', 'suffix'], 'string', 'max' => 15],
			['email','filter','filter' => 'trim'],
			['email','email'],
			//['email','unique','targetClass' => '\backend\models\Badges','message'=>'Email already exist. Please use another one.'],
			//['email','uniqueEmail'],
			['email_verify', 'compare', 'compareAttribute'=>'email', 'message'=>"Emails don't match"],
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

/*	public function uniqueEmail($attribute, $params){
         if($user = Badges::model()->exists('email=:email',array('email'=>$this->email)))
          $this->addError($attribute, 'Email already exists!');
    } */
	
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'yob' => 'YOB',
			'email_verify'=> 'Verify Email',
			'email_vrfy' => 'Verified',
			'phone_op' => 'Phone Optional',
			'ice_contact' => 'Emergency Contact',
			'ice_phone' => 'Emergency Contact Phone',
			'club_id' => 'Clubs',
			'mem_type' => 'Badge Type',
			'incep' => 'Date Joined',
			'wt_date' => 'Walk Through Date',
			'wt_instru' => 'Walk Through Instructor',
			'status'=>'Account Status',
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
		if ($status=='approved' || $status=='pending' || $status=='prob' || $status=='self') {
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
