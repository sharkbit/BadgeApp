<?php
namespace backend\Controllers;

use DateInterval;
use DateTime;
use DateTimeZone;
use yii;
use common\models\User;
use backend\models\Badges;
use backend\models\Params;
use backend\models\Privileges;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use yii\helpers\ArrayHelper;

class AdminController extends \yii\web\Controller {

	public $activeUser;

	public $rootAdminPermission = [
		'Accounts' => ['accounts/temp','accounts/index','accounts/create','accounts/update','accounts/view','accounts/delete','accounts/reset-password','accounts/request-password-reset'],
		'Admin' => ['site/admin-menu','privileges/create','privileges/delete','privileges/index','privileges/update'],
		'Badges'=>['badges/all','badges/add-certification','badges/api-check','badges/barcode','badges/create','badges/delete-certificate','badges/delete','badges/generate-new-sticker','badges/get-badge-name','badges/get-family-badges','badges/modify','badges/photo-add','badges/photo-crop','badges/post-print-transactions','badges/print','badges/print-rcpt','badges/renew-membership','badges/delete-renewal','badges/overideprice','badges/rename','badges/scan-badge','badges/test','badges/update-renewal','badges/view-certificate','badges/view-certifications-list','badges/update-certificate','badges/view-renewal-history','badges/view-remarks-history','badges/view-subscriptions','badges/view-violations-history','badges/view-work-credits','badges/view-work-credits-log',],
		'Calendar' =>['calendar/all','calendar/approve','calendar/bulkdelete','calendar/close','calendar/create','calendar/conflict','calendar/delete','calendar/get-event-types','calendar/inactive','calendar/index','calendar/open-range','calendar/recur','calendar/republish','calendar/shoot','calendar/showed','calendar/update','calendar/view'],
		'CalSetup' => ['cal-setup/index','cal-setup/clubs','cal-setup/updateclu','cal-setup/facility','cal-setup/updatefac','cal-setup/rangestatus','cal-setup/updateran','cal-setup/eventstatus','cal-setup/updateeven'],
		'Clubs' => ['clubs/roles','clubs/role-create','clubs/role-delete','clubs/role-update','clubs/officers','clubs/officers-create','clubs/officers-delete','clubs/officers-update','clubs/index','clubs/create','clubs/delete','clubs/update','clubs/view','clubs/badge-rosters'],
		'MassEmail' => ['mass-email/create','mass-email/index','mass-email/update','mass-email/send','mass-email/process'],
		'Events' => ['events/approve','events/add-att','events/index','events/close','events/create','events/delete','events/reg','events/return','events/remove-att','events/update','events/view'],
		'Membership Type'=>['membership-type/ajaxmoney-convert','membership-type/index','membership-type/create','membership-type/update','membership-type/delete-X','membership-type/view'],
		'Guest' => ['guest/all','guest/modify','guest/update','guest/stats','guest/delete'],
		'Index' => ['site/new-member','site/no-email','site/verify'],
		'LegeslativeEmails'=>['legelemail/index','legelemail/create','legelemail/groups','legelemail/update','legelemail/delete'],
		'Params' => ['params/update'],
		'Range Badge Database' => ['range-badge-database/index','range-badge-database/view','range-badge-database/delete','range-badge-database/update'],
		'Rso Report'=>['rso-rpt/current','rso-rpt/close_mod','rso-rpt/delete','rso-rpt/index','rso-rpt/remarks','rso-rpt/settings','rso-rpt/sticker','rso-rpt/update','rso-rpt/view','sticker/add','sticker/move','rso-rpt/sticker-update','rso-rpt/sticker-delete'],
		'Rules'=> ['rules/index','rules/create','rules/update','rules/view'],
		'sales' => ['payment/converge','payment/index','payment/inventory','sales/all','sales/create','sales/delete-sale','sales/delete','sales/stock','sales/summary','sales/update','sales/inventory','sales/report'],
		'violations' => ['violations/all','violations/board','violations/create','violations/delete','violations/report','violations/stats','violations/update'],
		'Work Credits'=>['work-credits/all','work-credits/approve','work-credits/update','work-credits/delete'],
	];

