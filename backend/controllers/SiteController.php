<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\widgets\ActiveForm;
use common\models\LoginForm;
use common\models\User;
use backend\controllers\AdminController;
use backend\models\Badges;
use backend\models\BadgesSm;
use backend\models\clubs;
use backend\models\Guest;
use backend\models\LoginAccess;
use backend\models\Privileges;

/**
 * Site controller
 */

class LoginMemberForm extends \yii\db\ActiveRecord {

	public $barcode;
	public $barcode_c;
	public $barcode_t;
	public $barcode_b;
	public $barcode_pw;
	public $badge;

	private $_identity;

	public function rules() {
		return [
			[['barcode', 'barcode_c', 'barcode_t', 'barcode_b', 'barcode_pw', 'badge'], 'required'],
			[['barcode_c', 'barcode_t', 'barcode_b', 'barcode_pw'], 'string'],
		];
	}

	public function attributeLabels() {
		return [
			'barcode' => 'Barcode',
			'barcode_c' => '',
			'barcode_t' => '',
			'barcode_b' => '',
			'barcode_pw' => '',
			'badge' => 'Badge',
		];
	}

	public function login() {
		$model = new LoginMemberForm();

		if ($model->load(Yii::$app->request->post())) {
			if($model->barcode_pw<>'') {
				$model->barcode_pw = strtoupper($model->barcode_pw);
				$bc_pw = ' '.$model->barcode_pw;
			} else { $bc_pw=''; }

			$oldbarcode = $model->barcode_c.' '.$model->barcode_t.' '.$model->barcode_b.$bc_pw;
			$query = ['badge_number'=> ltrim($model->badge, '0'),'qrcode'=>$oldbarcode];
			$badgeArray = Badges::find()->where($query)->one();

			if(!$badgeArray) { // Strip out leading zeroes from Badge#
				$oldbarcode = $model->barcode_c.' '.$model->barcode_t.' '.ltrim($model->barcode_b, '0').$bc_pw;
				$query = ['badge_number'=> ltrim($model->badge, '0'),'qrcode'=>$oldbarcode];
				$badgeArray = Badges::find()->where($query)->one();
			}

			if(!$badgeArray) { // Strip out leading zeroes from Badge# & Club
				$oldbarcode = ltrim($model->barcode_c, '0').' '.$model->barcode_t.' '.ltrim($model->barcode_b, '0').$bc_pw;
				$query = ['badge_number'=> ltrim($model->badge, '0'),'qrcode'=>$oldbarcode];
				$badgeArray = Badges::find()->where($query)->one();
			}

			if(!$badgeArray) { // Add Leading zeros to badge#
				$oldbarcode = $model->barcode_c.' '.$model->barcode_t.' '.str_pad($model->barcode_b, 5, '0', STR_PAD_LEFT).$bc_pw;
				$query = ['badge_number'=> ltrim($model->badge, '0'),'qrcode'=>$oldbarcode];
				$badgeArray = Badges::find()->where($query)->one();
			}

			if($badgeArray){
				// Is Badge Suspended - Revoked - Retired
				if ($badgeArray->status=='suspended' || $badgeArray->status =='revoked' || $badgeArray->status == 'retired') {
					Yii::$app->getSession()->setFlash('error', 'Badge '.$badgeArray->status.', Please See Staff.',false);
					return false;
				}
				// does member have privileges?
				$_SESSION["badge_number"] = $badgeArray->badge_number;
				$_SESSION["user"] = $badgeArray->first_name.' '.$badgeArray->last_name;
				$_SESSION['names'] = yii::$app->controller->getBadgeList();
				$userArray = User::find()->where(['badge_number'=> $model->badge])->one();
				if($userArray) {
					yii::$app->controller->RotateLog();
					// user has privileges
					$_SESSION['privilege']=json_decode($userArray->privilege);
					$_SESSION['r_user']=$userArray->r_user;
					foreach ($_SESSION['privilege'] as $priv) {
						$chk_priv = Privileges::find()->where(['id'=>$priv])->one();
						if (isset($_SESSION['timeout'])) {
							if ($_SESSION['timeout'] < $chk_priv->timeout) {
							$_SESSION['timeout'] = $chk_priv->timeout; }
						} else { $_SESSION['timeout'] = $chk_priv->timeout; }
					}
					if (strtotime($badgeArray->expires) < strtotime($this->getNowTime())) {
						if(array_intersect([3,6],$_SESSION['privilege'])) { // do nothing
						} else {
							unset($_SESSION);
							Yii::$app->getSession()->setFlash('warning', ' Badge needs to be Renewed! Please See Staff.',false);
							return false;
						}
					}
					return Yii::$app->user->login(User::findIdentity($userArray->id), 0);
				} else {  // Default Privilege for members
					// Is badge current?
					if (strtotime($badgeArray->expires) < strtotime($this->getNowTime())) {
						unset($_SESSION);
						Yii::$app->getSession()->setFlash('warning', ' Badge needs to be Renewed! Please See Staff.',false);
						return false;
					}
					$_SESSION['privilege']=array(5);
					$priv = Privileges::find()->where(['id'=>5])->one();
					$_SESSION['timeout'] = $priv->timeout;
					return Yii::$app->user->login(User::findIdentity(0), 0);
				}
			} else {
				Yii::$app->getSession()->setFlash('error', 'Please Verify your Barcode and Badge Number!  SPACES ARE IMPORTANT');
				return false;
			}
		} else {
			yii::$app->controller->createLog(true, 'SC_LoginMember-Login', 'Not a Post');
		}
		return false;
	}

