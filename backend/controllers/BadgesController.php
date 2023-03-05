<?php

namespace backend\controllers;

use Yii;
use backend\controllers\AdminController;
use backend\controllers\SalesController;
use backend\models\AgcCal;
use backend\models\BadgeCertification;
use backend\models\Badges;
use backend\models\BadgeSubscriptions;
use backend\models\CardReceipt;
use backend\models\clubs;
use backend\models\Guest;
use backend\models\MembershipType;
use backend\models\Params;
use backend\models\PostPrintTransactions;
use backend\models\StoreItems;
use backend\models\WorkCredits;
use backend\models\search\BadgesSearch;
use backend\models\search\BadgeCertificationSearch;
use backend\models\search\BadgeSubscriptionsSearch;
use backend\models\search\PostPrintTransactionsSearch;
use backend\models\search\WorkCreditsSearch;
use backend\models\search\ViolationsSearch;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;

use USPS\Address;
use USPS\AddressVerify;
use USPS\CityStateLookup;

class Phone extends \yii\db\ActiveRecord {
	// Phone Scrubber (actionApiPhone)
	public static function tableName() {
		return 'badges';
	}

	public function rules(){
		return [
			[['phone', 'phone_op', 'ice_phone'], 'string'],
			[['badge_number'], 'integer'],
		];
	}
}

class BadgesController extends AdminController {

	public $enableCsrfValidation = true;
	public $myFilters = ['badge_number','club_id','expire_condition','first_name','last_name','mem_type','suffix','status'];