	public $adminPermission = [
		'Accounts' => ['accounts/index','accounts/create','accounts/update','accounts/view','accounts/reset-password','accounts/request-password-reset'],
		'Admin' => ['site/admin-menu'],
		'Badges'=>['badges/all','badges/add-certification','badges/barcode','badges/create','badges/delete-certificate','badges/generate-new-sticker','badges/get-badge-name','badges/get-family-badges','badges/modify','badges/photo-add','badges/photo-crop','badges/post-print-transactions','badges/print','badges/print-rcpt','badges/renew-membership','badges/rename','badges/scan-badge','badges/test','badges/update-renewal','badges/delete-renewal','badges/overideprice','badges/view-certificate','badges/view-certifications-list','badges/update-certificate','badges/view-renewal-history','badges/view-remarks-history','badges/view-subscriptions','badges/view-violations-history','badges/view-work-credits','badges/view-work-credits-log'],
		'Calendar' =>['calendar/all','calendar/approve','calendar/close','calendar/create','calendar/conflict','calendar/delete','calendar/get-event-types','calendar/inactive','calendar/index','calendar/open-range','calendar/recur','calendar/republish','calendar/shoot','calendar/update'],
		'MassEmail' => ['mass-email/create','mass-email/index','mass-email/update','mass-email/send','mass-email/process'],
		'Membership Type'=>['membership-type/ajaxmoney-convert','membership-type/index','membership-type/create','membership-type/update','membership-type/view'],
		'Clubs' => ['clubs/roles','clubs/role-create','clubs/role-delete','clubs/role-update','clubs/officers','clubs/officers-create','clubs/officers-delete','clubs/officers-update','clubs/index','clubs/create','clubs/update','clubs/view','clubs/badge-rosters'],
		'Events' => ['events/approve','events/add-att','events/index','events/close','events/create','events/delete','events/reg','events/remove-att','events/update','events/view'],
		'Guest' => ['guest/all','guest/modify','guest/update','guest/delete','guest/stats'],
		'Rso Report'=>['rso-rpt/close_mod','rso-rpt/index','rso-rpt/remarks','rso-rpt/settings','rso-rpt/sticker','rso-rpt/view','sticker/add','sticker/move','rso-rpt/sticker-update'],
		'Rules'=> ['rules/index','rules/create','rules/update','rules/view'],
		'sales' => ['sales/all','sales/create','sales/delete-sale','sales/stock','sales/summary','sales/report','sales/update'],
		'violations' => ['violations/all','violations/board','violations/create','violations/delete','violations/report','violations/stats','violations/update'],
		'Work Credits'=>['work-credits/all','work-credits/approve','work-credits/update','work-credits/delete'],
	];

	public $adminViewPermission = [
		'Accounts' => ['accounts/index','accounts/view'],
		'Admin' => ['site/admin-menu'],
		'Badges'=>['badges/all','badges/get-badge-name','badges/get-family-badges','badges/post-print-transactions','badges/print-rcpt','badges/view-certifications-list','badges/view-renewal-history','badges/view-remarks-history','badges/view-subscriptions','badges/view-violations-history','badges/view-work-credits','badges/view-work-credits-log'],
		'Clubs' => ['clubs/officers','clubs/officers-create','clubs/officers-delete','clubs/officers-update','clubs/index','clubs/view'],
		'Events' => ['events/index','events/view'],
		'Guest' => ['guest/all','guest/stats'],
		'Rso Report'=>['rso-rpt/index','rso-rpt/view'],
		'Rules'=> ['rules/index','rules/view'],
		'sales' => ['sales/all','sales/stock','sales/summary'],
		'violations' => ['violations/all','violations/stats'],
		'Work Credits'=>['work-credits/all','work-credits/approve','work-credits/update','work-credits/delete'],
	];
	
	public $cashierPermission = [
		'Badges'=>['badges/all','badges/add-certification','badges/barcode','badges/create','badges/delete-certificate','badges/generate-new-sticker','badges/get-family-badges','badges/modify','badges/photo-add','badges/photo-crop','badges/post-print-transactions','badges/print','badges/print-rcpt','badges/renew-membership','badges/rename','badges/scan-badge','badges/test','badges/update-renewal','badges/overideprice','badges/delete-renewal','badges/view-certificate','badges/view-certifications-list','badges/update-certificate','badges/view-renewal-history','badges/view-remarks-history','badges/view-subscriptions','badges/view-work-credits','badges/view-work-credits-log'],
		'Membership Type'=>['membership-type/ajaxmoney-convert','membership-type/index','membership-type/create','membership-type/update','membership-type/view'],
		'Clubs' => ['clubs/index','clubs/view','clubs/badge-rosters'],
		'sales' => ['sales/all','sales/report','sales/stock','sales/summary'],
		'Guest' => ['guest/all','guest/modify','guest/update'],
	];

