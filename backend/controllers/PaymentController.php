<?php
//http://wiki.consolibyte.com/wiki/doku.php/quickbooks_qbms_integration#getting_started_with_the_desktop_communication_model

namespace backend\controllers;

use Yii;
use backend\controllers\AdminController;
use backend\models\Badges;
use backend\models\Guest;
use backend\models\Sales;
use backend\models\BadgeSubscriptions;
use backend\models\BadgeCertification;
use backend\models\Params;
use backend\models\CardReceipt;


class PaymentController extends AdminController {

	public function actionCharge() {  // Process charge
		if(isset($_POST['BadgeSubscriptions']['badge_fee'])) {
			$model = new BadgeSubscriptions();
			$UseSub = 'update';
		} elseif(isset($_POST['BadgeCertification']['certification_type'])) {
			$model = new BadgeCertification();
			$UseSub = 'cert';
		} elseif(isset($_POST['Sales']['first_name'])) {
			$model = new Sales();
			$UseSub = 'sales';
		} elseif(isset($_POST['Guest']['payment_type'])) {
			$model = new Guest();
			$UseSub = 'guest';
//  yii::$app->controller->createLog(false, 'trex_C_PayCtl:132', var_export($_POST,true));
		} else {
			$model = new Badges();
			$UseSub = false;
		}

		if ($model->load(Yii::$app->request->post())) {
			$confParams = Params::findOne('1');

			if($confParams->qb_env == 'prod') {
				$payenv = true; //'production';
				$URL="https://api.convergepay.com/VirtualMerchant/processxml.do";
				$merchantID = $confParams->conv_p_merc_id; //Converge 6 or 7-Digit Account ID *Not the 10-Digit Elavon Merchant ID*
				$merchantUserID = $confParams->conv_p_user_id; //Converge User ID *MUST FLAG AS HOSTED API USER IN CONVERGE UI*
				$merchantPIN = $confParams->conv_p_pin; //Converge PIN (64 CHAR A/N)
			} else {
				$payenv = false; //'sandbox';
				$URL="https://api.demo.convergepay.com/VirtualMerchantDemo/processxml.do";
				$merchantID = $confParams->conv_d_merc_id;
				$merchantUserID = $confParams->conv_d_user_id;
				$merchantPIN = $confParams->conv_d_pin;
			}
			$err = false;
			if($UseSub=='update') {	// Update
				$myPost=$_POST['Badges'];
				if($model->amount_due=='') { $err = true; } else { $cc_amount = $model->amount_due; }
				$Badge_price = $model->badge_fee - $_POST['BadgeSubscriptions']['discount'];
				if($myPost['state']=='') { $err = true; }   else { $cc_state = $myPost['state']; }
				if($myPost['zip']=='') { $err = true; }     else { $cc_zip = $myPost['zip']; }
				if($myPost['address']=='') { $err = true; } else { $cc_address = $myPost['address']; }
				if($myPost['city']=='') { $err = true; }    else { $cc_city = $myPost['city']; }
				if($myPost['first_name']=='') {$err=true;}  else { $first_name=trim($myPost['first_name']); }
				if($myPost['last_name']=='') { $err=true;}  else { $last_name=trim($myPost['last_name']); }
				if($myPost['mem_type']=='') { $err=true;}  else  { $memType=trim($myPost['mem_type']); }
				$MyCart = Badges::GetCart($_POST['cart'],$memType, $model->badge_fee, $Badge_price);

			} elseif($UseSub=='cert') {	// Certificate
				$myPost=$_POST['Badges'];
				if($model->cert_amount_due=='') { $err = true; } else { $cc_amount = $model->cert_amount_due; }
				if($myPost['state']=='') { $err = true; }   else { $cc_state = $myPost['state']; }
				if($myPost['zip']=='') { $err = true; }     else { $cc_zip = $myPost['zip']; }
				if($myPost['address']=='') { $err = true; } else { $cc_address = $myPost['address']; }
				if($myPost['city']=='') { $err = true; }    else { $cc_city = $myPost['city']; }
				if($myPost['first_name']=='') {$err=true;}  else { $first_name=trim($myPost['first_name']); }
				if($myPost['last_name']=='') { $err=true;}  else { $last_name=trim($myPost['last_name']); }
				if ($model->certification_type==5) {
					$MyCart = ["item"=>"Steel Certification","sku"=>410105,"ea"=>10.00 ,"qty"=>"1","price"=> 10.00 ];
				} elseif ($model->certification_type==6) {
					$MyCart = ["item"=>"Holster Certification","sku"=>410100,"ea"=>20.00 ,"qty"=>"1","price"=> 20.00 ];
				}

			} elseif($UseSub=='sales') {
				if($model->total=='') { $err = true; } 		else { $cc_amount = $model->total; }
				if($model->state=='') { $err = true; }		else { $cc_state = $model->state; }
				if($model->zip=='') { $err = true; }		else { $cc_zip = $model->zip; }
				if($model->address=='') { $err = true; }	else { $cc_address = $model->address; }
				if($model->city=='') { $err = true; }		else { $cc_city = $model->city; }
				if($model->first_name=='') { $err = true; } else { $first_name=trim($model->first_name); }
				if($model->last_name=='') { $err = true; } 	else { $last_name=trim($model->last_name); }
				if($model->cart=='') { $err = true; } 		else { $MyCart=$model->cart; }

			} elseif($UseSub=='guest') {  // Guest Bands
				if($model->amount_due=='') { $err = true; }	else { $cc_amount = $model->amount_due; }
				if($model->g_state=='') { $err = true; }	else { $cc_state = $model->g_state; }
				if($model->g_zip=='') { $err = true; }		else { $cc_zip = $model->g_zip; }
				if($model->cc_address=='') { $err = true; }	else { $cc_address = $model->cc_address; }
				if($model->cc_city=='') { $err = true; }	else { $cc_city = $model->cc_city; }
				if($model->g_first_name=='') { $err = true;}else { $first_name=trim($model->g_first_name); }
				if($model->g_last_name=='') { $err = true;} else { $last_name=trim($model->g_last_name); }
				$price_ea=($cc_amount / $model->guest_count);
				$MyCart = "[".json_encode(["item"=>"Guest Bracelet Fee","sku"=>$confParams->guest_sku,"ea"=>number_format($price_ea, 2, '.', ''),"qty"=>$model->guest_count,"price"=>number_format($cc_amount, 2, '.', '') ])."]";

			} else {   		// Create
				if($model->amt_due=='') { $err = true; } 	else { $cc_amount = $model->amt_due; }
				$Badge_price = $model->badge_fee - $model->discounts;
				if($model->state=='') { $err = true; }		else { $cc_state = $model->state; }
				if($model->zip=='') { $err = true; }		else { $cc_zip = $model->zip; }
				if($model->address=='') { $err = true; }	else { $cc_address = $model->address; }
				if($model->city=='') { $err = true; }		else { $cc_city = $model->city; }
				if($model->first_name=='') { $err = true; } else { $first_name=trim($model->first_name); }
				if($model->last_name=='') { $err = true; } 	else { $last_name=trim($model->last_name); }
				if($model->mem_type=='') { $err = true; } 	else { $memType=trim($model->mem_type); }
				$MyCart=Badges::GetCart( $_REQUEST['cart'],$memType, $model->badge_fee, $Badge_price,true );
			}

			if($model->cc_cvc=='') { $err = true; }
			if($model->cc_num=='') { $err = true; }

			if($err==false) {

				$model->cc_exp_yr = $model->cc_exp_yr + date('Y',strtotime(yii::$app->controller->getNowTime()));
				$exp = strval($model->cc_exp_mo) . strval(substr(trim($model->cc_exp_yr),-2));
				$model->cc_num = preg_replace('/\D/','',$model->cc_num);

				$PaymentProcessor = new \markroland\Converge\ConvergeApi( $merchantID, $merchantUserID, $merchantPIN, $payenv);
				$response = $PaymentProcessor->ccsale(
					array(
						'ssl_amount' => $cc_amount,				//'9.99',
						'ssl_card_number' => $model->cc_num,	//'5000300020003003',
						'ssl_cvv2cvc2' => $model->cc_cvc,		//'123',
						'ssl_exp_date' => $exp,					//'1222',
						'ssl_avs_zip' => $cc_zip,				//'21075',
						'ssl_avs_address' => $cc_address,		//'123 main',
						'ssl_city' => $cc_city,					//'Elkridge',
						'ssl_state' => $cc_state,				//'MD',
						'ssl_first_name' => $first_name,		//'John',
						'ssl_last_name' => $last_name			//'Smith'
					)
				);

				if (isset($response['ssl_result_message'])) {
					if($response['ssl_result_message']=='APPROVAL') {
						$myrcpt = (object)[];
						$myrcpt->id = $response['ssl_txn_id'];
						$myrcpt->status = 'CAPTURED';
						$myrcpt->authCode = $response['ssl_approval_code'];
						$myrcpt->cardNum = $response['ssl_card_number'];

						$savercpt = new CardReceipt();
						$savercpt->id = $response['ssl_txn_id'];
						$savercpt->badge_number = $model->badge_number;
						$savercpt->tx_date = $this->getNowTime();
						$savercpt->tx_type = 'creditnow';
						$savercpt->status = 'APPROVED';
						$savercpt->amount = $response['ssl_amount'];
						$savercpt->authCode = $response['ssl_approval_code'];
						$savercpt->name = $first_name.' '.$last_name;
						$savercpt->cardNum = $response['ssl_card_number'];
						$savercpt->cardType = $response['ssl_card_short_description'];
						$savercpt->expYear = $response['ssl_exp_date'];
						$savercpt->expMonth = '0';
						if(is_string($MyCart)) {$savercpt->cart = $MyCart;} else {$savercpt->cart = json_encode($MyCart);}
						$savercpt->cashier = $_SESSION['user'];
						if($savercpt->save()) {
							yii::$app->controller->createLog(true, $_SESSION['user'], 'Processed_CC for '.$savercpt->name.' $'.
								$savercpt->amount.', AuthCode: '.$savercpt->authCode.', Card: '.$savercpt->cardNum);
						} else {
							yii::$app->controller->createLog(true, 'trex_C_PC:257 savercpt', var_export($savercpt->errors,true));
						}
						return json_encode(["status"=>"success","message"=>$myrcpt]);

					} elseif($response['ssl_result_message']<>'') {
						//yii::$app->controller->createLog(false, 'trex-response', var_export($response,true));
						yii::$app->controller->createLog(true, $_SESSION['user'], "CC_Error: for $first_name $last_name - ".$response['ssl_result_message']);
						return json_encode(["status"=>"error","message"=>$response['ssl_result_message']]);

					} else {
						yii::$app->controller->createLog(true, 'trex_C_PC:286 Response', var_export($response,true));
						return json_encode(["status"=>"error","message"=>$response]);
					}
				} elseif (isset($response['errorCode'])) {
					yii::$app->controller->createLog(true, $_SESSION['user'], "CC_ERROR:318 ".$response['errorCode']." ".$response['errorName']);
					return json_encode(["status"=>"error","message"=>"Error# ".$response['errorCode'].": ".$response['errorMessage']]);
				} else { //real error
					yii::$app->controller->createLog(true, 'trex_C_PC:293 Response', var_export($response,true));
					return json_encode(["status"=>"error","message"=>"It Broke, Call Marc... (Payment Error Response:291)"]);
				}
			} else  {
				yii::$app->controller->createLog(true, 'trex_C_PayCtl:297 error', 'Form Data missing');
				yii::$app->controller->createLog(true, 'trex_C_PayCtl:298'," Amount: $cc_amount, Addr: $cc_address, City: $cc_city, State: $cc_state, Zip: $cc_zip, F_Name: $first_name, L_Name: $last_name");

				return json_encode(["status"=>"error","message"=>"Please Verify Form Information."]);
			}

		} else {
			yii::$app->controller->createLog(true, 'trex_C_PayCtl:304 ','not post');
			//echo Yii::$app->response->data = "Fail";
	//		$this->redirect('/');
		}
	}

	public function actionConverge(){
		$confParams = Params::findOne('1');
		return $this->render('converge', ['confParams' => $confParams]);
	}

	public function actionIndex() {
		$confParams = Params::findOne('1');
		return $this->render('index', [
			'confParams' => $confParams
		]);
	}

	public function actionInventory() {
		$confParams = Params::findOne('1');
		return $this->render('inventory', [
			'confParams' => $confParams
		]);
	}

	public function GetInventory() {
		$inventory = $dataService->Query('SELECT * FROM Item');
		return $inventory;
	}
}