	public function behaviors() {
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'delete' => ['POST'],
				],
			],
		];
	}

	public function actionAddCertification($membership_id) {
		$model = new BadgeCertification();
		if ($model->load(Yii::$app->request->post())) {

			$model->badge_number = $membership_id;
			$model->created_at = $this->getNowTime();
			$model->updated_at = $this->getNowTime();
			$sku = explode("|",$model->certification_type)[0];
			$item = (new StoreItems)->find()->where(['sku'=>$sku])->one();
			$model->certification_type = $item->sku;
			$model->fee = $item->price;
			$model->discount= 0.00;
			$model->amount_due = $model->fee - $model->discount;

			if($model->save()) {
				if($model->cert_payment_type <> 'creditnow') {
					$MyCart = ["item"=>$item->item,"sku"=> $item->sku,"ea"=>$item->price ,"qty"=>"1","price"=> $item->price ];

					$savercpt = new CardReceipt();
					$model->cc_x_id = 'x'.rand(100000000,1000000000);
					$savercpt->id = $model->cc_x_id;
					$savercpt->badge_number = $model->badge_number;
					$savercpt->tx_date = $this->getNowTime();
					$savercpt->tx_type = $model->cert_payment_type;
					$savercpt->amount = $model->cert_amount_due;
					$badgeArray = Badges::find()->where(['badge_number' => $model->badge_number])->one();
					$savercpt->name = $badgeArray->first_name.' '.$badgeArray->last_name;
					$savercpt->cart = "[".json_encode($MyCart)."]";
					$savercpt->cashier = $_SESSION['user'];
					if(is_null($_SESSION['badge_number'])) {$savercpt->cashier_badge = 0;} else {$savercpt->cashier_badge = $_SESSION['badge_number'];}
					if($savercpt->save()) {
						yii::$app->controller->createLog(true, $_SESSION['user'], "Saved Rcpt','".$model->badge_number);
					} else {
						yii::$app->controller->createLog(false, 'trex_C_BC savercpt', var_export($savercpt->errors,true));
					}
				}

				$this->createLog($this->getNowTime(), $_SESSION['user'], "Certificate generated','".$membership_id);
				Yii::$app->getSession()->setFlash('success', 'Certificate has been generated');
				return $this->redirect(['/badges/view-certificate', 'membership_id' => $membership_id, 'view_id'=>$model->id]);
			} else {
				$errors = $model->getErrors();
				if(array_key_exists('sticker', $errors)) {
					Yii::$app->getSession()->setFlash('error', 'Sticker '.$model->sticker.' has already been taken.');
					return $this->redirect(['/badges/update', 'badge_number' => $membership_id]);
				}
				//echo'<pre>'; print_r($errors); die();
			}
		}
	}

	public function actionApiGenerateRenavalFee() {
		if(isset($_REQUEST['_csrf-backend']) && (strlen($_REQUEST['_csrf-backend'])>60 )) {
			if(Yii::$app->request->post()) {
				//From Update Badge
				$badgeNumber = $_POST['badgeNumber'];
				$badgeFee = $_POST['BadgeFee'];
				$isCurent = $_POST['isCurent'];
				$renBadgeYear = $_POST['renBadgeYear'];
			} else {
				//From Issue New Badge
				$badgeNumber = $_GET['friend_badge'];
				$badgeFee = $_GET['BadgeFee'];
				$idCurrent = false;
				$renBadgeYear = $_GET['renBadgeYear'];
			}

			if(!isset($_SESSION['BasePriFee'])) {
				$BasePriFeeTmp =  MembershipType::find()->where(['id'=>50])->one();
				$BasePriFee = $BasePriFeeTmp->fullprice->price;
				$_SESSION['BasePriFee']=$BasePriFee;
			} else { $BasePriFee=$_SESSION['BasePriFee']; }

			if($BasePriFee < $badgeFee) {
				$BaseFee = $BasePriFee;
			} else {
				$BaseFee = $badgeFee;
			}

			$workcreditper = $BaseFee / 40;

			$DateChk = date('Y-01-31',strtotime("+1 years",strtotime($this->getNowTime())));
			$renBadgeYear =  date('Y-m-d',strtotime($renBadgeYear));
			if ($renBadgeYear == $DateChk) {
				$myBefDate = date('Y-01-01', strtotime($this->getNowTime()));
				$myAftDate = date('Y-12-31', strtotime("-2 years",strtotime($this->getNowTime())));
			} else {
				$myBefDate = date('Y-01-01', strtotime("+1 years",strtotime($this->getNowTime())));
				$myAftDate = date('Y-12-31', strtotime("-1 years",strtotime($this->getNowTime())));
			}

			$sql="SELECT badge_number,sum(work_hours) as creditSum FROM work_credits ".
				"WHERE badge_number=".$badgeNumber." and work_date<'".$myBefDate."' and  work_date>'".$myAftDate."' and status=1 ".
				"GROUP BY badge_number";

			$command = Yii::$app->getDb()->createCommand($sql);
			$AvalCreditCheck = $command->queryAll();

			if (isset($AvalCreditCheck[0]['creditSum'])) {$credit=$AvalCreditCheck[0]['creditSum'];} else {$credit=0;}
			if($credit<0) {$credit=0;}

			if($badgeFee > $BaseFee) {
				if(isset($isCurent) && ($isCurent==1)) {$discount = $BaseFee;} else {$discount=0;}
				$discount = ($workcreditper * $credit) + $discount;
				if($discount > $badgeFee) {
					return json_encode('recalculate',true);
				} else {
					$redeemableCredit = $credit;
				}
			} elseif($credit>40) {
				$discount = $workcreditper * 40;
				$redeemableCredit = 40;
			} else {
				$discount = $workcreditper * $credit;
				$redeemableCredit = $credit;
			}
			$discount = floor($discount*4)/4;

			$responce = [
				'badgeNumber'=>$badgeNumber,
				'BadgeFee' => (int)$badgeFee,
				'discount' => $discount,
				'amountDue' => $badgeFee - $discount,
				'redeemableCredit'=> $redeemableCredit,
			];
			return json_encode($responce,true);
		} else {
			return  $this->redirect(['/']);
		}
	}

	public function actionApiRequestFamily($badge_number) {
		if(isset($_REQUEST['_csrf-backend']) && (strlen($_REQUEST['_csrf-backend'])>60 )) {
			$badgeArray = Badges::find()->where(['badge_number' => $badge_number])->one();
			if(!empty($badgeArray)) {
				
				$params = Params::findOne('1');
				$isExpired = Badges::isExpired($badge_number,$params);
			
				if($badgeArray->phone) {$ice_phone=$badgeArray->phone;} else {$ice_phone=$badgeArray->phone_op;}
				$responce = [
					'status'=> 'success',
					'badge_number' => $badgeArray->badge_number,
					'prefix' => $badgeArray->prefix,
					'suffix' => $badgeArray->suffix,
					'first_name' => $badgeArray->first_name,
					'last_name' => $badgeArray->last_name,
					'address' => $badgeArray->address,
					'city' => $badgeArray->city,
					'state' => $badgeArray->state,
					'zip' => $badgeArray->zip,
					'ice_phone' => $ice_phone,
					'mem_type' => $badgeArray->mem_type,
					'isExpired'=> $isExpired
				];
			} else {
				$responce = [
					'status'=> 'error',
				];
			}
			return Json::encode($responce,true);
		} else {
			return  $this->redirect(['/']);
		}
		
	}

	private function fwrite_stream($fp, $string) {
		for ($written = 0; $written < strlen($string); $written += $fwrite) {
			$fwrite = fwrite($fp, substr($string, $written));
			if ($fwrite === false) {
				return $written;
			}
		}
		return $written;
	}

	public function actionApiCheck($unpub=false) {		// Test Functions to Fix database issues.  Use  ./badges/api-check
	yii::$app->controller->createLog(true, 'trex_B_C_BC-apiCheck', var_export($_REQUEST,true));
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

		if(isset($_GET['email']) && ($_GET['email']=='true')) {			// /badges/api-check?email=true
			$mail = yii::$app->controller->emailSetup();
			if ($mail) {
				$mail->setFrom(yii::$app->params['mail']['Username'], 'AGC Range');
				$mail->addCustomHeader('List-Unsubscribe', yii::$app->params['wp_site'].'/site/no-email?unsubscribe=noclick">');
				$mail->Subject = 'PHPMailer Test Subject via smtp, basic with authentication';
				$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
				$EmailBody = "This is the Email Body,  Isn't it sexy.  <br/ >From: ".$_SERVER['HTTP_HOST'];
				$mail->Body  = "<!DOCTYPE html><html><body>".$EmailBody."</body></html>";
				//$mail->addAddress('some@email.com', 'Marc');
				$mail->addAddress(yii::$app->params['adminEmail'], 'Prez');

				//$mail->Timeout=1000;
				$mail->SMTPDebug = 4; 			// debugging: 1 = errors and messages, 2 = messages only
				if($mail->Send()) {
					Yii::$app->response->data .= "Test Email Sent";
				} else {
					Yii::$app->response->data .= "Test Email failed<br />";
					Yii::$app->response->data .= 'Mailer Error: ' . $mail->ErrorInfo;
				}

			} else {
				Yii::$app->response->data .= "Email system Off";
			}
			exit;

		}
		elseif(isset($_GET['reverify']) && ($_GET['reverify']=='true')) {			//   /badges/api-check?reverify=true
			$model = Badges::find()->where("email_vrfy=0 AND email <>'' AND expires > '2016-01-01'")->orderBy(['badge_number' => SORT_ASC])->all();

			foreach ($model as $key => $member) {
			//	if ($member->badge_number < 525 ) { continue; }
				$thatGuy = Badges::find()->where(['email'=>$member->email])->one();
				yii::$app->controller->createEmailLog(true, 'next: ',$member->badge_number );

				if (filter_var($member->email, FILTER_VALIDATE_EMAIL)) {
					Yii::$app->response->data .= "$thatGuy->badge_number <br>";
					yii::$app->controller->sendVerifyEmail($member->email,'update',$thatGuy);
					yii::$app->controller->createEmailLog(true, 'Email Verify Sent:',$member->badge_number.' '.$member->email );
				} else {
					$thatGuy->email=null;
					$thatguy->save(false);
					yii::$app->controller->createEmailLog(true, 'Email Verify', "Removed:  ".$email."','".$thatguy->badge_number);
				}

				sleep(6);
			}
			Yii::$app->response->data .= 'fin';
			yii::$app->controller->createEmailLog(true, 'Email Verify','Fin');
		}
		elseif(isset($_GET['print']) && ($_GET['print']=='true')) {		//  /badges/api-check?print=true
			//var_dump(printer_list(PRINTER_ENUM_LOCAL | PRINTER_ENUM_SHARED));
			if(($conn = fsockopen('192.168.5.110',9100,$errno,$errstr))===false){
				echo 'Connection Failed' . $errno . $errstr;
			}

			$data = "<<<HERE".chr(13).
			"^XA".chr(13).
			"^FT50,200".chr(13).
			"^A0N,200,200^FDTEST^FS".chr(13).
			"^FT50,500".chr(13).
			"^A0N,200,200^FDZebra Printer^FS".chr(13).
			"^XZ".chr(13).
			"HERE";
			//$data
			fwrite ($conn, "I like learn PHP programming");

			#send request
			//$fput = ($conn, $data, strlen($data));

			#close the connection
			fclose($conn);
			echo "fin";

		}
		elseif(isset($_GET['phone']) && ($_GET['phone']=='true')) {		// ./badges/api-check?phone=true
			// Scrubs phone # formats.  requires Phone class and permissions in AdminController.

			$sql="SELECT badge_number,phone FROM badges where phone like '%(%' or phone_op like '%(%' or ice_phone like '%(%'";
			$connection = Yii::$app->getDb();
			$command = $connection->createCommand($sql);
			$BadgeNum = $command->queryAll();

			foreach ($BadgeNum as $key => $value) {
				$model = Phone::find()->where(['badge_number' =>$value['badge_number']])->one();
				if($model) {
					$model->phone = preg_replace('/\D/','',$model->phone);
					$model->phone_op = preg_replace('/\D/','',$model->phone_op);
					$model->ice_phone = preg_replace('/\D/','',$model->ice_phone);

					echo $value['badge_number']." - ". $model->phone. " - ". $model->phone_op." - ".$model->ice_phone;
					if($model->save()) {
						echo " Saved ".$value['badge_number']."<br>";
					} else {
						echo var_export($model->getErrors()).'<br>'.PHP_EOL.'<br>'.PHP_EOL;
						echo var_export($model).'<br>'.PHP_EOL;
						//print_r($model->getErrors());
						echo "didnt save";
						exit;
					}
				} else { echo "no Model"; }
			}
			echo "Phone #'s Cleaned";
		}
		elseif(isset($_GET['address']) ) {  //address
			$params = Params::findOne('1');
			if((isset($params->usps_api)) && (strlen($params->usps_api)>5)) {
				$verify = new AddressVerify($params->usps_api);
				$address = new Address;

				$address->setFirmName('some guy');
				$address->setApt('');
				$address->setAddress('123 some st');
				$address->setCity('Elkrdge');
				$address->setState('MD');
				$address->setZip5(21075);
				$address->setZip4('');

				// Add the address object to the address verify class
				$verify->addAddress($address);

				// Perform the request and return result
				echo "<br>Verify:<br>";
				var_dump($verify->verify());
				echo "<hr>Response:<br>";
				print_r($verify->getArrayResponse());
				echo "<hr>other:<br>";
				var_dump($verify->isError());

				// See if it was successful
				if($verify->isSuccess()) {
				  echo '<hr>Done';
				} else {
				  echo '<hr>Error: ' . $verify->getErrorMessage();
				}
				echo "fin";
				exit;
			}
		}
		elseif(isset($_GET['ckh_zip']) ) {  //  ./badges/api-check?zip=21075
			$params = Params::findOne('1');
			if((isset($params->usps_api)) && (strlen($params->usps_api)>5)) {
				$verify = new CityStateLookup($params->usps_api);

				$verify->addZipCode($_GET['ckh_zip']);

				// Perform the call and print out the results

				$verify->lookup();

				$response = $verify->getArrayResponse();

				// Check if it was completed
				if ($verify->isSuccess()) {
					$myzip = $response['CityStateLookupResponse'];
					echo json_encode($myzip['ZipCode']);
				} else {
					echo json_encode('Error: '.$verify->getErrorMessage());
				}
			} else {
				return json_encode(['city'=>'','state'=>'']);
			}

		}
		elseif(isset($_GET['unpub'])) {    //	./badges/api-check?unpub
			if($unpub) {$where='club_id='.$unpub.' AND';} else {$where='';}
			$sql = "SELECT distinct calendar_id FROM associat_agcnew.agc_calendar where $where calendar_id = recurrent_calendar_id and event_date>='".date('Y-01-01',strtotime("+1 year"))."'";
			$FixRecs = Yii::$app->getDb()->createCommand($sql)->queryAll();
			AgcCal::UpdateAll(['conflict'=>0,'approved'=>1,'active'=>1]); //,'deleted'=>0]);
			foreach ($FixRecs as $bad_recu) {
				$fixCal = AgcCal::find()->where(['calendar_id'=>$bad_recu['calendar_id']])->one();
				AgcCal::UpdateAll(['club_id'=>$fixCal->club_id,
						'facility_id'=>$fixCal->facility_id,
						'event_name'=>$fixCal->event_name,
						'keywords'=>$fixCal->keywords,
						'start_time'=>$fixCal->start_time,
						'end_time'=>$fixCal->end_time,
						'lanes_requested'=>$fixCal->lanes_requested,
						'recur_week_days'=>$fixCal->recur_week_days,
						'recurrent_start_date'=>$fixCal->recurrent_start_date,
						'recurrent_end_date'=>$fixCal->recurrent_end_date,
						'event_status_id'=>$fixCal->event_status_id,
						'range_status_id'=>$fixCal->range_status_id,
						'conflict'=>0,
						'deleted'=>$fixCal->deleted,
						'approved'=>$fixCal->approved,
						'active'=>$fixCal->active,
						'poc_badge'=>$fixCal->poc_badge,
					],'recurrent_calendar_id='.$bad_recu['calendar_id']);

				$check_cal = AgcCal::find()->where(['recurrent_calendar_id'=>$bad_recu['calendar_id'],'deleted'=>0])->orderBy(['calendar_id'=>SORT_ASC])->one(); // ->andWhere(['>=','event_date', date('Y-01-01',strtotime("+1 minute")) ])->all();
				if(strtotime($check_cal->event_date) <  strtotime(date('Y-12-31 23:25:00'))) {
					AgcCal::DeleteAll('recurrent_calendar_id='.$bad_recu['calendar_id']." AND event_date>='".date('Y-01-01',strtotime("+1 year"))."'");
					AgcCal::UpdateAll(['recurrent_calendar_id'=>$check_cal->calendar_id],'recurrent_calendar_id='.$bad_recu['calendar_id']);
					yii::$app->controller->createLog(true, 'trex_C_BC unPub', "bad: ".$bad_recu['calendar_id']." -> good: ".$check_cal->calendar_id);
					echo "bad: ".$bad_recu['calendar_id']." -> good: ".$check_cal->calendar_id."<br>";

				} else {
					yii::$app->controller->createLog(true, 'trex_C_BC unPub', "No events in previous year - ".$bad_recu['calendar_id']);
					echo "No events in previous year - ".$bad_recu['calendar_id']."<br/>";
				}
			}
			exit;
			//return $this->redirect(['/calendar/recur']);
		}
		elseif(isset($_GET['cashier'])) { //  /badges/api-check?cashier=1
			echo "<div class='col-xs-12'><br /><br /><br /><br />";
			$cashier = (new CardReceipt)->find()->select('cashier')->distinct()->where('cashier_badge is null')->all();
			foreach ($cashier as $ier){
				$badge = (new Badges)->find()->where("concat(first_name,' ',last_name) = \"".$ier->cashier."\"")->one();
				if($badge) {
					echo $ier->cashier.' - '.$badge->badge_number."<br>";
					CardReceipt::updateAll(['cashier_badge' => $badge->badge_number], "cashier = \"".$ier->cashier.'"');
				}
				else {echo $ier->cashier.'<br>';}
			}
			echo "</div>";
			return $this->render('_blank');
		}
		else {
			var_dump($_GET);
			exit;
			//return $this->redirect('index'); }
		}
	}

	public function actionApiZip($zip) {
		$params = Params::findOne('1');
		if((isset($params->usps_api)) && (strlen($params->usps_api)>5)) {
			$verify = new CityStateLookup($params->usps_api);
			$verify->addZipCode($zip);

			// Perform the call and print out the results

			$verify->lookup();
			$response = $verify->getArrayResponse();

			// Check if it was completed
			if ($verify->isSuccess()) {
				$myzip = $response['CityStateLookupResponse'];
				return json_encode($myzip['ZipCode']);
			} else {
				return json_encode('Error: '.$verify->getErrorMessage());
			}
		} else {
			return json_encode(['city'=>'','state'=>'']);
		}
	}

	public function actionCreate() {
		$confParams  = Params::findOne('1');
		$model = new Badges();

		if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			return ActiveForm::validate($model);

		}
		elseif ($model->load(Yii::$app->request->post())) {
			$model->created_at = $this->getNowTime();

			$model->status ='approved';
			$model = $model->cleanBadgeData($model,true,true);

			$bg_num =(New Badges)->find()->where(['badge_number'=>$model->badge_number])->count();
			if($bg_num > 0) {
				$model->badge_number=$model->getFirstFreeBadge();
				$qr=explode(" ",$model->qrcode);
				$model->qrcode=$qr[0]." ".$qr[1]." ".str_pad($model->badge_number, 5, '0', STR_PAD_LEFT)." ".$qr[3];
			}

			$saved=$model->save();
			if($saved) {
				if($model->payment_method <> 'creditnow') {
					$MyCart = ["item"=>$_POST['item_name'],"sku"=>$_POST['item_sku'],"ea"=>$model->badge_fee ,"qty"=>"1","price"=>$model->badge_fee ];
					$MyCart = "[".rtrim( json_encode($MyCart),",")."]";
					if(isset($_POST['cart'])) {
						SalesController::processCart($_POST['cart']);
						$MyCart = json_encode(array_merge(json_decode($MyCart),json_decode($_POST['cart'])));
					}

					// not storing multiple discounts yet.
					if(is_array($_POST['Badges']['discounts'])) {
						$discount=0;
						foreach ($_POST['Badges']['discounts'] as $d_item ) {
							$d_discount = explode(":",$d_item);
							if($d_discount[0]=='s'){
								$stu =  (new StoreItems)->find()->where(['sku'=>$confParams->sku_student])->one();
								if($stu){
									$discount += $stu->price;
									$MyCart = json_encode(array_merge(json_decode($MyCart),
										[ ["item"=>'Discount - Student',"sku"=>$confParams->sku_student,"ea"=>'-'.$stu->price ,"qty"=>"1","price"=>'-'.$stu->price ] ] ) );
								}
							}
						}
						$model->discounts=$discount;
					} else { $model->discounts=0; }

					$savercpt = new CardReceipt();
					$model->cc_x_id = 'x'.rand(100000000,1000000000);
					$savercpt->id = $model->cc_x_id;
					$savercpt->badge_number = $model->badge_number;
					$savercpt->tx_date = $this->getNowTime();
					$savercpt->tx_type = $model->payment_method;
					$savercpt->amount = $model->amt_due;
					$savercpt->tax = $model->tax;
					$savercpt->name = $model->first_name.' '.$model->last_name;
					$savercpt->cart = $MyCart;
					$savercpt->cashier = $_SESSION['user'];
					if(is_null($_SESSION['badge_number'])) {$savercpt->cashier_badge = 0;} else {$savercpt->cashier_badge = $_SESSION['badge_number'];}
					if($savercpt->save()) {
						yii::$app->controller->createLog(true, $_SESSION['user'], "Saved Rcpt','".$model->badge_number);
					} else {
						yii::$app->controller->createLog(false, 'trex_C_BC savercpt', var_export($savercpt->errors,true));
					}
				} else { $model->payment_method = 'credit';}

				//Email Verify
				yii::$app->controller->sendVerifyEmail($model->email,'new',$model);

				$stat = (New clubs)->saveClub($model->badge_number, $model->club_id);

				if($_POST['FriendCredits']>0) {
					$workCredit = new WorkCredits();
					$workCredit->badge_number = $_POST['FriendBadge'];
					$mydate = date('m',strtotime($this->getNowTime()));
					if ($mydate==10 || $mydate==11 || $mydate===12) {
						$myWorkDate = $this->getNowTime();
					} else {
						$myWorkDate = date('Y-12-31', strtotime("-1 years",strtotime($this->getNowTime())));
					}
					$workCredit->work_date = $myWorkDate;
					$workCredit->work_hours = '-'.$_POST['FriendCredits'];
					$workCredit->project_name = "Redeemed for New Badge";
					$workCredit->remarks = "Redeemed for Badge ".$model->first_name." ".$model->last_name;
					$workCredit->authorized_by = $_SESSION['user'];
					$workCredit->supervisor = $_SESSION['user'];
					$workCredit->status = '1';
					$workCredit->updated_at = $this->getNowTime();
					$workCredit->created_at = $this->getNowTime();
					$workCredit->created_by = $_SESSION['badge_number'];
					if($workCredit->save()) {
						yii::$app->controller->createLog(true, $_SESSION['user'], $_POST['FriendBadge'].' Gave '.$_POST['FriendCredits']." hours to','".$model->badge_number);
					} else {
						yii::$app->controller->createLog(true, 'Badge With WorkCredits Broke!', var_export($workCredit->getErrors()));
					}
				}

				$badgeSubscriptionsModel = new BadgeSubscriptions();
				$badgeSubscriptionsModel->badge_number = $model->badge_number;
				$badgeSubscriptionsModel->badge_year = $model->badge_year;
				$badgeSubscriptionsModel->payment_type = $model->payment_method;
				$badgeSubscriptionsModel->status = 'active';
				$badgeSubscriptionsModel->created_at = $this->getNowTime();
				$badgeSubscriptionsModel->badge_fee = $model->badge_fee;
				$badgeSubscriptionsModel->paid_amount = $model->amt_due;
				$badgeSubscriptionsModel->discount = $model->discounts;
				$badgeSubscriptionsModel->sticker = $model->sticker;
				$badgeSubscriptionsModel->transaction_type ='NEW';
				$badgeSubscriptionsModel->club_id = $model->club_id;
				$badgeSubscriptionsModel->cc_x_id = $model->cc_x_id;
				if($badgeSubscriptionsModel->save(false)) {
					if($model->save()) {
					} else {
					   // echo'<pre>'; print_r($model->getErrors()); die();
					}
				} else {
				  //echo'<pre>'; print_r($badgeSubscriptionsModel->getErrors()); die();
				}
				$stker = (new \backend\models\Stickers)::find()->where(['sticker'=>$model->sticker])->one();
				if($stker){
					yii::$app->controller->createLog(true, $_SESSION['user'],"Sticker','Issued ".$model->sticker.' to Badge '.$model->badge_number);
					$stker->status = 'isu';
					$stker->holder = $model->badge_number;
					$stker->updated =  $this->getNowTime();
					$stker->save();
				}

				$this->createLog($this->getNowTime(), $_SESSION['user'], "Issued new Badge','".$model->badge_number." for ".$model->first_name." ".$model->last_name);
				Yii::$app->getSession()->setFlash('success', 'Badge Holder Details has been created');
				return $this->redirect(['view', 'badge_number' => $model->badge_number]);
			} else {
				$model->remarks = '';

				$errors = $model->getErrors();
				if(array_key_exists('sticker', $errors)) {
					Yii::$app->getSession()->setFlash('error', 'Sticker '.$model->sticker.' has already been taken.');
					return $this->render('create',['model'=>$model,'confParams' => $confParams]);
				} elseif (array_key_exists('email', $errors)) {
					Yii::$app->getSession()->setFlash('error', $errors['email'][0]);
					return $this->render('create',['model'=>$model,'confParams' => $confParams]);
				} else {
					print var_export( $errors,true);
				}
			}
		}
		else {
			$model->badge_number = $model->getFirstFreeBadge();

			return $this->render('create', [
				'model' => $model,
				'confParams' => $confParams
			]);
		}
	}

	public function actionDelete($id,$back_to=null) {
		$badge_id = Badges::find()->where(['id' => $id])->one();

		$reciepts = CardReceipt::find()->where(['cashier_badge'=>$badge_id->badge_number])->all();
		if($reciepts) { // Fail
			Yii::$app->getSession()->setFlash('error', 'Can not Delete Member that was a Cashier!');
		} else {
		$sql="SELECT * from violations WHERE badge_involved like '%".$badge_id->badge_number."%'";
		$command = Yii::$app->getDb()->createCommand($sql);
		$ViolationsCheck = $command->queryAll();
		if(!$ViolationsCheck) { //NO Violations, Okay to Delete
			BadgeCertification::deleteAll(['badge_number' => $badge_id->badge_number]);
			BadgeSubscriptions::deleteAll(['badge_number' => $badge_id->badge_number]);
			PostPrintTransactions::deleteAll(['badge_number' => $badge_id->badge_number]);

			$sql="DELETE FROM badge_to_club WHERE badge_number=".$badge_id->badge_number;
			$command = Yii::$app->getDb()->createCommand($sql);
			$cmd = $command->execute();

			Guest::deleteAll(['badge_number' => $badge_id->badge_number]);
			WorkCredits::deleteAll(['badge_number' => $badge_id->badge_number]);

			if(BadgesController::findModel($id)->delete()) {
			//if($this->findModel($id)->delete()) {
			   yii::$app->controller->createLog(true, $_SESSION['user'], 'Deleted Badge: '.$badge_id->badge_number.' - '.$badge_id->first_name.' '.$badge_id->last_name);
			}
			Yii::$app->getSession()->setFlash('success', "Member Deleted.");
		} else {
			Yii::$app->getSession()->setFlash('error', 'Can not Delete Member with Violations!');
		}
		}
		if (!$back_to) {
			return $this->redirect('index');
		}
	}

	public function actionDeleteCertificate($membership_id,$view_id) {
		$certificationModel = BadgeCertification::findOne($view_id);
		if($certificationModel->badge_number==$membership_id) {
			$certificationModel->delete();
			Yii::$app->getSession()->setFlash('success', 'Certificate has been deleted');
			return $this->redirect(['badges/view-certifications-list','id'=>$membership_id]);
		}
	}

	public function actionDeleteRenewal($badge_number,$id) {
		$subscription = BadgeSubscriptions::find()->where(['id'=>$id,'badge_number'=>$badge_number])->one();
		if ($subscription) {
			if ($subscription->is_migrated) {
				Yii::$app->getSession()->setFlash('error', 'Must be deleted by hand!');
			} else {
				if ($subscription->cc_x_id) {
					$ccReciept = CardReceipt::find()->where(['id'=>$subscription->cc_x_id,'badge_number'=>$badge_number])->one();
					if($ccReciept){
						$ccReciept->delete();
					}
				}
				$subscription->delete();
				Yii::$app->getSession()->setFlash('success', 'Renual ID: '.$id.' deleted');
			}
		}
		return $this->redirect(['badges/view-renewal-history','badge_number'=>$badge_number]);
	}

	public function actionGenerateNewSticker() {
		$responce  = [
			'sticker'=>$this->getStikker(),
		];

		echo json_encode($responce,true);
	}

	public function actionGetBadgeDetails($badge_number,$rtn=false) {
		$badgeArray = Badges::find()->where(['badge_number'=>$badge_number])->one();
		if($badgeArray) {
			//Get Current Years Work Credits
			$myAftDate = date('Y-12-31', strtotime("-1 years",strtotime($this->getNowTime())));
			$myCurYr = date('Y');
			$sqla="SELECT ".$myCurYr." as wcCurYr, sum(work_hours) as wcCurHr FROM work_credits ".
				"WHERE badge_number=".$badge_number." and work_date>'".$myAftDate."' and status=1 ".
				"GROUP BY badge_number";
			$command = Yii::$app->getDb()->createCommand($sqla);
			$CurCredits = $command->queryAll();

			//Get Last Years Work Credits
			$myAftDate = date('Y-12-31', strtotime("-2 years",strtotime($this->getNowTime())));
			$myBefDate = date('Y-01-01', strtotime($this->getNowTime()));
			$myLasYr = (int)date('Y',strtotime("-1 years",strtotime($this->getNowTime())));
			$sqlb="SELECT ".$myLasYr." as wcLasYr, sum(work_hours) as wcLasHr FROM work_credits ".
				"WHERE badge_number=".$badge_number." and work_date<'".$myBefDate."' and  work_date>'".$myAftDate."' and status=1 ".
				"GROUP BY badge_number";
			$command = Yii::$app->getDb()->createCommand($sqlb);
			$LastCredits = $command->queryAll();
			
			if(($CurCredits) && ($LastCredits)) {
				$mergtwo=array_merge($CurCredits[0],$LastCredits[0]);
			} else if ($CurCredits) {
				$mergtwo=array_merge($CurCredits[0], ['wcLasYr'=>$myLasYr,'wcLasHr'=>0]);
			} else if ($LastCredits){
				$mergtwo=array_merge(['wcCurYr'=>$myCurYr,'wcCurHr'=>0], $LastCredits[0]);
			} else {
				$mergtwo=['wcCurYr'=>$myCurYr,'wcCurHr'=>0,'wcLasYr'=>$myLasYr,'wcLasHr'=>0];
			}

			$params = Params::findOne('1');
			$isExpired = Badges::isExpired($badge_number,$params);
			$mergtwo=array_merge($mergtwo,['isExpired'=> $isExpired]);

			$mergtwo=array_merge($mergtwo,['clubs'=> (New clubs)->getMyClubs($badge_number)]);

			$responceA = Json::encode($badgeArray,JSON_PRETTY_PRINT);
			$responceB = Json::encode($mergtwo,JSON_PRETTY_PRINT);
			$responce=trim(rtrim($responceA,"}")).",".ltrim($responceB,"{");
		} else {
			$responce=Json::encode(['success'=>false]);
		}

		if($rtn){
			return Json::decode($responce);
		} else {
			if(isset($_REQUEST['_csrf-backend']) && (strlen($_REQUEST['_csrf-backend'])>60 )) {
				Yii::$app->response->data = $responce;
			} else {
				return  $this->redirect(['/']);
			}
		}
	}

	public function actionGetBadgeName($badge_number) {
		$badgeArray = Badges::find()->where(['badge_number'=>$badge_number])->one();
		if($badgeArray) {
			$responce = Json::encode([
				'success'=>true,
				'first_name'=>$badgeArray->first_name,
				'last_name'=>$badgeArray->last_name,
				'expires'=>$badgeArray->expires]);
		} else {$responce=Json::encode(['success'=>false]);}
		return $responce;
	}

	public function actionGetFamilyBadges() {
		if (isset($_POST['depdrop_parents'])) {
			$parents = $_POST['depdrop_parents'];
			if ($parents != null) {
				$clubId = $parents[0];
				$familyBadges = Badges::find()->where(['club_id'=>$clubId])->all();
				$familyBadgesList = ArrayHelper::map($familyBadges, 'badge_number','first_name');

				$array = [];

				foreach ($familyBadges as  $value) {
					$array [] = [
						'id' => $value->badge_number,
						'name' => $value->badge_number.' - '.$value->first_name,
					];
				}
				return Json::encode(['output'=>$array, 'selected'=>'']);
			}
		}
		return Json::encode(['output'=>'', 'selected'=>'']);
	}

	public function actionHelp($badge_number) {
		$model = Badges::find()->where(['badge_number'=>$badge_number])->one();
		return $this->render('help',['model' => $model]);
	}

	public function actionIndex() {
		$searchModel = new BadgesSearch();

	//yii::$app->controller->createLog(false, 'trex-B_C_BC Request', var_export($_REQUEST,true));
		$this->RestoreSession($searchModel,'Badges',$this->myFilters);
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionPostPrintTransactions() {

	  { //Scrubs All Transactions and adds to Transaction list.
		 set_time_limit(3800);
		$findSubscriptionRecords = BadgeSubscriptions::find()
			->where(['is_migrated'=>'0'])
			->all();

		foreach ($findSubscriptionRecords as $key => $subscription) {
			$postPrintTransactions = new PostPrintTransactions();
				$postPrintTransactions->badge_number		=   $subscription->badge_number;
				$postPrintTransactions->transaction_type	=   $subscription->transaction_type;
				$postPrintTransactions->created_at		  =   $subscription->created_at;
				$postPrintTransactions->fee				 =   $subscription->badge_fee;
				$postPrintTransactions->discount			=   $subscription->discount;
				$postPrintTransactions->paid_amount		 =   $subscription->paid_amount;
				if($postPrintTransactions->save()) {
					$subscription->is_migrated = '1';
					if($subscription->save(false)) {
					}
				} else {
					yii::$app->controller->createLog(true, 'T-Rex-BC_PPT:sub', var_export($postPrintTransactions->getErrors(),true));
					Yii::$app->getSession()->setFlash('error', 'Badge Subscriptions '.' Please see Activity Log for T-Rex Error!.');
				};
		}

		$findCertificationRecords = BadgeCertification::find()
			->where(['is_migrated'=>'0'])
			->all();

		foreach ($findCertificationRecords as $certification) {
			$postPrintTransactions = new PostPrintTransactions();
			$postPrintTransactions->badge_number		=   $certification->badge_number;
			$postPrintTransactions->transaction_type	=   'CERT';
			$postPrintTransactions->created_at		  =   $certification->created_at;
			$postPrintTransactions->fee				 =   $certification->fee;
			$postPrintTransactions->discount			=   $certification->discount;
			$postPrintTransactions->paid_amount		 =   $certification->amount_due;
			if($postPrintTransactions->save()) {
				$certification->is_migrated = '1';
				$certification->save();
			} else {
				yii::$app->controller->createLog(true, 'T-Rex-BC_PPT:cert', var_export($postPrintTransactions->getErrors(),true));
				Yii::$app->getSession()->setFlash('error', 'Badge Certifications '.' Please see Activity Log for T-Rex Error!.');
			};
		}
	  }

		$searchModel = new PostPrintTransactionsSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		return $this->render('post-print-transactions',[
				'searchModel'=> $searchModel,
				'dataProvider' => $dataProvider,
			]);
	}

	public function actionPhotoAdd() {
		if (Yii::$app->request->post()) {
			//Post & get are used because of the way data is sent back.
			if(isset($_GET['badge']) && $_GET['badge'] >0 ) {
				$myPath = "./files/badge_photos/";
				if (!is_dir($myPath)) {
					mkdir($myPath, 0775, true);
				}
				if(isset($_POST['imgBase64'])) {
					$img = $_POST['imgBase64'];
					if(strpos($img,"image/jpeg")) {
						$img=explode(',',$img)[1];
						$ext=".jpg";
					} elseif (strpos($img,"image/png")) {
						$img=explode(',',$img)[1];
						$ext=".png";
					} else {
						yii::$app->controller->createLog(true, 'trex_C_BC add photo OTHER ',substr($img, 0, 80));
						$ext=".txt";
					}
					$data = base64_decode($img);
					$myfile = "files/badge_photos/".str_pad($_GET['badge'], 5, '0', STR_PAD_LEFT).$ext;
					if(is_file($myfile)) {
						chmod($myfile, 0777);
						unlink($myfile);
					}

					$file = file_put_contents( $myfile, $data );
					if($file){
						if(strpos($_SERVER['HTTP_REFERER'],'crop')) {
							yii::$app->controller->createLog(true, $_SESSION['user'],"Added Photo','".$_GET['badge']);
						}
					} else {
						yii::$app->controller->createLog(true, 'Error '.$_SESSION['user'],"Add Photo failed','".$_GET['badge']);
					}
					exit;
				}

			}
		} else {
			return $this->render('photo-add');
		}
	}

	public function actionPhotoCrop() {
		return $this->render('photo-crop');
	}

	public function actionPrint($badge_number,$ty='') {
		set_time_limit(380);
		$badgeModel = Badges::find()->where(['badge_number'=>$badge_number])->one();

		$this->UpdateQR($badgeModel);
		$PgOne = $this->renderPartial('_badge-print-view',['model'=>$badgeModel,'page'=>1,'chk'=>$ty]);
		$PgThr = $this->renderPartial('_badge-print-view',['model'=>$badgeModel,'page'=>3]);
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'c',
			'margin_left' => 0,
			'margin_right' => 0,
			'margin_top' => 0,
			'margin_bottom' => 0,
			'format' => [54,86],
			]);
		$mpdf->SetTitle('AGC: '.$badgeModel->badge_number);
		$mpdf->WriteHTML($PgOne);

		$mpdf->AddPage('L');
		$mpdf->WriteHTML($PgThr);

		$mpdf->Output($badge_number.'_'.$ty,'I');
	}

	public function actionPrintRcpt ($x_id,$badge_number,$email=false,$first=null) {  //Reciept Email or Print
		$MyRcpt = CardReceipt::findOne($x_id,$badge_number);
		if($MyRcpt) {
			if($MyRcpt->on_qb==0) {
				$badge = Badges::find()->where(['badge_number'=>$badge_number])->one();
				if (filter_var($badge->email, FILTER_VALIDATE_EMAIL)) {
					$MyRcpt->on_qb=1;
					$MyRcpt->save();
					$this::actionPrintRcpt($x_id,$badge_number,$badge->email,$badge->first_name );
				}
			}
			if($email) {
				if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$mail = yii::$app->controller->emailSetup();
					if ($mail) {
						$mail->addCustomHeader('List-Unsubscribe', yii::$app->params['wp_site'].'/site/no-email?unsubscribe='.$email.'">');
						$mail->setFrom(yii::$app->params['mail']['Username'], 'AGC Range');
						$mail->addAddress($email, $first);
						$mail->Subject = 'AGC Range Reciept';
						$EmailBody = $this->renderPartial('print-rcpt',[ 'MyRcpt' => $MyRcpt ] );
						$mail->Body = "<!DOCTYPE html><html><body>".$EmailBody."</body></html>";
						$mail->send();
						Yii::$app->getSession()->setFlash('success', 'Receipt Emailed');
						yii::$app->controller->createLog(true, 'Email', "Recept sent to $first','".$badge_number);
						$responce = [ 'status'=> 'success','msg'=>'Receipt sent via Email.' ];
					} else {
						$responce = [ 'status'=> 'error','msg'=>'Reciept not Emailed. Email system is disabled.' ];
					}
				} else {
					$responce = [ 'status'=> 'error','msg'=>'No Valid Email, Print Reciept Upon Request.' ];
				}
				return Json::encode($responce,true);
			} else { return $this->render('print-rcpt',[ 'MyRcpt' => $MyRcpt ] ); }
		} else {
			Yii::$app->getSession()->setFlash('error', 'Reciept Not Found.');
			return $this->redirect($_SERVER['HTTP_REFERER']);
		}
	}

	public function actionRenewMembership($membership_id) {
		$confParams  = Params::findOne('1');
		$model = new BadgeSubscriptions();
		$badgeRecords = Badges::find()->where(['badge_number'=>$membership_id])->one();
		$badgeRecords->load(Yii::$app->request->post());
		$dirty = (New Badges)->loadDirtyFilds($badgeRecords);
		$dirty=implode(", ",$dirty);
		$badgeRecords = $badgeRecords->cleanBadgeData($badgeRecords);
		yii::$app->controller->sendVerifyEmail($badgeRecords->email,'update',$badgeRecords);

		if ($model->load(Yii::$app->request->post())) {
			$this->expireBadgeSubcription($model->badge_number);

			if ($model->payment_type=='creditnow') {
				$model->payment_type = 'credit'; $needRecpt=false;
			} else { $needRecpt=true; }
			$wt_date_reIssue = null;
			if($model->wt_date!='') {
				$wt_date_reIssue = date('Y-m-d',strtotime($model->wt_date));
				$wt_instru_reIssue = $model->wt_instru;
			}
			$model->badge_year = $_REQUEST['renBadgeYear'];
			$model->status = 'active';
			$model->created_at = $this->getNowTime();
			$model->paid_amount = $model->amount_due;

// not storing multiple discounts yet.
			if((is_array($_POST['BadgeSubscriptions']['discount'])) && ($_POST['BadgeSubscriptions']['badge_fee']>0 )) {
				foreach ($_POST['BadgeSubscriptions']['discount'] as $d_item ) {
					$d_discount = explode(":",$d_item);
					$model->discount += number_format($d_discount[1],2);
					if($d_discount[0]=='w'){
						$d_cart = json_encode( [ ["item"=>'Discount - Work Credits',"sku"=>$confParams->sku_wc_discount,"ea"=>'-'.$d_discount[1] ,"qty"=>"1","price"=>'-'.$d_discount[1] ] ] );
					}
				}
			} else { $model->discount=0; }

			$model->transaction_type = 'RENEW';
			$model->club_id = $badgeRecords->club_id;
			if($model->cc_x_id =='') {$model->cc_x_id = 'x'.rand(100000000,1000000000); }

			if($model->save()) {
				if($needRecpt) {
					$MyCart = ["item"=>$_POST['item_name'],"sku"=>$_POST['item_sku'],"ea"=>$model->badge_fee ,"qty"=>"1","price"=>$model->badge_fee ];
					$MyCart = "[".rtrim( json_encode($MyCart),",")."]";
					if(isset($d_cart)) {
						$MyCart = json_encode(array_merge(json_decode($MyCart),json_decode($d_cart)));
					}
					if(isset($_POST['cart'])) {
						SalesController::processCart($_POST['cart']);
						$MyCart = json_encode(array_merge(json_decode($MyCart),json_decode($_POST['cart'])));
					}
					$savercpt = new CardReceipt();
					$savercpt->id = $model->cc_x_id;
					$savercpt->badge_number = $model->badge_number;
					$savercpt->tx_date = $this->getNowTime();
					$savercpt->tx_type = $model->payment_type;
					$savercpt->amount = $model->amount_due;
					$savercpt->tax = $model->tax;
					$savercpt->name = $badgeRecords->first_name.' '.$badgeRecords->last_name;
					$savercpt->cart = $MyCart;
					$savercpt->cashier = $_SESSION['user'];
					if(is_null($_SESSION['badge_number'])) {$savercpt->cashier_badge = 0;} else {$savercpt->cashier_badge = $_SESSION['badge_number'];}
					if($savercpt->save()) {
						yii::$app->controller->createLog(true, $_SESSION['user'], "Saved Rcpt','".$model->badge_number);
					} else {
						yii::$app->controller->createLog(false, 'trex_C_BC savercpt', var_export($savercpt->errors,true));
					}
				}

				$badgeRecords->work_credits = (int)$badgeRecords->work_credits - (int)$model->redeemable_credit;
				$badgeRecords->sticker = $model->sticker;
				$badgeRecords->wt_date = $wt_date_reIssue !=null ? $wt_date_reIssue : $badgeRecords->wt_date;
				$badgeRecords->wt_instru = $wt_date_reIssue !=null ? $wt_instru_reIssue : $badgeRecords->wt_instru;
				$badgeRecords->status = 'approved';
				$badgeRecords->updated_at = $this->getNowTime();
				if($model->redeemable_credit>0) {
					$workCredit = new WorkCredits();
					$workCredit->badge_number = $badgeRecords->badge_number;
					$mydate = date('m',strtotime($this->getNowTime()));
					if ($mydate==10 || $mydate==11 || $mydate===12) {
						$myWorkDate = $this->getNowTime();
					} else {
						$myWorkDate = date('Y-12-31', strtotime("-1 years",strtotime($this->getNowTime())));
					}
					$workCredit->work_date = $myWorkDate;
					$workCredit->work_hours = '-'.$model->redeemable_credit;
					$workCredit->project_name = "Redeemed for renewal";
					$workCredit->remarks = "Redeemed for renewal of Badge ".$badgeRecords->badge_number;
					$workCredit->authorized_by = $_SESSION['user'];
					$workCredit->supervisor = $_SESSION['user'];
					$workCredit->status = '1';
					$workCredit->updated_at = $this->getNowTime();
					$workCredit->created_at = $this->getNowTime();
					$workCredit->created_by = $_SESSION['badge_number'];
					if($workCredit->save()) {
						yii::$app->controller->createLog(true, $_SESSION['user'],"Renewed using ".$model->redeemable_credit. " hours','".$model->badge_number);
					} else {
						yii::$app->controller->createLog(true, 'Badge With WorkCredits Broke!', var_export($workCredit->getErrors()));
					}

					$badgeRecords->work_credits = 0;
				}

				$stker = (new \backend\models\Stickers)::find()->where(['sticker'=>$model->sticker])->one();
				if($stker){
					yii::$app->controller->createLog(true, $_SESSION['user'],"Sticker','Issued ".$model->sticker.' to Badge '.$model->badge_number);
					$stker->status = 'isu';
					$stker->holder = $model->badge_number;
					$stker->updated =  $this->getNowTime();
					$stker->save();
				}

				if($dirty) {$cmnt = "Updated: ".$dirty; } else { $cmnt = ''; }
				$nowRemakrs = [
					'created_at' => $this->getNowTime(),
					'data' => $cmnt,
					'changed' => 'Renewed by '.$_SESSION['user'],
				];
				$remarksOld = json_decode($badgeRecords->remarks,true);
				if($remarksOld != '') {
					array_push($remarksOld,$nowRemakrs);
				} else {
					$remarksOld = [
						$nowRemakrs,
					];
				}
				$badgeRecords->remarks = json_encode($remarksOld,true);

				if($badgeRecords->save(false)) {
					$this->createLog($this->getNowTime(), $_SESSION['user'], "Membership Renewed','".$badgeRecords->badge_number." for ".$badgeRecords->first_name." ".$badgeRecords->last_name);
					Yii::$app->getSession()->setFlash('success', 'Membership has been Renewed');
					return $this->redirect(['badges/view-subscriptions', 'badge_number' => $model->badge_number]);
				}
			} else {

				$errors = $model->getErrors();
				Yii::$app->response->data = '<pre>'; print_r($errors); die();
				if(array_key_exists('sticker', $errors)) {
					Yii::$app->getSession()->setFlash('error', 'Sticker '.$model->sticker.' has already been taken.');
					return $this->redirect(['/badges/update', 'id' => $membership_id]);
				}
			}
		} else {
			return $this->redirect('view?badge_number='.$badgeRecords->badge_number);
		}
	}

	public function actionScanBadge() {
				return $this->render('scan-badge');
	}

	public function actionUpdate($badge_number) {
		if(!yii::$app->controller->hasPermission('badges/all') && $badge_number!=$_SESSION['badge_number']) {
			return $this->redirect(['update', 'badge_number' => $_SESSION['badge_number']]);
		}
		$model = Badges::find()->where(['badge_number'=>$badge_number])->one();

		if ($model) {
			$badgeSubscriptions = new BadgeSubscriptions();
			$badgeCertification = new BadgeCertification();

			if(yii::$app->request->isAjax) {
				// if ajax request
				if ($model->load(Yii::$app->request->post())) {
					$model = $model->cleanBadgeData($model,true);

					if($model->save(false)) {
						$this->createLog($this->getNowTime(), $_SESSION['user'], "Updated Badge','".$model->badge_number);
						$responce = [
							'status' => 'true',
							'data'	 => $model->attributes,
						];
						return json_encode($responce,true);
					}
				} else {
					$responce = [
						'status' => 'false',
						'data'	 => null,
					];
					return json_encode($responce,true);
				}
			}
			else {  // if not ajax request
				if ($model->load(Yii::$app->request->post())) {
					$model = $model->cleanBadgeData($model,true);
					if($model->save(false)) {

						//send Email
						yii::$app->controller->sendVerifyEmail($model->email,'update',$model);

						$this->createLog($this->getNowTime(), $_SESSION['user'], "Updated Badge','".$model->badge_number);
						Yii::$app->getSession()->setFlash('success', 'Badge Holder Details has been updated');
						return $this->redirect(['view', 'badge_number' => $model->badge_number]);
					}
				} elseif (isset($_GET['new'])) {
					$this->UpdateQR($model);
					return $this->redirect(['update', 'badge_number' => $_GET['badge_number']]);

				} else {
					$confParams  = Params::findOne('1');
					return $this->render('update', [
						'model' => $model,
						'badgeSubscriptions'=>$badgeSubscriptions,
						'badgeCertification'=>$badgeCertification,
						'confParams'=>$confParams
					]);
				}
			}
		} else {
			Yii::$app->getSession()->setFlash('error', 'Badge Number not found.');
			return $this->redirect(['index']);
		}
	}

	public function actionUpdateCertificate($membership_id,$view_id) {

	   $badgesModel = Badges::find()->where(['badge_number'=>$membership_id])->one();
	   $certificationModel = BadgeCertification::findOne($view_id);
	   if($certificationModel->load(Yii::$app->request->post())) {
			$certificationModel->updated_at = $this->getNowTime();
			$certificationModel->save(false);
			Yii::$app->getSession()->setFlash('success', 'Certificate has been edited');
	   }

		return $this->render('update-certificate',[
			'badgeModel'=> $badgesModel,
			'certificationModel'=> $certificationModel,
		]);
	}

	public function actionUpdateRenewal($id) {
		$model = BadgeSubscriptions::find()->where(['id'=>$id])->one();
		if ($model->load(Yii::$app->request->post())) {
			if($model->save()) {
				Yii::$app->getSession()->setFlash('success', 'Badge Holder Details has been updated');
				return $this->redirect(['view-renewal-history', 'badge_number' => $model->badge_number]);
			} else {
				Yii::$app->getSession()->setFlash('error', 'Failed to save record');
			}
		}

		return $this->render('update-renewal',[
			'model'=> $model,
		]);
	}

	public function actionVerifyEmail($badge_number,$type) {
		$model = Badges::find()->where(['badge_number'=>$badge_number])->one();
		if(!$model) { return true; }

		if (filter_var($model->email, FILTER_VALIDATE_EMAIL)) {
			if($type=='new') { $welcome = 'Welcome to the AGC '.$model->first_name.'!,'; }
			else if ($type=='update') { $welcome = 'Hi '.$model->first_name.','; }

			$mail = $this->emailSetup();
			if ($mail) {
				$mail->addCustomHeader('List-Unsubscribe', '<'.yii::$app->params['wp_site'].'/comms.php?unsubscribe='.$model->email.'>');
				$mail->addAddress($model->email, $model->first_name.' '.$model->last_name);

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
						'<p><a href="'.yii::$app->params['wp_site'].'/comms.php?verifyemail='.$model->email.'"> Verify your Email: '.$model->email.' </a></p><br>' .
						'<p>Thank You,<br />Associated Gun Clubs of Baltimore.</p>' ."\n".
						'<a href="'.yii::$app->params['wp_site'].'">'.yii::$app->params['wp_site'].'</a>' ."\n".
						"<br /><br><p>P.S. We know our email probably went to the spam folder. Please tell your provider It's not Spam!. </p>\n".
						'<br /><p> or Click here to <a href="'.yii::$app->params['wp_site'].'/comms.php?unsubscribe='.$model->email.'">remove your email from our List</a>.</p>'. "\n".
						"</body>\n</html>";

						//Content
					$mail->Subject = 'AGC Email Verification';
					$mail->Body	= $message;
					$mail->AltBody = $welcome."\n\n".
						"Please take a moment to verify your Email by clicking on the link below.\n\n".
						yii::$app->params['wp_site'].'/comms.php?verifyemail='.$model->email."\n\n".
						"Thank You,\nAssociated Gun Clubs of Baltimore.";

					$mail->send();
					yii::$app->controller->createLog(true, 'Email Verify', "Sent to ".$model->email."','".$model->badge_number);
					return true;
				} catch (Exception $e) {
					$mail->SMTPDebug = 3;
					Yii::$app->response->data .= 'Message could not be sent.';
					Yii::$app->response->data .= 'Mailer Error: ' . $mail->ErrorInfo;
					yii::$app->controller->createLog(true, 'trex Verify Email Error: ', var_export($mail->ErrorInfo,true));
				}
			}
		}
		return false;
	}

	public function actionView($badge_number) {
		if(!yii::$app->controller->hasPermission('badges/all') && $badge_number!=$_SESSION['badge_number']) {
			return $this->redirect(['view', 'badge_number' => $_SESSION['badge_number']]);
		}

		$model = Badges::find()->where(['badge_number'=>$badge_number])->one();
		if ($model) {
			return $this->render('view', [
				'model' => $model,
			]);
		} else {
			Yii::$app->getSession()->setFlash('error', 'Badge Number not found.');
			return $this->redirect(['index']);
		}
	}

	public function actionViewCertificate($membership_id,$view_id) {

	   $badgesModel = Badges::find()->where(['badge_number'=>$membership_id])->one();
	   $certificationModel = BadgeCertification::findOne($view_id);

		return $this->render('view-certificate',[
			'badgeModel'=> $badgesModel,
			'certificationModel'=> $certificationModel,
		]);
	}

	public function actionViewCertificationsList($badge_number) {
		if(!yii::$app->controller->hasPermission('badges/all') && $badge_number!=$_SESSION['badge_number']) {
			return $this->redirect(['view-certifications-list', 'badge_number' => $_SESSION['badge_number']]);
		}

		$model = Badges::find()->where(['badge_number'=>$badge_number])->one();
		if($model) {
			$searchModel = new BadgeCertificationSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			$dataProvider->query->andWhere(['badge_number'=>$badge_number]);
			$dataProvider->query->orderBy(['id' => SORT_DESC]);

			return $this->render('view-certifications-list',[
				'model'=> $model,
				'dataProvider' => $dataProvider,
				'searchModel' => $searchModel,
			]);
		} else {return $this->redirect(['index']);}
	}

	public function actionViewRemarksHistory($badge_number) {
		if(!yii::$app->controller->hasPermission('badges/all') && $badge_number!=$_SESSION['badge_number']) {
			return $this->redirect(['view-remarks-history', 'badge_number' => $_SESSION['badge_number']]);
		}

		$model = Badges::find()->where(['badge_number'=>$badge_number])->one();
		if($model) {
			return $this->render('view-remarks-history',[
				'model'=>$model]);
		} else {return $this->redirect(['index']);}
	}

	public function actionViewRenewalHistory($badge_number) {
		if(!yii::$app->controller->hasPermission('badges/all') && $badge_number!=$_SESSION['badge_number']) {
			return $this->redirect(['view-renewal-history', 'badge_number' => $_SESSION['badge_number']]);
		}

		$model = Badges::find()->where(['badge_number'=>$badge_number])->one();
		if($model) {
			$searchModel = new BadgeSubscriptionsSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			$dataProvider->query->andWhere(['badge_number'=>$badge_number]);
			$dataProvider->query->orderBy(['id' => SORT_DESC]);

			return $this->render('view-renewal-history',[
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
				'model'=>$model,
			]);
		} else {return $this->redirect(['index']);}
	}

	public function actionViewSubscriptions($badge_number=0,$id=0) {
		if($id>0) {
			$subciptionsArray = BadgeSubscriptions::findOne($id);
		} else {
			$subciptionsArray = BadgeSubscriptions::find()->where(['badge_number'=>$badge_number])->orderBy(['id'=>SORT_DESC])->one();
		}
		return $this->render('view-subscriptions',[
			'subciptionsArray'=>$subciptionsArray,
		]);
	}

	public function actionViewViolationsHistory($badge_number) {
		if(!yii::$app->controller->hasPermission('badges/all') && $badge_number!=$_SESSION['badge_number']) {
			return $this->redirect(['view-violations-history', 'badge_number' => $_SESSION['badge_number']]);
		}
		$model = Badges::find()->where(['badge_number'=>$badge_number])->one();
		if($model) {
			$searchModel = new ViolationsSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			$dataProvider->query->andWhere(['badge_involved'=>$badge_number]);
			return $this->render('view-violations-history', [
				'model' => $model,
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
			]);
		} else {return $this->redirect(['index']);}
	}

	public function actionViewWorkCredits($badge_number) {
		if(!yii::$app->controller->hasPermission('badges/all') && $badge_number!=$_SESSION['badge_number']) {
			return $this->redirect(['view-work-credits', 'badge_number' => $_SESSION['badge_number']]);
		}

		$model = Badges::find()->where(['badge_number'=>$badge_number])->one();
		if($model) {
			//$workCredit = $model->getWorkCredits($model->badge_number);
			$searchModel = new WorkCreditsSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			$dataProvider->query->andWhere(['badge_number' => $badge_number]);
			$dataProvider->pagination->pageSize = 20;

			return $this->render('view-work-credits',[
					'model'=> $model,
					'searchModel'=>$searchModel,
					'dataProvider'=>$dataProvider,
			]);
		} else {return $this->redirect(['index']);}
	}

	public function actionViewWorkCreditsLog($badge_number) {
		if(!yii::$app->controller->hasPermission('badges/all') && $badge_number!=$_SESSION['badge_number']) {
			return $this->redirect(['view-work-credits-log', 'badge_number' => $_SESSION['badge_number']]);
		}

		$model = Badges::find()->where(['badge_number'=>$badge_number])->one();
		if($model) {
			$searchModel = new WorkCreditsSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			$dataProvider->query->andWhere(['badge_number'=>$badge_number]);
			$dataProvider->query->orderBy(['id' => SORT_DESC]);

			return $this->render('view-work-credits-log',[
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
				'model'=>$model,
			]);
		} else {return $this->redirect(['index']);}
	}

	public function getStikker() {
		$badgeCertification = new BadgeCertification();
		$presmission = true;
		while($presmission==true) {
			$sticker = $badgeCertification->generateSticker();
			$validate = $badgeCertification->validateSticker($sticker);
			if($validate==true) {
				return $sticker;
			}
		}
	}

	public function UpdateQR($model) {
		if(strlen($model->qrcode)<>14) {
			$clubs = (new clubs)->getMyClubs($model->badge_number);
			$characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
			$randomString = '';
			for ($i = 0; $i < 2; $i++) {
				$randomString .= $characters[rand(0, 32 - 1)];
			}

			$model->qrcode = str_pad($clubs[0], 2, '0', STR_PAD_LEFT)." ".$model->mem_type." ".str_pad($_GET['badge_number'], 5, '0', STR_PAD_LEFT)." ".$randomString;
			$model->status = "approved";
			if ($model->save(false)) {Yii::$app->getSession()->setFlash('success', 'Badge QR code Update!  Account set to Approved!');}
		}
	}

	protected function expireBadgeSubcription($badge_number) {
		$sql="UPDATE badge_subscriptions SET status='expired' WHERE badge_number=".$badge_number." AND status='active'";
		$connection = Yii::$app->getDb();
		$command = $connection->createCommand($sql);
		$exec = $command->execute();

		return $exec;
	}

	protected function findMemTypeId($string) {

		$membershipType = MembershipType::find()
			->where(['type'=>$string])
			->one();
		return $membershipType->id !=null ? $membershipType->id : '0';
	}

	protected function findModel($id) {
		if (($model = Badges::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	protected function getOfferFee($feeArray) {
		$now = $this->getNowTime();
		$nowMonthOnly = date('m',strtotime($now));
		if($nowMonthOnly>=7 && $nowMonthOnly<11) {
			$persontage = yii::$app->params['conf']['offer'];
			$fee = ($feeArray->fee / 100) * $persontage;

		} else {
			$fee = $feeArray->fee;
		}

		$discount = $feeArray->fee - $fee;
		$responce = [
			'badgeFee'=>$feeArray->fee,
			'badgeSpecialFee' =>$fee,
			'discount'=>$discount,
		];
		return $responce;
	}

}