	public $rsoLeadPermission = [
		'Badges'=>['badges/all','badges/add-certification','badges/generate-new-sticker','badges/get-badge-name','badges/get-family-badges','badges/modify','badges/photo-add','badges/photo-crop','badges/post-print-transactions','badges/print-rcpt','badges/renew-membership','badges/test','badges/view-certifications-list','badges/view-renewal-history','badges/view-remarks-history','badges/view-subscriptions','badges/view-violations-history','badges/view-work-credits','badges/view-work-credits-log'],
		'Calendar' =>['calendar/all','calendar/showed','calendar/index'],
		'Events' => ['events/approve','events/index','events/close','events/create','events/reg','events/return','events/remove-att','events/view'],
		'Membership Type'=>['membership-type/ajaxmoney-convert'],
		'Guest' => ['guest/all','guest/modify','guest/update'],
		'Rso Report'=>['rso-rpt/current','rso-rpt/close_mod','rso-rpt/index','rso-rpt/sticker','rso-rpt/update','rso-rpt/view'],
		'sales' => ['sales/all','sales/stock'],
		'violations' => ['violations/all','violations/create','violations/report','violations/update'],
	];

	public $rsoPermission = [
		'Badges'=>['badges/all','badges/add-certification','badges/generate-new-sticker','badges/get-badge-name','badges/get-family-badges','badges/modify','badges/photo-add','badges/photo-crop','badges/post-print-transactions','badges/print-rcpt','badges/renew-membership','badges/test','badges/view-certifications-list','badges/view-renewal-history','badges/view-remarks-history','badges/view-subscriptions','badges/view-violations-history','badges/view-work-credits','badges/view-work-credits-log'],
		'Calendar' =>['calendar/all','calendar/showed','calendar/index'],
		'Events' => ['events/approve','events/index','events/close','events/create','events/reg','events/return','events/remove-att','events/view'],
		'Membership Type'=>['membership-type/ajaxmoney-convert'],
		'Guest' => ['guest/all','guest/modify','guest/update'],
		'Rso Report'=>['rso-rpt/current','rso-rpt/index','rso-rpt/sticker','rso-rpt/view'],
		'sales' => ['sales/all','sales/stock'],
		'violations' => ['violations/all','violations/create','violations/update'],
	];

	public $viewPermission = [
		'Badges'=>['badges/all','badges/get-badge-name','badges/get-family-badges','badges/post-print-transactions','badges/print-rcpt','badges/view-certifications-list','badges/view-renewal-history','badges/view-remarks-history','badges/view-subscriptions','badges/view-violations-history','badges/view-work-credits','badges/view-work-credits-log'],
		'Events' => ['events/index','events/view'],
		'Guest' => ['guest/all'],
		'sales' => ['sales/all','sales/stock','sales/report','sales/summary'],
		'Rso Report'=>['rso-rpt/index'],
		'violations' => ['violations/all'],
		'Work Credits'=>['work-credits/all'],
	];

	public $workcreditPermission = [
		'Badges'=>['badges/all','badges/get-badge-name','badges/view-certificate','badges/view-certifications-list','badges/view-remarks-history','badges/view-work-credits','badges/view-work-credits-log'],
		'Events' => ['events/create','events/index','events/reg','events/view'],
		'Guest' => ['guest/update'],
		'Work Credits'=>['work-credits/add'],
	];

	public $cioPermission = [
		'Badges'=>['badges/restrict','badges/get-badge-name','badges/view-certificate','badges/view-certifications-list','badges/view-remarks-history','badges/view-work-credits','badges/view-work-credits-log'],
		'Calendar' =>['calendar/create','calendar/index','calendar/conflict','calendar/get-event-types','calendar/inactive','calendar/open-range','calendar/update'],
		'Events' => ['events/index','events/add-att','events/create','events/reg','events/view'],
		'Guest' => ['guest/update'],
	];

	public $userPermission = [
		'Badges'=>['badges/restrict','badges/photo-add','badges/photo-crop','badges/view-certificate','badges/view-certifications-list','badges/view-renewal-history','badges/view-remarks-history','badges/view-subscriptions','badges/view-violations-history','badges/view-work-credits','badges/view-work-credits-log'],
		'Guest' => ['guest/update'],
	];

