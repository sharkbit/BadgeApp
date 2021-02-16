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
			'yob' => 'YOB',
			'email_vrfy' => 'Verified',
			'phone_op' => 'Phone Optional',
			'ice_contact' => 'Emergency Contact',
			'ice_phone' => 'Emergency Contact Phone',
			'club_id' => 'Clubs',
			'mem_type' => 'Badge Type',
			'incep' => 'Date Joined',
			'wt_date' => 'WT Date',
			'wt_instru' => 'WT Instructor',
			'status'=>'Account Status',
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
				case 'self': return 'Self-Registered'; break;
			}
		} else {
			return ['approved'=>'Approved','pending'=>'Pending','prob'=>'Probation','suspended'=>'Suspended','revoked'=>'Revoked','retired'=>'Retired'];
		}
	}

	public static function cleanBadgeData($model, $DoComment=false,$isNew=false) {
		if(isset($model->yob)) {
			$model->yob = (int)trim($model->yob);
			if($model->yob == 0) { $model->yob=null; }
		}
		if(isset($model->mem_type)) {$model->mem_type = (int)trim($model->mem_type);}
		if($model->mem_type!='51') {
			$model->primary = null;
		}

		$model->first_name = trim($model->first_name);
		$model->last_name = trim($model->last_name);
		$model->suffix = trim($model->suffix);
		$model->address = str_replace("\r\n", ", ", $model->address);
		$model->address = trim(str_replace("\n", ", ", $model->address));
		$model->phone = preg_replace('/\D/','',$model->phone);
		$model->phone_op = preg_replace('/\D/','',$model->phone_op);
		$model->ice_phone = preg_replace('/\D/','',$model->ice_phone);
		$model->wt_date = date('Y-m-d',strtotime($model->wt_date));
		$model->expires = date('Y-m-d',strtotime($model->expires));
		$model->incep = date('Y-m-d H:i:s',strtotime($model->incep));

		if(isset($model->payment_method)) {
			if($model->payment_method=='creditnow') {$payment_method = 'credit';} else {$payment_method = $model->payment_method;} }
		if(isset($_POST['new_club'])) {
			(new Clubs)->saveClub($model->badge_number,$_POST['new_club']);
			$model->club_id=$_POST['new_club'][0];
		} else {
			if(!$isNew) {(new Clubs)->saveClub($model->badge_number,[35]);
			$model->club_id=35; }
		}

		$dirty = (New Badges)->loadDirtyFilds($model);
		$dirty = implode(", ",$dirty);

		$model->incep = date('Y-m-d H:i:s',strtotime($model->incep));
		$model->updated_at = yii::$app->controller->getNowTime();

		if((isset($model->remarks_temp) && $model->remarks_temp <> '') || ($DoComment && $dirty)) {
			$remarksOld = json_decode($model->remarks,true);
			if(($model->remarks_temp) && ($dirty)) {
				$cmnt = $model->remarks_temp.", Updated: ".$dirty;
			} elseif($dirty) { $cmnt = "Updated: ".$dirty;
			} elseif($model->remarks_temp) { $cmnt=$model->remarks_temp;
			} else { $cmnt = ''; }
			if ($isNew) {$by='Created';} else {$by='Updated';}
			$nowRemakrs = [
				'created_at'=>yii::$app->controller->getNowTime(),
				'data'=>$cmnt. ' ',
				'changed'=> $by.' by '.$_SESSION['user'],
			];
			if($remarksOld != '') {
				array_push($remarksOld,$nowRemakrs);
			} else {
				$remarksOld = [
					$nowRemakrs,
				];
			}
			$model->remarks = json_encode($remarksOld,true);
		}

		return $model;
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

	public static function loadDirtyFilds($model) {
		$model->club_id=(int)$model->club_id;
		$items=$model->getDirtyAttributes();
		$obejectWithkeys = [
			'club_id' => 'Club',
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
			'mem_type' => 'Membership Type',
			'primary' => 'Primary',
			'expires' => 'Expires',
			'qrcode' => 'qrcode',
			'wt_date' => 'WT Date',
			'wt_instru' => 'WT Instructor',
			'status'=>'Account Status',
		];

		$responce = [];
		foreach($items as $key => $item) {
			if(array_key_exists($key,$obejectWithkeys)) {
				$responce[] = $obejectWithkeys[$key];
			}
		}
		sort($responce);
		return $responce;
	}

	public function getFirstFreeBadge(){
		$sql='SELECT t.badge_number + 1 AS FirstAvailableId FROM badges t LEFT JOIN badges t1 ON t1.badge_number = t.badge_number + 1 WHERE t1.badge_number IS NULL ORDER BY t.badge_number LIMIT 0, 1';
		$connection = Yii::$app->getDb();
		$command = $connection->createCommand($sql);
		$NewId = $command->queryAll();
		if (isset($NewId[0]['FirstAvailableId'])) {$FirstId=$NewId[0]['FirstAvailableId'];} else {$FirstId=1;}
		return $FirstId;
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
