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
		'Accounts' => ['accounts/index','accounts/create','accounts/update','accounts/view','accounts/delete','accounts/reset-password','accounts/request-password-reset'],
		'Admin' => ['site/admin-menu','privileges/create','privileges/delete','privileges/index','privileges/update'],
		'Badges'=>['badges/all','badges/add-certification','badges/api-check','badges/api-generate-renaval-fee','badges/barcode','badges/create','badges/delete-certificate','badges/delete','badges/generate-new-sticker','badges/get-badge-name','badges/get-family-badges','badges/modify','badges/photo-add','badges/photo-crop','badges/post-print-transactions','badges/print','badges/print-rcpt','badges/renew-membership','badges/delete-renewal','badges/rename','badges/scan-badge','badges/test','badges/update-renewal','badges/view-certificate','badges/view-certifications-list','badges/update-certificate','badges/view-renewal-history','badges/view-remarks-history','badges/view-subscriptions','badges/view-work-credits','badges/view-work-credits-log',],
		'Calendar' =>['calendar/all','calendar/approve','calendar/close','calendar/create','calendar/conflict','calendar/delete','calendar/get-event-types','calendar/inactive','calendar/index','calendar/open-range','calendar/recur','calendar/republish','calendar/showed','calendar/update','calendar/view'],
		'CalSetup' => ['cal-setup/index','cal-setup/clubs','cal-setup/updateclu','cal-setup/facility','cal-setup/updatefac','cal-setup/rangestatus','cal-setup/updateran','cal-setup/eventstatus','cal-setup/updateeven'],
		'Clubs' => ['clubs/index','clubs/create','clubs/delete','clubs/update','clubs/view','clubs/delete-X','clubs/badge-rosters'],
		'MassEmail' => ['mass-email/create','mass-email/index','mass-email/update','mass-email/send','mass-email/process'],
		'Events' => ['events/approve','events/add-att','events/index','events/close','events/create','events/delete','events/reg','events/return','events/remove-att','events/update','events/view'],
		'Membership Type'=>['membership-type/ajaxmoney-convert','membership-type/index','membership-type/create','membership-type/update','membership-type/delete-X','membership-type/view','membership-type/fees-by-type'],
		'Guest' => ['guest/all','guest/modify','guest/update','guest/stats','guest/delete'],
		'Index' => ['site/no-email','site/verify'],
		'LegeslativeEmails'=>['legelemail/index','legelemail/create','legelemail/update','legelemail/delete'],
		'Params' => ['params/update'],
		'Range Badge Database' => ['range-badge-database/index','range-badge-database/view','range-badge-database/delete','range-badge-database/update'],
		'Rules'=> ['rules/index','rules/create','rules/update','rules/view'],
		'sales' => ['payment/converge','payment/index','payment/inventory','sales/all','sales/create','sales/stock','sales/update','sales/inventory'], // Test Pages
		'violations' => ['violations/all','violations/board','violations/create','violations/delete','violations/report','violations/stats','violations/update'],
		'Work Credits'=>['work-credits/all','work-credits/approve','work-credits/update','work-credits/delete'],
	];

	public $adminPermission = [
		'Accounts' => ['accounts/index','accounts/create','accounts/update','accounts/view','accounts/reset-password','accounts/request-password-reset'],
		'Admin' => ['site/admin-menu'],
		'Badges'=>['badges/all','badges/add-certification','badges/api-generate-renaval-fee','badges/barcode','badges/create','badges/delete-certificate','badges/generate-new-sticker','badges/get-badge-name','badges/get-family-badges','badges/modify','badges/photo-add','badges/photo-crop','badges/post-print-transactions','badges/print','badges/print-rcpt','badges/renew-membership','badges/rename','badges/scan-badge','badges/test','badges/update-renewal','badges/delete-renewal','badges/view-certificate','badges/view-certifications-list','badges/update-certificate','badges/view-renewal-history','badges/view-remarks-history','badges/view-subscriptions','badges/view-work-credits','badges/view-work-credits-log'],
		'Calendar' =>['calendar/all','calendar/approve','calendar/close','calendar/create','calendar/conflict','calendar/delete','calendar/get-event-types','calendar/inactive','calendar/index','calendar/open-range','calendar/recur','calendar/republish','calendar/update'],
		'Membership Type'=>['membership-type/ajaxmoney-convert','membership-type/index','membership-type/create','membership-type/update','membership-type/view','membership-type/fees-by-type'],
		'Clubs' => ['clubs/index','clubs/create','clubs/update','clubs/view','clubs/badge-rosters'],
		'Events' => ['events/approve','events/add-att','events/index','events/close','events/create','events/delete','events/reg','events/remove-att','events/update','events/view'],
		'Guest' => ['guest/all','guest/modify','guest/update','guest/delete'],
		'Rules'=> ['rules/index','rules/create','rules/update','rules/view'],
		'sales' => ['sales/all','sales/create','sales/stock'],
		'violations' => ['violations/all','violations/board','violations/create','violations/delete','violations/report','violations/stats','violations/update'],
		'Work Credits'=>['work-credits/all','work-credits/approve','work-credits/update'],
	];

	public $cashierPermission = [
		'Badges'=>['badges/all','badges/add-certification','badges/api-generate-renaval-fee','badges/barcode','badges/create','badges/delete-certificate','badges/generate-new-sticker','badges/get-family-badges','badges/modify','badges/photo-add','badges/photo-crop','badges/post-print-transactions','badges/print','badges/print-rcpt','badges/renew-membership','badges/rename','badges/scan-badge','badges/test','badges/update-renewal','badges/delete-renewal','badges/view-certificate','badges/view-certifications-list','badges/update-certificate','badges/view-renewal-history','badges/view-remarks-history','badges/view-subscriptions','badges/view-work-credits','badges/view-work-credits-log'],
		'Membership Type'=>['membership-type/ajaxmoney-convert','membership-type/index','membership-type/create','membership-type/update','membership-type/view','membership-type/fees-by-type'],
		'Clubs' => ['clubs/index','clubs/view','clubs/badge-rosters'],
		'sales' => ['sales/all'],
		'Guest' => ['guest/update'],
	];

	public $rsoLeadPermission = [
		'Badges'=>['badges/all','badges/add-certification','badges/api-generate-renaval-fee','badges/generate-new-sticker','badges/get-badge-name','badges/get-family-badges','badges/modify','badges/photo-add','badges/photo-crop','badges/post-print-transactions','badges/print-rcpt','badges/renew-membership','badges/test','badges/view-certifications-list','badges/view-renewal-history','badges/view-remarks-history','badges/view-subscriptions','badges/view-work-credits','badges/view-work-credits-log'],
		'Calendar' =>['calendar/all','calendar/showed','calendar/index'],
		'Events' => ['events/approve','events/index','events/close','events/create','events/reg','events/return','events/remove-att','events/view'],
		'Membership Type'=>['membership-type/ajaxmoney-convert','membership-type/fees-by-type'],
		'Guest' => ['guest/all','guest/modify','guest/update'],
		'sales' => ['sales/all'],
		'violations' => ['violations/all','violations/create','violations/report','violations/update'],
	];

	public $rsoPermission = [
		'Badges'=>['badges/all','badges/add-certification','badges/api-generate-renaval-fee','badges/generate-new-sticker','badges/get-badge-name','badges/get-family-badges','badges/modify','badges/photo-add','badges/photo-crop','badges/post-print-transactions','badges/print-rcpt','badges/renew-membership','badges/test','badges/view-certifications-list','badges/view-renewal-history','badges/view-remarks-history','badges/view-subscriptions','badges/view-work-credits','badges/view-work-credits-log'],
		'Calendar' =>['calendar/all','calendar/showed','calendar/index'],
		'Events' => ['events/approve','events/index','events/close','events/create','events/reg','events/return','events/remove-att','events/view'],
		'Membership Type'=>['membership-type/ajaxmoney-convert','membership-type/fees-by-type'],
		'Guest' => ['guest/all','guest/modify','guest/update'],
		'sales' => ['sales/all'],
		'violations' => ['violations/all','violations/create','violations/update'],
	];

	public $viewPermission = [
		'Badges'=>['badges/all','badges/get-badge-name','badges/get-family-badges','badges/post-print-transactions','badges/print-rcpt','badges/view-certifications-list','badges/view-renewal-history','badges/view-remarks-history','badges/view-subscriptions','badges/view-work-credits','badges/view-work-credits-log'],
		'Events' => ['events/index','events/view'],
		'Guest' => ['guest/all'],
		'violations' => ['violations/all'],
		'Work Credits'=>['work-credits/all'],
	];

	public $workcreditPermission = [
		'Badges'=>['badges/all','badges/get-badge-name','badges/view-certificate','badges/view-certifications-list','badges/view-renewal-history','badges/view-remarks-history','badges/view-subscriptions','badges/view-work-credits','badges/view-work-credits-log'],
		'Events' => ['events/create','events/index','events/reg','events/view'],
		'Guest' => ['guest/update'],
		'Work Credits'=>['work-credits/add'],
	];

	public $cioPermission = [
		'Badges'=>['badges/restrict','badges/get-badge-name','badges/view-certificate','badges/view-certifications-list','badges/view-renewal-history','badges/view-remarks-history','badges/view-subscriptions','badges/view-work-credits','badges/view-work-credits-log'],
		'Calendar' =>['calendar/create','calendar/index','calendar/conflict','calendar/get-event-types','calendar/inactive','calendar/open-range','calendar/update'],
		'Events' => ['events/index','events/add-att','events/create','events/reg','events/view'],
		'Guest' => ['guest/update'],
	];

	public $userPermission = [
		'Badges'=>['badges/restrict','badges/view-certificate','badges/view-certifications-list','badges/view-renewal-history','badges/view-remarks-history','badges/view-subscriptions','badges/view-work-credits','badges/view-work-credits-log'],
		'Guest' => ['guest/update'],
	];

	public $calendarPermission = [
		'Calendar' =>['calendar/create','calendar/index','calendar/conflict','calendar/delete','calendar/get-event-types','calendar/inactive','calendar/index','calendar/open-range','calendar/recur','calendar/republish','calendar/update'],
		'Badges'=>['badges/restrict','badges/view-certificate','badges/view-certifications-list','badges/view-renewal-history','badges/view-remarks-history','badges/view-subscriptions','badges/view-work-credits','badges/view-work-credits-log'],
		'Guest' => ['guest/update'],
	];

	public $chairmanPermission = [
		'Calendar' => ['calendar/close','calendar/recur'],
	];

	public $AllPermission = [
		'Badges'=>['badges/api-zip','badges/api-request-family','badges/get-badge-details','badges/index','badges/update','badges/view'],
		'Guest' => ['guest/add','guest/addcredit','guest/create','guest/index','guest/out','guest/sticky-form','guest/view'],
		'payments'=>['payment/charge'],
		'sales' => ['sales/index','sales/print-rcpt','sales/purchases'],
		'Site' => ['site/index','site/error','site/logout','site/login','site/login-member'],
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
				throw new \yii\web\UnauthorizedHttpException();
			}
		} else if (yii::$app->user->isGuest) {
			// Pages that dont require login
			if((Yii::$app->controller->id."/".Yii::$app->controller->action->id=='events/reg') ||
				(Yii::$app->controller->id."/".Yii::$app->controller->action->id=='site/verify') ||
				(Yii::$app->controller->id."/".Yii::$app->controller->action->id=='site/no-email') ||
				(Yii::$app->controller->id."/".Yii::$app->controller->action->id=='site/login') ||
				(Yii::$app->controller->id."/".Yii::$app->controller->action->id=='site/login-member') ||
				(Yii::$app->controller->id."/".Yii::$app->controller->action->id=='badges/get-badge-name') ||
				(Yii::$app->controller->id."/".Yii::$app->controller->action->id=='clubs/badge-rosters') ||
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
					'<p>'.$welcome.'</p>' .
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
				return 'Message has been sent';
				yii::$app->controller->createLog(true, 'Email Verify', "Sent to ".$email."','".$model->badge_number);
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