	public function getNowTime($offset = null) {
		$date = new \DateTime;
		$date->setTimezone(new \DateTimeZone(yii::$app->params['timeZone']));
		if($offset) {
			$date->add(new \DateInterval($offset));
		}
		return $date->format('Y-m-d H:i:s');
	}
}

class SiteController extends AdminController {
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
					   // 'actions' => ['login', 'error'],
						'allow' => true,
					],
					[
					   // 'actions' => ['logout', 'index','new-badge'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
				   // 'logout' => ['post'],
				],
			],
		];
	}

	public function actions() {
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
		];
	}

	public function actionAdminMenu() {
		return $this->render('adminMenu');
	}

	public function actionIndex() {
		$nowDate = date('Y-m-d',strtotime($this->getNowTime()));
		$badges = Badges::find()
				->where(['>', 'expires', $nowDate])
				->all();
		$guests = Guest::find()->where(['is', 'time_out',null])->all();

		return $this->render('index',[
			'badgeCount'=> count($badges),
			'guestCount'=> count($guests)
		]);
	}

	public function actionLogError($PageLoc='', $ErrorData='') {
		if(isset($_SESSION['user'])) {$usr = $_SESSION['user'];} else {$usr='unk';}
		yii::$app->controller->createJavaLog($PageLoc, var_export($ErrorData,true),$usr);
	}

	public function actionLogin() {
		if (!Yii::$app->user->isGuest) {
			return $this->goHome();
		}

		$model = new LoginForm();
		if ($model->load(Yii::$app->request->post())) {
			if($model->login()) {
				$this->log_access("user",$_SERVER['REMOTE_ADDR'],$model->username,'success');
				if (isset($_SESSION['jump']) && $_SESSION['jump'] !='') {
					$jump = base64_decode($_SESSION['jump']);
					unset($_SESSION['jump']);
					$this->redirect($jump);
				} else {
					return $this->goBack();
				}
			} else {
				$this->log_access("user",$_SERVER['REMOTE_ADDR'],$_REQUEST['LoginForm']['username'],'FAIL');
			}
		}
		return $this->render('login', [
			'model' => $model,
		]);
	}

	public function actionLoginMember() {
		if (!Yii::$app->user->isGuest) {
			return $this->goHome();
		}

		$model = new LoginMemberForm();
		if ($model->load(Yii::$app->request->post())) {
			if ($model->login()) {
				$this->log_access("member",$_SERVER['REMOTE_ADDR'],$_SESSION['user'],'success');
				if (isset($_SESSION['jump']) && $_SESSION['jump'] !='') {
					$jump = base64_decode($_SESSION['jump']);
					unset($_SESSION['jump']);
					$this->redirect($jump);
				} else {
					return $this->goBack();
				}
			} else {
				$this->log_access("member",$_SERVER['REMOTE_ADDR'],$_REQUEST['LoginMemberForm']['badge'],'FAIL');
			}
		}
		return $this->render('login-member', [
			'model' => $model,
		]);
	}

	public function actionLogout($url=false) {
		if (in_array(5, $_SESSION['privilege'])) {$ReDir=false;} else {$ReDir=true;}
		Yii::$app->user->logout();
		if (($url) && ($ReDir)) {
			return $this->redirect(['login-member', 'url' => $url]);
		} else {
			return $this->goHome();}
	}

	public function actionNewMember() {
		$nowDate = date('Y-m-d',strtotime($this->getNowTime()));
		$model = New BadgesSm;

		if ($model->load(Yii::$app->request->post())) {
			if (Yii::$app->request->isAjax) {
				Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
				return ActiveForm::validate($model);

			} else {
				$model->created_at = $this->getNowTime();
				$model->remarks = '';
				$model->remarks_temp='';

				$model = (New Badges)->cleanBadgeData($model,true,true,'Self');
				$bg_num =(New Badges)->find()->where(['badge_number'=>$model->badge_number])->count();
				if($bg_num > 0) {
					$model->badge_number = (new Badges)->getFirstFreeBadge();
					$qr=explode(" ",$model->qrcode);
					$model->qrcode=$qr[0]." ".$qr[1]." ".str_pad($model->badge_number, 5, '0', STR_PAD_LEFT)." ".$qr[3];
				}

				$saved=$model->save();
				if($saved) {
					$this->createLog($this->getNowTime(), $model->first_name.' '.$model->last_name, "Self-Registered new Badge','".$model->badge_number);
					Yii::$app->getSession()->setFlash('success', 'Badge Holder Details has been created');
					(New clubs)->saveClub($model->badge_number, $model->club_id, false);

					yii::$app->controller->sendVerifyEmail($model->email,'new',$model);

					//Auto Login!
					$badgeArray = Badges::find()->where(['badge_number'=>$model->badge_number])->one();
					$_SESSION["badge_number"] = $badgeArray->badge_number;
					$_SESSION["user"] = $badgeArray->first_name.' '.$badgeArray->last_name;
					$_SESSION['names'] = yii::$app->controller->getBadgeList();
					$_SESSION['privilege']=array(5);
					$priv = Privileges::find()->where(['id'=>5])->one();
					$_SESSION['timeout'] = $priv->timeout;
					Yii::$app->user->login(User::findIdentity(0), 0);
					$this->createLog($this->getNowTime(), $_SESSION['user'], "Self-Registered new Badge','".$model->badge_number);

					return $this->redirect(['/badges/photo-add', 'badge' => $model->badge_number]);
				} else {
					Yii::$app->getSession()->setFlash('error', 'Something Broke?');
					$errors = $model->getErrors();
					yii::$app->controller->createLog(true, 'trex_self_error', var_export($errors,true));
				}
			}
		}

		$model->badge_number = (new Badges)->getFirstFreeBadge();
		return $this->render('new-member',[
			'model'=> $model
		]);
	}

	public function actionNoEmail($unsubscribe) {
		if (filter_var($unsubscribe, FILTER_VALIDATE_EMAIL)) {
			$sql = "UPDATE badges SET email='', email_vrfy=0 where email='".$unsubscribe."'";
			$command = Yii::$app->db->createCommand($sql);
			$result = $command->execute();

			yii::$app->controller->createLog(true, 'Email UnSubscribe', $unsubscribe);

			if($result) {
				echo "<!DOCTYPE html>\n<html lang='en-US'>".PHP_EOL;
				echo "<head><title>AGC Unsubscribe</title></head>".PHP_EOL;
				echo "The Associated Gun Clubs of Baltimore will miss you!<br />".
				"Your email address: ".$unsubscribe." will be removed promptly.<br /><br />".
				"<a href='".yii::$app->params['wp_site']."/'>The AGC</a></html>";
			} else { echo "Email Not found in Database."; }

			// Run same Command on Main server
			if( !strpos( strtolower(" ".$_SERVER['SERVER_NAME']), "badgeapp" )) {
				$command = "wget -qO- '".yii::$app->params['badge_site']."/site/no-email?unsubscribe=".$unsubscribe."'";
				exec('nohup ' . $command . ' > /dev/null 2>&1 &');
			}

		} else { echo " The Email you entered is invalid."; }
		echo "<br /> Good Bye.";
	}

	public function actionVerify($email, $send=false) {
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

			if($send) {
				yii::$app->controller->sendVerifyEmail($email);
			} else {
				$query = ['email'=> trim($email)];
				$badge = Badges::find()->where($query)->one();

				if ($badge) {
					if($badge->email_vrfy==0) {
						$sql = "UPDATE badges SET email_vrfy=1 where email='".$email."'";
						$command = Yii::$app->db->createCommand($sql);
						$result = $command->execute();
						if($result) {
							yii::$app->controller->createLog(true, 'Email Verify', 'saved: '.$email."','".$badge->badge_number);
						} else {
							yii::$app->controller->createLog(true, 'Email Verify', 'Error saving:'.$email."','".$badge->badge_number);
						}
					}
				} else {
					yii::$app->controller->createLog(true, 'Email Verify', 'no email found? ' .$email);
				}
				echo "<!DOCTYPE html>\n<html lang='en-US'>".PHP_EOL .
					"<head><title>AGC Email Validation</title></head>".PHP_EOL .
					"<body><center><br /><br /><h3>Thank you for validating your email.</h3>\n".
					"<br /><a href='".yii::$app->params['wp_site']."/'>Return to AGC<br/>\n".
					"<img src='/images/AGC_Logo.jpg' /></a>";

				// Run same Command on other server
				if( strpos( strtolower(" ".$_SERVER['SERVER_NAME']), "tmp" )) {
					$command = "wget -qO- '".yii::$app->params['badge_site']."/site/verify?email=".$email."'";
					exec('nohup ' . $command . ' > /dev/null 2>&1 &');
				}
			}

		} else { echo " The Email you entered is invalid."; }
	}

	public function log_access($m,$i,$n,$s) {
		$log = new LoginAccess;
		$log->l_date = yii::$app->controller->getNowTime();
		$log->module = $m;
		$log->ip = $i;
		$log->l_name = $n;
		$log->l_status = $s;
		$log->save();
	}
}