	public $calendarPermission = [
		'Calendar' =>['calendar/create','calendar/index','calendar/conflict','calendar/delete','calendar/get-event-types','calendar/inactive','calendar/index','calendar/open-range','calendar/recur','calendar/republish','calendar/update'],
		'Guest' => ['guest/update'],
	];

	public $chairmanPermission = [
		'Calendar' => ['calendar/close','calendar/recur'],
		'Events' => ['events/add-att','events/index','events/create','events/reg','events/return','events/view'],
		'Work Credits'=>['work-credits/add'],
	];

	public $shootPermission = [
		'Calendar' => ['calendar/all','calendar/shoot'],
	];
	
	public $AllPermission = [
		'Badges'=>['badges/api-zip','badges/api-generate-renaval-fee','badges/api-request-family','badges/get-badge-details','badges/index','badges/update','badges/verify-email','badges/view'],
		'Guest' => ['guest/add','guest/addcredit','guest/create','guest/index','guest/out','guest/sticky-form','guest/view'],
		'membershiptype'=>['membership-type/fees-by-type'],
		'help'=>['badges/help','sales/help','rso-rpt/help'],
		'payments'=>['payment/charge'],
		'sales' => ['sales/index','sales/print-rcpt','sales/purchases'],
		'Site' => ['site/index','site/error','site/log-error','site/logout','site/login','site/login-member','params/password'],
		'violations' => ['violations/index','violations/view'],
		'Work Credits'=>['work-credits/create','work-credits/index','work-credits/sticky-form','work-credits/credit-transfer','work-credits/transfer-confirm','work-credits/transfer-form','work-credits/transfer-view','work-credits/view'],
	];

	// Used for Importing CVS data
	public $creditArray = ['badgenum','workdate','workhours','project','auth','status','last_update','procdate','who'];

	public function beforeAction($event) {

		if(!yii::$app->user->isGuest) {
			if(isset($_GET['goBack'])) {
				array_pop($_SESSION['back']);
				$goBack = end($_SESSION['back']);
				array_pop($_SESSION['back']);
				$this->redirect($goBack);
			} else {
				$_SESSION['back'][]=$_SERVER['REQUEST_URI'];
			}
			if(!$this->hasPermission(Yii::$app->controller->id."/".Yii::$app->controller->action->id)){
				//throw new \yii\web\UnauthorizedHttpException();
				return $this->redirect(['/']); 
			}
		} else if (yii::$app->user->isGuest) {
			// Pages that dont require login
			if((Yii::$app->controller->id."/".Yii::$app->controller->action->id=='events/reg') ||
				(Yii::$app->controller->id."/".Yii::$app->controller->action->id=='site/verify') ||
				(Yii::$app->controller->id."/".Yii::$app->controller->action->id=='site/new-member') ||
				(Yii::$app->controller->id."/".Yii::$app->controller->action->id=='site/no-email') ||
				(Yii::$app->controller->id."/".Yii::$app->controller->action->id=='site/login') ||
				(Yii::$app->controller->id."/".Yii::$app->controller->action->id=='site/login-member') ||
				(Yii::$app->controller->id."/".Yii::$app->controller->action->id=='badges/get-badge-name') ||
				(Yii::$app->controller->id."/".Yii::$app->controller->action->id=='badges/verify-email') ||
				(Yii::$app->controller->id."/".Yii::$app->controller->action->id=='clubs/badge-rosters') ||
				(Yii::$app->controller->id."/".Yii::$app->controller->action->id=='payment/charge') ||
				(Yii::$app->controller->id."/".Yii::$app->controller->action->id=='membership-type/fees-by-type') ||
				(Yii::$app->controller->id."/".Yii::$app->controller->action->id=='badges/api-request-family') ||
				(Yii::$app->controller->id."/".Yii::$app->controller->action->id=='badges/api-zip') ||
				(Yii::$app->controller->id."/".Yii::$app->controller->action->id=='badges/api-generate-renaval-fee') ||
				(Yii::$app->controller->id."/".Yii::$app->controller->action->id=='mass-email/process') ) {

				$myJump = Yii::$app->request->get();
				if (isset($myJump['url'])) { $_SESSION['jump'] = $myJump['url']; }
			}
			else {
				unset($_COOKIE['_identity-backend']);
				header("Location: /site/login-member");
				exit;
			}
		}
		if(isset($_SESSION['myFlash'])) {
			$flash=explode('^',$_SESSION['myFlash']);
			Yii::$app->getSession()->setFlash($flash[0], $flash[1]);
			unset ($_SESSION['myFlash']);
		}
		return parent::beforeAction($event);
	}

