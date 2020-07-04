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
// Removed Pages
// 'badges/import-badges','badges/import-recive','clubs/import-form','work-credits/import'

	public $rootAdminPermission = [
		'Accounts' => ['accounts/index','accounts/create','accounts/update','accounts/view','accounts/delete','accounts/reset-password','accounts/request-password-reset'],
		'Admin' => ['is_root','badge/log-error','badge/badge-print','badge/index','badge/users-index','badge/edit-user','badge/view-user','badge/create-user','badge/admin-function','badge/work-credit-entry','badge/brows-work-credits','badge/work-credit-menu','badge/club-name-look-up','badge/club-name-create','badge/club-name-edit','badge/work-credit-transfer','badge/create', 'badge/update', 'site/logout','site/login','site/new-badge','privileges/create','privileges/delete','privileges/index','privileges/update'],
		'Badges'=>['badges/all','badges/add-certification','badges/api-generate-renaval-fee','badges/api-check','badges/api-request-family','badges/barcode','badges/create','badges/delete','badges/generate-new-sticker','badges/get-badge-details','badges/get-family-badges','badges/index','badges/modify','badges/photo-add','badges/photo-crop','badges/post-print-transactions','badges/print','badges/print-rcpt','badges/renew-membership','badges/delete-renewal','badges/rename','badges/scan-badge','badges/test','badges/update','badges/update-renewal','badges/view','badges/view-renewal-history','badges/view-remarks-history','badges/view-subscriptions','badges/view-work-credits','badges/view-work-credits-log',],
		'Calendar' =>['calendar/all','calendar/approve','calendar/close','calendar/create','calendar/conflict','calendar/delete','calendar/inactive','calendar/index','calendar/open-range','calendar/recur','calendar/republish','calendar/update','calendar/view'],
		'CalSetup' => ['cal-setup/index','cal-setup/clubs','cal-setup/updateclu','cal-setup/facility','cal-setup/updatefac','cal-setup/rangestatus','cal-setup/updateran','cal-setup/eventstatus','cal-setup/updateeven'],
		'Clubs' => ['clubs/index','clubs/create','clubs/delete','clubs/update','clubs/view','clubs/delete-X','clubs/badge-rosters'],
		'MassEmail' => ['mass-email/create','mass-email/index','mass-email/update','mass-email/send','mass-email/process'],
		'Events' => ['events/approve','events/add-att','events/index','events/close','events/create','events/delete','events/reg','events/return','events/remove-att','events/update','events/view','badges/get-badge-name'],
		'Fees Structure'=>['fee-structure/ajaxmoney-convert','fee-structure/index','fee-structure/create','fee-structure/update','fee-structure/delete-X','fee-structure/view','fee-structure/fees-by-type','badges/view-certificate','badges/view-certifications-list','badges/update-certificate','badges/delete-certificate'],
		'Guest' => ['guest/all','guest/index','guest/view','guest/add','guest/addcredit','guest/create','guest/modify','guest/update','guest/stats','guest/out','guest/delete','guest/sticky-form'],
		'Index' => ['site/index', 'site/error', 'site/logout','site/login','site/login-member','site/no-email','site/new-badge','site/verify'],
		'LegeslativeEmails'=>['legelemail/index','legelemail/create','legelemail/import','legelemail/update','legelemail/delete'],
		'Params' => ['params/update'],
		'Range Badge Database' => ['range-badge-database/index','range-badge-database/view','range-badge-database/delete','range-badge-database/update'],
		'Rules'=> ['rules/index','rules/create','rules/update','rules/view'],
		'sales' => ['payment/charge','payment/refreshtoken','sales/index','sales/purchases','sales/all','sales/stock','sales/update','sales/print-rcpt','payment/oauth','payment/index2','payment/oauth2','payment/disconnect','payment/index','payment/reconnect', // QuickBooks Related
			'sales/qb-items','payment/oauthopen','payment/refreshtoken','sales/inventory','payment/inventory',
			'payment/chargereq','payment/info','payment/invoice','payment/process','payment/purchase','payment/page'], // Test Pages
		'paypal'=> ['payment/paypalsetup','payment/paypal','payment/paypalprocess'],
		'violations' => ['violations/all','violations/board','violations/create','violations/delete','violations/index','violations/report','violations/stats','violations/update','violations/view'],
		'Work Credits'=>['work-credits/all','work-credits/approve','work-credits/index','work-credits/sticky-form','work-credits/create','work-credits/update','work-credits/credit-transfer','work-credits/transfer-confirm','work-credits/transfer-view','work-credits/view','work-credits/delete','work-credits/transfer-form'],
	];

	public $adminPermission = [
		'Accounts' => ['accounts/index','accounts/create','accounts/update','accounts/view','accounts/reset-password','accounts/request-password-reset'],
		'Index' => ['site/index','site/error','site/logout','site/login','site/login-member','site/new-badge'],
		'Admin' => ['badge/log-error','events/approve','badge/badge-print','badge/index','badge/users-index','badge/edit-user','badge/view-user','badge/create-user','badge/admin-function','badge/work-credit-entry','badge/brows-work-credits','badge/work-credit-menu','badge/club-name-look-up','badge/club-name-create','badge/club-name-edit','badge/work-credit-transfer','badge/create', 'badge/update', 'site/logout','site/login','site/new-badge'],
		'Badges'=>['badges/all','badges/add-certification','badges/api-generate-renaval-fee','badges/api-check','badges/api-request-family','badges/barcode','badges/create','badges/generate-new-sticker','badges/get-badge-details','badges/get-family-badges','badges/index','badges/modify','badges/photo-add','badges/photo-crop','badges/post-print-transactions','badges/print','badges/print-rcpt','badges/renew-membership','badges/rename','badges/scan-badge','badges/test','badges/update','badges/update-renewal','badges/delete-renewal','badges/view','badges/view-renewal-history','badges/view-remarks-history','badges/view-subscriptions','badges/view-work-credits','badges/view-work-credits-log'],
		'Calendar' =>['calendar/all','calendar/approve','calendar/close','calendar/create','calendar/conflict','calendar/delete','calendar/inactive','calendar/index','calendar/open-range','calendar/recur','calendar/republish','calendar/update'],
		'Fees Structure'=>['fee-structure/ajaxmoney-convert','fee-structure/index','fee-structure/create','fee-structure/update','fee-structure/delete-X','fee-structure/view','fee-structure/fees-by-type','badges/view-certificate','badges/view-certifications-list','badges/update-certificate','badges/delete-certificate'],
		'Clubs' => ['clubs/index','clubs/create','clubs/update','clubs/view','clubs/badge-rosters'],
		'Events' => ['events/approve','events/add-att','events/index','events/close','events/create','events/delete','events/reg','events/remove-att','events/update','events/view','badges/get-badge-name'],
		'Guest' => ['guest/all','guest/index','guest/view','guest/add','guest/addcredit','guest/create','guest/modify','guest/update','guest/out','guest/delete','guest/sticky-form'],
		'Rules'=> ['rules/index','rules/create','rules/update','rules/view'],
		'sales' => ['payment/charge','payment/refreshtoken','sales/index','sales/print-rcpt','sales/purchases','sales/all'],
		'violations' => ['violations/all','violations/board','violations/create','violations/delete','violations/index','violations/report','violations/stats','violations/update','violations/view'],
		'Work Credits'=>['work-credits/all','work-credits/approve','work-credits/index','work-credits/sticky-form','work-credits/create','work-credits/update','work-credits/credit-transfer','work-credits/transfer-confirm','work-credits/transfer-view','work-credits/view','work-credits/transfer-form'],
	];

	public $cashierPermission = [
		'Index' => ['site/index','site/error','site/logout','site/login','site/login-member','site/new-badge'],
		'Admin'=>['badge/log-error'],
		'Badges'=>['badges/all','badges/add-certification','badges/api-generate-renaval-fee','badges/api-check','badges/api-request-family','badges/barcode','badges/create','badges/generate-new-sticker','badges/get-badge-details','badges/get-family-badges','badges/index','badges/modify','badges/photo-add','badges/photo-crop','badges/post-print-transactions','badges/print','badges/print-rcpt','badges/renew-membership','badges/rename','badges/scan-badge','badges/test','badges/update','badges/update-renewal','badges/delete-renewal','badges/view','badges/view-renewal-history','badges/view-remarks-history','badges/view-subscriptions','badges/view-work-credits','badges/view-work-credits-log'],
		'Fees Structure'=>['fee-structure/ajaxmoney-convert','fee-structure/index','fee-structure/create','fee-structure/update','fee-structure/delete-X','fee-structure/view','fee-structure/fees-by-type','badges/view-certificate','badges/view-certifications-list','badges/update-certificate','badges/delete-certificate'],
		'Clubs' => ['clubs/index','clubs/view','clubs/badge-rosters'],
		'sales' => ['payment/charge','payment/refreshtoken','sales/index','sales/print-rcpt','sales/purchases','sales/all'],

		'Guest' => ['guest/index','guest/add','guest/addcredit','guest/view','guest/create','guest/update','guest/out','guest/sticky-form'],
		'violations' => ['violations/index','violations/index','violations/view'],
		'Work Credits'=>['work-credits/index','work-credits/sticky-form','work-credits/create','work-credits/credit-transfer','work-credits/transfer-confirm','work-credits/transfer-view','work-credits/view','work-credits/transfer-form'],
	];

	public $rsoLeadPermission = [
		'Index' => ['site/index','site/error','site/logout','site/login','site/login-member'],
		'Admin'=>['badge/log-error'],
		'Badges'=>['badges/all','badges/add-certification','badges/api-generate-renaval-fee','badges/api-check','badges/api-request-family','badges/generate-new-sticker','badges/get-badge-details','badges/get-family-badges','badges/index','badges/modify','badges/post-print-transactions','badges/print-rcpt','badges/renew-membership','badges/test','badges/update','badges/view','badges/view-certifications-list','badges/view-renewal-history','badges/view-remarks-history','badges/view-subscriptions','badges/view-work-credits','badges/view-work-credits-log'],
		'Events' => ['events/approve','events/index','events/close','events/create','events/reg','events/return','events/remove-att','events/view','badges/get-badge-name'],
		'Fees Structure'=>['fee-structure/ajaxmoney-convert','fee-structure/fees-by-type'],
		'Guest' => ['guest/all','guest/index','guest/add','guest/addcredit','guest/view','guest/create','guest/modify','badges/photo-add','badges/photo-crop','guest/update','guest/out','guest/sticky-form'],
		'sales' => ['payment/charge','payment/refreshtoken','sales/index','sales/print-rcpt','sales/purchases','sales/all'],
		'violations' => ['violations/all','violations/index','violations/create','violations/index','violations/report','violations/update','violations/view'],
		'Work Credits'=>['work-credits/index','work-credits/sticky-form','work-credits/create','work-credits/credit-transfer','work-credits/transfer-confirm','work-credits/transfer-view','work-credits/view','work-credits/transfer-form'],
	];

	public $rsoPermission = [
		'Index' => ['site/index','site/error','site/logout','site/login','site/login-member'],
		'Admin'=>['badge/log-error'],
		'Badges'=>['badges/all','badges/add-certification','badges/api-generate-renaval-fee','badges/api-check','badges/api-request-family','badges/generate-new-sticker','badges/get-badge-details','badges/get-family-badges','badges/index','badges/modify','badges/photo-add','badges/photo-crop','badges/post-print-transactions','badges/print-rcpt','badges/renew-membership','badges/test','badges/update','badges/view','badges/view-certifications-list','badges/view-renewal-history','badges/view-remarks-history','badges/view-subscriptions','badges/view-work-credits','badges/view-work-credits-log'],
		'Events' => ['events/approve','events/index','events/close','events/create','events/reg','events/return','events/remove-att','events/view','badges/get-badge-name'],
		'Fees Structure'=>['fee-structure/ajaxmoney-convert','fee-structure/fees-by-type'],
		'Guest' => ['guest/all','guest/index','guest/add','guest/addcredit','guest/view','guest/create','guest/modify','guest/update','guest/out','guest/sticky-form'],
		'sales' => ['payment/charge','payment/refreshtoken','sales/index','sales/print-rcpt','sales/purchases','sales/all'],
		'violations' => ['violations/all','violations/index','violations/create','violations/index','violations/update','violations/view'],
		'Work Credits'=>['work-credits/index','work-credits/sticky-form','work-credits/create','work-credits/credit-transfer','work-credits/transfer-confirm','work-credits/transfer-view','work-credits/view','work-credits/transfer-form'],
	];

	public $viewPermission = [
		'Index' => ['site/index','site/error','site/logout','site/login','site/login-member','site/new-badge'],
		'Admin'=>['badge/log-error'],
		'Badges'=>['badges/all','badges/api-check','badges/get-badge-details','badges/get-family-badges','badges/index','badges/post-print-transactions','badges/print-rcpt','badges/update','badges/view','badges/view-certifications-list','badges/view-renewal-history','badges/view-remarks-history','badges/view-subscriptions','badges/view-work-credits','badges/view-work-credits-log'],
		'Events' => ['events/index','events/view','badges/get-badge-name'],
		'Guest' => ['guest/all','guest/index','guest/add','guest/addcredit','guest/view','guest/create','guest/out','guest/sticky-form'],
		'sales' => ['payment/charge','payment/refreshtoken','sales/index','sales/print-rcpt','sales/purchases','badges/api-request-family'],
		'violations' => ['violations/all','violations/index','violations/index','violations/view'],
		'Work Credits'=>['work-credits/all','work-credits/index','work-credits/sticky-form','work-credits/create','work-credits/credit-transfer','work-credits/transfer-confirm','work-credits/transfer-view','work-credits/view','work-credits/transfer-form'],
	];

	public $workcreditPermission = [
		'Index' => ['site/index','site/error','site/logout','site/login','site/login-member','site/new-badge'],
		'Admin'=>['badge/log-error'],
		'Badges'=>['badges/all','badges/api-check','badges/get-badge-details','badges/index','badges/update','badges/view','badges/view-certificate','badges/view-certifications-list','badges/view-renewal-history','badges/view-remarks-history','badges/view-subscriptions','badges/view-work-credits','badges/view-work-credits-log'],
		'Events' => ['events/create','events/index','events/reg','events/view','badges/get-badge-name'],
		'Guest' => ['guest/index','guest/add','guest/addcredit','guest/view','guest/create','guest/update','guest/out','guest/sticky-form'],
		'sales' => ['payment/charge','payment/refreshtoken','sales/index','sales/print-rcpt','sales/purchases','badges/api-request-family'],
		'violations' => ['violations/index','violations/index','violations/view'],
		'Work Credits'=>['work-credits/add','work-credits/index','work-credits/sticky-form','work-credits/create','work-credits/credit-transfer','work-credits/transfer-confirm','work-credits/transfer-view','work-credits/view','work-credits/transfer-form'],
	];

	public $cioPermission = [
		'Index' => ['site/index','site/error','site/logout','site/login','site/login-member','site/new-badge'],
		'Admin'=>['badge/log-error'],
		'Badges'=>['badges/restrict','badges/api-check','badges/get-badge-details','badges/index','badges/update','badges/view','badges/view-certificate','badges/view-certifications-list','badges/view-renewal-history','badges/view-remarks-history','badges/view-subscriptions','badges/view-work-credits','badges/view-work-credits-log'],
		'Calendar' =>['calendar/create','calendar/index','calendar/conflict','calendar/inactive','calendar/open-range','calendar/update'],
		'Events' => ['events/index','events/add-att','events/create','events/reg','events/view','badges/get-badge-name'],
		'Guest' => ['guest/index','guest/add','guest/addcredit','guest/view','guest/create','guest/update','guest/out','guest/sticky-form'],
		'sales' => ['payment/charge','payment/refreshtoken','sales/index','sales/print-rcpt','sales/purchases','badges/api-request-family'],
		'violations' => ['violations/index','violations/index','violations/view'],
		'Work Credits'=>['work-credits/index','work-credits/sticky-form','work-credits/create','work-credits/credit-transfer','work-credits/transfer-confirm','work-credits/transfer-view','work-credits/view','work-credits/transfer-form'],
	];

	public $userPermission = [
		'Index' => ['site/index','site/error','site/logout','site/login','site/login-member','site/new-badge'],
		'Admin'=>['badge/log-error'],
		'Badges'=>['badges/restrict','badges/api-check','badges/get-badge-details','badges/index','badges/update','badges/view','badges/view-certificate','badges/view-certifications-list','badges/view-renewal-history','badges/view-remarks-history','badges/view-subscriptions','badges/view-work-credits','badges/view-work-credits-log'],
		'Guest' => ['guest/index','guest/add','guest/addcredit','guest/view','guest/create','guest/update','guest/out','guest/sticky-form'],
		'sales' => ['payment/charge','payment/refreshtoken','sales/index','sales/print-rcpt','sales/purchases','badges/api-request-family'],
		'violations' => ['violations/index','violations/index','violations/view'],
		'Work Credits'=>['work-credits/index','work-credits/sticky-form','work-credits/create','work-credits/credit-transfer','work-credits/transfer-confirm','work-credits/transfer-view','work-credits/view','work-credits/transfer-form'],
	];

	public $calendarPermission = [
		'Admin'=>['badge/log-error'],
		'Calendar' =>['calendar/create','calendar/index','calendar/conflict','calendar/delete','calendar/inactive','calendar/index','calendar/open-range','calendar/recur','calendar/update'],
		'Badges'=>['badges/restrict','badges/api-check','badges/get-badge-details','badges/index','badges/update','badges/view','badges/view-certificate','badges/view-certifications-list','badges/view-renewal-history','badges/view-remarks-history','badges/view-subscriptions','badges/view-work-credits','badges/view-work-credits-log'],
		'Index' => ['site/index','site/error','site/logout','site/login','site/login-member','site/new-badge'],
		'Guest' => ['guest/index','guest/add','guest/addcredit','guest/view','guest/create','guest/update','guest/out','guest/sticky-form'],
		'sales' => ['payment/charge','payment/refreshtoken','sales/index','sales/print-rcpt','sales/purchases','badges/api-request-family'],
		'violations' => ['violations/index','violations/index','violations/view'],
		'Work Credits'=>['work-credits/index','work-credits/sticky-form','work-credits/create','work-credits/credit-transfer','work-credits/transfer-confirm','work-credits/transfer-view','work-credits/view','work-credits/transfer-form'],
	
	];

	public $chairmanPermission = [
		'extras' => ['calendar/close'],
	];

	// Used for Importing CVS data
	public $creditArray = ['badgenum','workdate','workhours','project','auth','status','last_update','procdate','who'];

	public function beforeAction($event) {

		if(!yii::$app->user->isGuest) {
			if(!$this->hasPermission(Yii::$app->controller->id."/".Yii::$app->controller->action->id)){
				throw new \yii\web\UnauthorizedHttpException();
			}
		} else if (yii::$app->user->isGuest) {
			// Pages that dont require login
			if((Yii::$app->controller->id."/".Yii::$app->controller->action->id=='events/reg') ||
				(Yii::$app->controller->id."/".Yii::$app->controller->action->id=='badges/get-badge-name') ||
				(Yii::$app->controller->id."/".Yii::$app->controller->action->id=='payment/info') ||
				(Yii::$app->controller->id."/".Yii::$app->controller->action->id=='payment/paypal') ||
				(Yii::$app->controller->id."/".Yii::$app->controller->action->id=='site/verify') ||
				(Yii::$app->controller->id."/".Yii::$app->controller->action->id=='site/no-email') ||
				(Yii::$app->controller->id."/".Yii::$app->controller->action->id=='site/login') ||
				(Yii::$app->controller->id."/".Yii::$app->controller->action->id=='site/login-member') ||
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
		return base64_encode(implode(";",$myNames_a));
	}

	public function decodeBadgeName($badge_nu) {
		if($badge_nu==0) {return 'Admin';}
		$myNames = explode(';',base64_decode($_SESSION['names']));
		foreach($myNames as $id){
			$expNmaes=explode(',',$id);
			$myNames_a[$expNmaes[0]] = $expNmaes[1];
		}
		return $myNames_a[$badge_nu];
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

	public function createAccessLog($time, $username, $activity) {
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
			$param->save();
		}

		$root = Yii::getAlias('@webroot/access_logs.txt');
		$fp = fopen($root, 'a');
		fwrite($fp, "['".yii::$app->controller->getNowTime()."','".$username."','".$activity."']\r\n");
		fclose($fp);
	}

	public function createLog($isEnabled, $username, $activity, $name='activity') {
		$param = Params::find()->one();
		if(($isEnabled) || ($param->qb_env=='dev')) {
			$root = Yii::getAlias('@webroot/'.$name.'_logs.txt');
			$fp = fopen($root, 'a');
			fwrite($fp, "['".yii::$app->controller->getNowTime()."','".$username."','".$activity."']\r\n");
			fclose($fp);
		}
	}

	public function createCalLog($isEnabled, $username, $activity) {
		$param = Params::find()->one();
		if(($isEnabled) || ($param->qb_env=='dev')) {
			$root = Yii::getAlias('@webroot/calendar_logs.txt');
			$fp = fopen($root, 'a');
			fwrite($fp, "['".yii::$app->controller->getNowTime()."','".$username."','".$activity."']\r\n");
			fclose($fp);
		}
	}

	public function createEmailLog($isEnabled, $username, $activity) {
		$param = Params::find()->one();
		if(($isEnabled) || ($param->qb_env=='dev')) {
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
		$mail = new PHPMailer; //(true);  						// Passing `true` enables exceptions

	//Server settings
		$mail->isSMTP();									// Set mailer to use SMTP
	//	$mail->SMTPDebug = 3;								// Enable verbose debug output  2= good,  4 = connection
		$mail->Host = 'associatedgunclubs.org';
		$mail->Port = 587;
		$mail->SMTPSecure = 'tls';							// Enable TLS encryption, `ssl` also accepted
		$mail->SMTPAuth = true;								// Enable SMTP authentication
		$mail->Username = "<some@email.com>";				// SMTP username
		$mail->Password = "<your Password>";				// SMTP password
		//$mail->Timeout=900;

		$mail->addCustomHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');

		$mail->isHTML(true);								  // Set email format to HTML
		return $mail;
	}

	public function mergeRemarks($old,$nowRemakrs) {
		$remarksOld = json_decode($old,true);
		if($remarksOld != '') {
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
			if($type=='new') { $welcome = 'Welcome to the AGC!'; }
			else if ($type=='update') { $welcome = 'Hi '.$model->first_name.','; }

			$mail = $this->emailSetup();
			$mail->addCustomHeader('List-Unsubscribe', '<https://agcrange.org/comms.php?unsubscribe='.$email.'>');

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

			try {
				//Recipients
				$mail->setFrom('noreply@associatedgunclubs.org', 'AGC Range');

				//$mail->addAddress('contact@example.com');			   // Name is optional
				//$mail->addReplyTo('info@example.com', 'Information');
				//$mail->addCC('cc@example.com');
				//$mail->addBCC('bcc@example.com');

				//Attachments
				//$mail->addAttachment('/var/tmp/file.tar.gz');		 // Add attachments
				//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');	// Optional name

				// Compose a simple HTML email message
				$message = "<!DOCTYPE html><html>\n<body>\n" .
					'<p>'.$welcome.'</p>' .
					'<p>Please take a moment to verify your Email by clicking on the link below.</p>' .
					'<p><a href="https://agcrange.org/comms.php?verifyemail='.$email.'"> Verify your Email: '.$email.' </a></p><br>' .
					'<p>Thank You,<br />Associated Gun Clubs of Baltimore.</p>' ."\n".
					'<a href="https://agcrange.org">agcrange.org</a>' ."\n".
					"<br /><br><p>P.S. We know our email probably went to the spam folder. Please tell your provider It's not Spam!. </p>\n".
					'<br /><p> or Click here to <a href="https://agcrange.org/comms.php?unsubscribe='.$email.'">remove your email from our List</a>.</p>'. "\n".
					"</body>\n</html>";

					//Content
				$mail->Subject = 'AGC Email Verification';
				$mail->Body	= $message;
				$mail->AltBody = $welcome."\n\n".
					"Please take a moment to verify your Email by clicking on the link below.\n\n".
					'https://agcrange.org/comms.php?verifyemail='.$email."\n\n".
					"Thank You,\nAssociated Gun Clubs of Baltimore.";

				$mail->send();
				return 'Message has been sent';
				yii::$app->controller->createLog(true, 'Email Verify', "Sent to ".$email."','".$model->badge_number);
			} catch (Exception $e) {
				$mail->SMTPDebug = 3;
				Yii::$app->response->data .= 'Message could not be sent.';
				Yii::$app->response->data .= 'Mailer Error: ' . $mail->ErrorInfo;
				yii::$app->controller->createLog(true, 'trex Verify Email Error: ', var_export($mail->ErrorInfo,true));
			}

			return true;
		} else {
			return false;
		}
	}

}