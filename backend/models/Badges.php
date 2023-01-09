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
	public $badge_year;
	public $cat;
	public $cc_num;
	public $cc_cvc;
	public $cc_exp_mo;
	public $cc_exp_yr;
	public $cc_x_id;
	public $club_id;
	public $discounts;
	public $item_name;
	public $item_sku;
	public $pagesize;
	public $payment_method;
	public $sticker;
	public $subcat;
	public $tax;
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
			[['first_name', 'last_name', 'address', 'city','state', 'zip', 'gender', 'mem_type','club_id', 'incep', 'wt_date','discounts','amt_due','badge_fee','payment_method','wt_instru'], 'required'],
			[['address', 'gender', 'qrcode','status','cc_num','cc_x_id'], 'string'],
			[['incep', 'expires', 'wt_date','prefix','suffix','ice_phone','ice_contact','remarks','payment_method','remarks_temp','created_at','updated_at','status', 'club_id'], 'safe'],
			[['badge_number','zip', 'mem_type','cc_cvc','cc_exp_yr','cc_exp_mo','email_vrfy','yob'], 'integer'],
			[['badge_fee','badge_year', 'amt_due','tax'], 'number'],
			[['prefix', 'suffix'], 'string', 'max' => 15],
			['email', 'string', 'max' => 60],
			['email', 'filter', 'filter' => 'trim'],
			['email', 'email'],
			//['email','unique','targetClass' => '\backend\models\Badges','message'=>'Email already exist. Please use another one.'],
			[['email'],'uniqueEmail'],
			[['first_name','last_name'], 'string', 'max' => 35],
			[['phone_op','ice_phone'], 'match', 'pattern' => '/^[- 0-9() +]+$/', 'message' => 'Not a valid phone number.'],
			[['city', 'phone', 'phone_op', 'ice_phone'], 'string', 'max' => 25],
			[['sticker'],'string','min'=>8, 'max'=>10],
			[['zip'], 'string', 'max' => 10],
			[['wt_instru'], 'string', 'max' => 255],
			['primary', 'required', 'when' => function ($model) {
					return $model->mem_type == '51';},
				'whenClient' => "function (attribute, value) { return $('#badges-mem_type').val() == '51'; }"
			],
		];
	}

	public function uniqueEmail($a) {
		$user = (New Badges)->find()->where(['email'=>$this->$a])->one();
		if($user) {
			$this->addError('email', 'Email used by: '.$user->first_name.' (Badge #'.$user->badge_number.')');
		}
	}

	public function attributeLabels() {
		return [
			'badgeyear'=>'Badge Year',
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

	public function getBadgeYear() {
		$bgyr = BadgeSubscriptions::find()->where(['badge_number'=>$this->badge_number])->orderBy(['badge_year'=>SORT_DESC])->one();
		if ($bgyr) {return $bgyr->badge_year; } else { return null; }
    }

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
			return ['self'=>'Self-Registered','approved'=>'Approved','pending'=>'Pending','prob'=>'Probation','suspended'=>'Suspended','revoked'=>'Revoked','retired'=>'Retired'];
		}
	}

	public static function cleanBadgeData($model, $DoComment=false,$isNew=false,$self=false) {
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
		$model->incep = date('Y-m-d H:i:s',strtotime($model->incep));

		if(isset($model->payment_method)) {
			if($model->payment_method=='creditnow') {$payment_method = 'credit';} else {$payment_method = $model->payment_method;} }
		$dirty = (New Badges)->loadDirtyFilds($model);
		if((yii::$app->controller->hasPermission('badges/all')) && (Yii::$app->controller->id.'->'.Yii::$app->controller->action->id <> 'sales->index')) {
			if(isset($model->club_id)) {
				if($model->club_id=='') {
					yii::$app->controller->createLog(true, 'trex_zod_1',  Yii::$app->controller->id.'->'.Yii::$app->controller->action->id);
					(new clubs)->saveClub($model->badge_number,[35]); 
					$dirty[]='Club';
				} else {
					$mine = (new clubs)->getMyClubs($model->badge_number);
					if ($mine != $model->club_id) {
						(new clubs)->saveClub($model->badge_number,$model->club_id);
						$dirty[]='Club';
					}
				}
			} else {
				if(!$isNew) {(new clubs)->saveClub($model->badge_number,[35]); $dirty[]='Club'; 
				yii::$app->controller->createLog(true, 'trex_zod_2',  Yii::$app->controller->id.'->'.Yii::$app->controller->action->id);}
			}
		}
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
				'changed'=> $by.' by '.(($self)? $model->last_name.' (Self-Registered)':$_SESSION['user']),
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
		if ($status=='approved' || $status=='pending' || $status=='prob' || $status=='self') { return true; }
		else { return false; }
	}

	public function gtActiveSubscriptionModel() {
		return $this->hasOne(BadgeSubscriptions::className(),['id'=>'badge_subscription_id']);
	}

	public static function loadDirtyFilds($model) {
		$items=$model->getDirtyAttributes();
		$obejectWithkeys = [
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