	public function hasPermission($event) {

		if(isset(Yii::$app->user->id)) {

			if(!isset($_SESSION['privilege'])) {
				$activeUser = $this->getActiveUser();
				if ($activeUser->badge_number >0 ) {$_SESSION['badge_number'] = $activeUser->badge_number;}
					else {$_SESSION['badge_number']=0;}
				$_SESSION['privilege'] = json_decode($activeUser->privilege);
				$_SESSION['user'] = $activeUser->full_name;
				$_SESSION['names'] = $this->getBadgeList();
				foreach ($_SESSION['privilege'] as $priv) {
					$chk_priv = Privileges::find()->where(['id'=>$priv])->one();
					if (isset($_SESSION['timeout'])) {
						if ($_SESSION['timeout'] < $chk_priv->timeout) {
						$_SESSION['timeout'] = $chk_priv->timeout; }
					} else { $_SESSION['timeout'] = $chk_priv->timeout; }
				}
				if ((isset($activeUser->clubs)) && (is_array(json_decode($activeUser->clubs)))) {
					$_SESSION['cal_clubs'] = json_decode($activeUser->clubs); }
			}

			if ($this->Check_Privs($event,$this->AllPermission)) { return true; }
 			foreach ($_SESSION['privilege'] as $priv) {
				if ($priv==1)     { if ($this->Check_Privs($event,$this->rootAdminPermission)) return true; }
				elseif ($priv==2) { if ($this->Check_Privs($event,$this->adminPermission)) return true; }
				elseif ($priv==6) { if ($this->Check_Privs($event,$this->rsoLeadPermission)) return true; }
				elseif ($priv==3) { if ($this->Check_Privs($event,$this->rsoPermission)) return true; }
				elseif ($priv==7) { if ($this->Check_Privs($event,$this->workcreditPermission)) return true; }
				elseif ($priv==8) { if ($this->Check_Privs($event,$this->cioPermission)) return true; }
				elseif ($priv==9) { if ($this->Check_Privs($event,$this->calendarPermission)) return true; }
				elseif ($priv==10){ if ($this->Check_Privs($event,$this->cashierPermission)) return true; }
				elseif ($priv==4) { if ($this->Check_Privs($event,$this->viewPermission)) return true; }
				elseif ($priv==5) { if ($this->Check_Privs($event,$this->userPermission)) return true; }
				elseif ($priv==11) { if ($this->Check_Privs($event,$this->chairmanPermission)) return true; }
				elseif ($priv==13) { if ($this->Check_Privs($event,$this->adminViewPermission)) return true; }
				elseif ($priv==15) { if ($this->Check_Privs($event,$this->shootPermission)) return true; }
			}
			return false;
		}
	} //.has permission ending

	private function Check_Privs($event,$TestPriv) {
		foreach ($TestPriv as $permission) { if(in_array($event,$permission)) { return true; } }
		return false;
	}

	public function getNowTime($offset = null) {
		$date = new DateTime();
		$date->setTimezone(new DateTimeZone(yii::$app->params['timeZone']));
		if($offset) {
			$date->add(new DateInterval($offset));
		}
		return $date->format('Y-m-d H:i:s');
	}

	public function getActiveUser() {
		if(isset(yii::$app->user->id)) {
			$activeUser = User::find()->where(['id' =>yii::$app->user->id])->one();
			return $activeUser;
		}
	}

	public function getBadgeList() {
		$sql = "SELECT badge_number, CONCAT(first_name,' ',last_name) as bname from badges ORDER BY badge_number";
		$command = Yii::$app->db->createCommand($sql);
		$myNames = $command->queryAll();

		foreach($myNames as $id){
			$myNames_a[$id['badge_number']] = $id['badge_number'].','.$id['bname'];
		}
		if(isset($myNames_a)) { return base64_encode(implode(";",$myNames_a)); }
	}

	public function decodeBadgeName($badge_nu) {
		if($badge_nu==0) {return 'Admin';}
		if(isset($_SESSION['names'])) {
			$myNames = explode(';',base64_decode($_SESSION['names']));
			foreach($myNames as $id){
				$expNmaes=explode(',',$id);
				$myNames_a[$expNmaes[0]] = $expNmaes[1];
			}
			if (empty($myNames_a[$badge_nu])) {
				return "Unknown";
			} else {
				return $myNames_a[$badge_nu];
			}
		} else { return "Unknown"; }
	}

	public function getNowDigit() {
		date_default_timezone_set(yii::$app->params['timeZone']);
		$dateTime = date('ymdHis');
		return $dateTime;
	}

	public function pretydtg($t){
		if( date('Ymd') == date('Ymd',strtotime($t)) ) {
			$Ptime = date('G:i',strtotime($t));
		} elseif( date('Y') == date('Y',strtotime($t)) ) {
			$Ptime = date('M d, G:i',strtotime($t));
		} else {$Ptime = date('M d, Y',strtotime($t));}
		return $Ptime;
	}

	public function getCurrentUrl() {
		$controllerId = yii::$app->controller->id;
		$actionId = yii::$app->controller->action->id;
		$requestUrl = $_SERVER['REQUEST_URI'];
		$responce = [
			'controllerId'=>$controllerId,
			'actionId' => $actionId,
			'requestUrl' => $requestUrl,
		];

		return $responce;
	}

	public function getStates(){
		return ['AL'=>'Alabama','AK'=>'Alaska','AZ'=>'Arizona','AR'=>'Arkansas','CA'=>'California','CO'=>'Colorado','CT'=>'Connecticut','DE'=>'Delaware','FL'=>'Florida','GA'=>'Georgia','HI'=>'Hawaii','ID'=>'Idaho','IL'=>'Illinois','IN'=>'Indiana','IA'=>'Iowa','KS'=>'Kansas','KY'=>'Kentucky','LA'=>'Louisiana','ME'=>'Maine','MD'=>'Maryland','MA'=>'Massachusetts','MI'=>'Michigan','MN'=>'Minnesota','MS'=>'Mississippi','MO'=>'Missouri','MT'=>'Montana','NE'=>'Nebraska','NV'=>'Nevada','NH'=>'New Hampshire','NJ'=>'New Jersey','NM'=>'New Mexico','NY'=>'New York','NC'=>'North Carolina','ND'=>'North Dakota','OH'=>'Ohio','OK'=>'Oklahoma','OR'=>'Oregon','PA'=>'Pennsylvania','RI'=>'Rhode Island','SC'=>'South Carolina','SD'=>'South Dakota','TN'=>'Tennessee','TX'=>'Texas','UT'=>'Utah','VT'=>'Vermont','VA'=>'Virginia','WA'=>'Washington','WV'=>'West Virginia','WI'=>'Wisconsin','WY'=>'Wyoming','DC'=>'District of Columbia','GU'=>'Guam','MH'=>'Marshall Islands','MP'=>'Northern Mariana Island','PR'=>'Puerto Rico','VI'=>'Virgin Islands','AE'=>'Armed Forces Africa','AA'=>'Armed Forces Americas','AE'=>'Armed Forces Canada','AE'=>'Armed Forces Europe','AE'=>'Armed Forces Middle East','AP'=>'Armed Forces Pacific'];
	}

	public function validateExcell($excell_type,$items) {
		if($excell_type=='workcredit') {
			$item = $items[0];
			$rowAttributes = $this->creditArray;
			foreach ($rowAttributes as  $attribute) {
				if(!array_key_exists($attribute, $item)) {
					return 'false';
				}
			}
			return 'true';
		}
	}

	public function stringReplace($string,$replace_array) {
		foreach ($replace_array as $key) {
			$string = str_replace($key, '_', $string);
		}
		return $string;
	}

	public function RestoreSession(&$searchModel,$form,$filters,$tst=false) {
		if($tst) yii::$app->controller->createLog(true, 'trex_Admin_RS_'.$form, 'Hit from '.$form);
		if($tst) yii::$app->controller->createLog(true, 'trex_Admin_RS_', var_export($filters,true));
		if($tst) yii::$app->controller->createLog(true, 'trex_Admin_RS_', var_export($_REQUEST,true));
		if(isset($_REQUEST['reset'])) {
			foreach($filters as $filtr) {
				$clr=$form.'Search'.$filtr;
				unset($_SESSION[$clr]);
			}
			$urlStatus = yii::$app->controller->getCurrentUrl();
			return $this->redirect([$urlStatus['actionId']]);
		} else {
			foreach($filters as $filtr) {
				$clr=$form.'Search'.$filtr;
				if(isset($_REQUEST[$form.'Search'][$filtr])) {
					if($tst) yii::$app->controller->createLog(true, 'trex_Admin_RS_'.$form, 'Found '.$filtr);
					$searchModel->$filtr = $_REQUEST[$form.'Search'][$filtr];
					$_SESSION[$clr] = $_REQUEST[$form.'Search'][$filtr];
				} elseif (isset($_REQUEST[$filtr])) {
					if($tst) yii::$app->controller->createLog(true, 'trex_Admin_RS_'.$form, 'Found '.$filtr);
					$searchModel->$filtr = $_REQUEST[$filtr];
					$_SESSION[$clr] = $_REQUEST[$filtr];
				} elseif (isset($_SESSION[$clr])) {
					$searchModel->$filtr = $_SESSION[$clr];
				}
			}
		}
		return $searchModel;
	}

	public function RotateLog() {
		$param = Params::find()->one();
		$mon = date('m',strtotime(yii::$app->controller->getNowTime()));
		if($param->log_rotate <> $mon) {
			//Rotate Logs
			$dir=Yii::getAlias('@webroot/');
			$yr = date('Y',strtotime(yii::$app->controller->getNowTime()));
			if ($mon==1) { $log_mon=12; $yr=$yr-1; } else { $log_mon=$mon-1; }
			$log_mon = str_pad($log_mon, 1, '0', STR_PAD_LEFT);

			@rename($dir."activity_logs.txt",$dir."activity_log.$yr-$log_mon.txt");
			@rename($dir."access_logs.txt",  $dir."access_log.$yr-$log_mon.txt");
			@rename($dir."calendar_logs.txt",   $dir."calendar_log.$yr-$log_mon.txt");
			@rename($dir."email_logs.txt",   $dir."email_log.$yr-$log_mon.txt");
			@rename($dir."java_logs.txt",   $dir."java_log.$yr-$log_mon.txt");

			$param->log_rotate = $mon;
			$param->save(false);
		}
	}

	public function createLog($isEnabled, $username, $activity, $name='activity') {
		$param = Params::findOne('1');
		if(($isEnabled) || (Yii::$app->params['env']=='dev')) {
			$root = Yii::getAlias('@webroot/'.$name.'_logs.txt');
			$fp = fopen($root, 'a');
			fwrite($fp, "['".yii::$app->controller->getNowTime()."','".$username."','".$activity."']\r\n");
			fclose($fp);
		}
	}

	public function createCalLog($isEnabled, $username, $activity) {
		$param = Params::findOne('1');
		if(($isEnabled) || (Yii::$app->params['env']=='dev')) {
			$root = Yii::getAlias('@webroot/calendar_logs.txt');
			$fp = fopen($root, 'a');
			fwrite($fp, "['".yii::$app->controller->getNowTime()."','".$username."','".$activity."']\r\n");
			fclose($fp);
		}
	}

	public function createEmailLog($isEnabled, $username, $activity) {
		$param = Params::findOne('1');
		if(($isEnabled) || (Yii::$app->params['env']=='dev')) {
			$root = Yii::getAlias('@webroot/email_logs.txt');
			$fp = fopen($root, 'a');
			fwrite($fp, "['".yii::$app->controller->getNowTime()."','".$username."','".$activity."']\r\n");
			fclose($fp);
		}
	}

	public function createJavaLog($PageLoc, $activity, $username) {
		$root = Yii::getAlias('@webroot/java_logs.txt');
		$fp = fopen($root, 'a');
		fwrite($fp, "['".yii::$app->controller->getNowTime()."','".$PageLoc."','".$activity."','".$username."']\r\n");
		fclose($fp);
	}

	public function emailSetup() {
		$mail = new PHPMailer; //(true);  					// Passing `true` enables exceptions

	//Server settings
		$mail_conf = yii::$app->params['mail'];
		if ($mail_conf['Enabled']) {
			
			$mail->isSMTP();									// Set mailer to use SMTP
			if($mail_conf['Debug']) {$mail->SMTPDebug = $mail_conf['Debug'];}	// Enable verbose debug output  2= good,  4 = connection
			$mail->Host = $mail_conf['Host'];
			$mail->Port = $mail_conf['Port'];
			$mail->SMTPSecure = $mail_conf['SMTPSecure'];		// Enable TLS encryption, `ssl` also accepted
			$mail->SMTPAuth = $mail_conf['SMTPAuth'];			// Enable SMTP authentication
			$mail->Username = $mail_conf['Username'];			// SMTP username
			$mail->Password = $mail_conf['Password'];			// SMTP password
			//$mail->Timeout=900;

			$mail->addCustomHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');

			$mail->isHTML(true);								  // Set email format to HTML
			return $mail;
		} else { return false; }
	}

	public function mergeRemarks($old,$nowRemakrs) {
		if($old) {
			$remarksOld = json_decode($old,true);
			array_push($remarksOld,$nowRemakrs);
		} else {
			$remarksOld = [
				$nowRemakrs,
			];
		}
		return json_encode($remarksOld,true);
	}

	public function sendVerifyEmail($email,$type='new',$model=null) {
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			if($type=='new') { $welcome = 'Welcome to the AGC '.$model->first_name.'!,'; }
			else if ($type=='update') { $welcome = 'Hi '.$model->first_name.','; }

			$mail = $this->emailSetup();
			if ($mail) {
			$mail->addCustomHeader('List-Unsubscribe', '<'.yii::$app->params['wp_site'].'/comms.php?unsubscribe='.$email.'>');

		// Only send out email to user after waiting 10 min.
			if(isset($model->badge_number)) {
				if($model->email_vrfy==1) {	return; }
				if(isset($_SESSION['emails'])) {
					//$badge_number = $model->badge_number;
					if(isset($_SESSION['emails'][$model->badge_number])) {
						$myTest = $_SESSION['emails'][$model->badge_number];
						if($myTest < time()) {
							$_SESSION['emails'][$model->badge_number] = time() + (10 * 60);
						}else {
							//Yii::$app->getSession()->setFlash('error', 'Email aleard sent to '.$model->first_name.'. Wait 10min.');
							return;
						}
					} else { $_SESSION['emails'][$model->badge_number]= time() + (10 * 60); }
				} else { $_SESSION['emails']=[$model->badge_number => time() + (10 * 60)]; }
				$mail->addAddress($email, $model->first_name);
			} else { $mail->addAddress($email); }

			if($type=='new') {
				$extra = '<p>Your new Badge Number is: <b>'.$model->badge_number.'</b><br />And your Login code is: <b>'.$model->qrcode.'</b><br />This information wil also be on the back of your badge.</p>';
			} else { $extra=''; }
				
			try {
				//Recipients
				$mail->setFrom(yii::$app->params['mail']['Username'], 'AGC Range');

				//$mail->addAddress('contact@example.com');			   // Name is optional
				//$mail->addReplyTo('info@example.com', 'Information');
				//$mail->addCC('cc@example.com');
				//$mail->addBCC('bcc@example.com');

				//Attachments
				//$mail->addAttachment('/var/tmp/file.tar.gz');		 // Add attachments
				//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');	// Optional name

				// Compose a simple HTML email message
				$message = "<!DOCTYPE html><html>\n<body>\n" .
					'<p>'.$welcome.'</p>'.$extra.
					'<p>Please take a moment to verify your Email by clicking on the link below.</p>' .
					'<p><a href="'.yii::$app->params['wp_site'].'/comms.php?verifyemail='.$email.'"> Verify your Email: '.$email.' </a></p><br>' .
					'<p>Thank You,<br />Associated Gun Clubs of Baltimore.</p>' ."\n".
					'<a href="'.yii::$app->params['wp_site'].'">'.yii::$app->params['wp_site'].'</a>' ."\n".
					"<br /><br><p>P.S. We know our email probably went to the spam folder. Please tell your provider It's not Spam!. </p>\n".
					'<br /><p> or Click here to <a href="'.yii::$app->params['wp_site'].'/comms.php?unsubscribe='.$email.'">remove your email from our List</a>.</p>'. "\n".
					"</body>\n</html>";

					//Content
				$mail->Subject = 'AGC Email Verification';
				$mail->Body	= $message;
				$mail->AltBody = $welcome."\n\n".
					"Please take a moment to verify your Email by clicking on the link below.\n\n".
					yii::$app->params['wp_site'].'/comms.php?verifyemail='.$email."\n\n".
					"Thank You,\nAssociated Gun Clubs of Baltimore.";

				$mail->send();
				yii::$app->controller->createLog(true, 'Email Verify', "Sent to ".$email."','".$model->badge_number);
				return 'Message has been sent';
			} catch (Exception $e) {
				$mail->SMTPDebug = 3;
				Yii::$app->response->data .= 'Message could not be sent.';
				Yii::$app->response->data .= 'Mailer Error: ' . $mail->ErrorInfo;
				yii::$app->controller->createLog(true, 'trex Verify Email Error: ', var_export($mail->ErrorInfo,true));
			}
			return true;
			}
		}
		return false;
	}
}
