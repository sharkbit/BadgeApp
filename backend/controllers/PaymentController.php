<?php
//http://wiki.consolibyte.com/wiki/doku.php/quickbooks_qbms_integration#getting_started_with_the_desktop_communication_model

namespace backend\controllers;

use Yii;
use backend\controllers\AdminController;
use backend\models\Badges;
use backend\models\Guest;
use backend\models\Sales;
use backend\models\StoreItems;
use backend\models\BadgeSubscriptions;
use backend\models\BadgeCertification;
use backend\models\Params;
use backend\models\CardReceipt;


class PaymentController extends AdminController {

	public function actionCharge() {  // Process charge
		if(isset($_POST['BadgeCertification']['certification_type'])) {
			$model = new BadgeCertification();
			$UseSub = 'cert';
		} elseif(isset($_POST['BadgeSubscriptions']['badge_fee'])) {
			$model = new BadgeSubscriptions();
			$UseSub = 'update';
		} elseif(isset($_POST['Sales']['first_name'])) {
			$model = new Sales();
			$UseSub = 'sales';
		} elseif(isset($_POST['Guest']['payment_type'])) {
			$model = new Guest();
			$UseSub = 'guest';
		} else {
			$model = new Badges();
			$UseSub = false;
		}
//yii::$app->controller->createLog(true, 'trex_C_PayCtl', var_export($_REQUEST,true));
		if ($model->load(Yii::$app->request->post())) {
			$confParams = Params::findOne('1');

			if(Yii::$app->params['env'] == 'prod') {
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
			$tax = 0;
			if($UseSub=='update') {	// Update
				$myPost=$_POST['Badges'];
				if(!(float)$model->amount_due>0) { $err='Amount Due'; } else { $cc_amount = $model->amount_due; }
				if($myPost['state']=='') { $err ='State'; }   else { $cc_state = $myPost['state']; }
				if($myPost['zip']=='') { $err='Zip Code'; }     else { $cc_zip = $myPost['zip']; }
				if($myPost['address']=='') { $err='Address'; } else { $cc_address = $myPost['address']; }
				if($myPost['city']=='') { $err = 'City'; }    else { $cc_city = $myPost['city']; }
				if($myPost['first_name']=='') {$err='First Name';}  else { $first_name=trim($myPost['first_name']); }
				if($myPost['last_name']=='') { $err='Last Name';}  else { $last_name=trim($myPost['last_name']); }
				if($myPost['mem_type']=='') { $err='Member Type';}  else  { $memType=trim($myPost['mem_type']); }
				$tax = $model->tax;
				$MyCart = "[".json_encode(["item"=>$_REQUEST['item_name'],"sku"=>$_REQUEST['item_sku'],"ea"=>$model->badge_fee ,"qty"=>"1","price"=>$model->badge_fee ])."]";
				// not storing multiple discounts yet.					
				if(is_array($_POST['BadgeSubscriptions']['discount'])) {
					foreach ($_POST['BadgeSubscriptions']['discount'] as $d_item ) {
						$d_discount = explode(":",$d_item);
						if($d_discount[0]=='w'){
							$MyCart = json_encode(array_merge(json_decode($MyCart),
							[ ["item"=>'Discount - Work Credits',"sku"=>$confParams->sku_wc_discount,"ea"=>'-'.$d_discount[1] ,"qty"=>"1","price"=>'-'.$d_discount[1] ] ] ) );
						}
					}
				}
				if(isset($_POST['cart'])) {
					$MyCart = json_encode(array_merge(json_decode($MyCart),json_decode($_POST['cart'])));
				}
			} elseif($UseSub=='cert') {	// Certificate
				$myPost=$_POST['Badges'];
				if(!(float)$model->cert_amount_due>0) { $err='Amount Due'; } else { $cc_amount = $model->cert_amount_due; }
				if($myPost['state']=='') { $err ='State'; }   else { $cc_state = $myPost['state']; }
				if($myPost['zip']=='') { $err ='Zip Code'; }  else { $cc_zip = $myPost['zip']; }
				if($myPost['address']=='') { $err ='Address'; } else { $cc_address = $myPost['address']; }
				if($myPost['city']=='') { $err ='City'; }    else { $cc_city = $myPost['city']; }
				if($myPost['first_name']=='') {$err='First Name';}  else { $first_name=trim($myPost['first_name']); }
				if($myPost['last_name']=='') { $err='LastName';}  else { $last_name=trim($myPost['last_name']); }
				$model->badge_number = $_REQUEST['BadgeSubscriptions']['badge_number'];
				$sku = explode("|",$model->certification_type)[0];
				$myCert = StoreItems::find()->where(['sku'=>$sku])->one();
				$MyCart = "[".json_encode(["item"=>$myCert->item,"sku"=>$sku,"ea"=>$myCert->price ,"qty"=>"1","price"=>$myCert->price ])."]";

			} elseif($UseSub=='sales') {
				if(!(float)$model->total>0) { $err = 'Total'; } 	else { $cc_amount = $model->total; }
				if($model->state=='') { $err='State'; }		else { $cc_state = $model->state; }
				if($model->zip=='') { $err = 'Zip Code'; }	else { $cc_zip = $model->zip; }
				if(trim($model->address)=='') { $err='Address';} else { $cc_address = $model->address; }
				if($model->city=='') { $err = 'City'; }		else { $cc_city = $model->city; }
				if($model->first_name=='') { $err='First Name'; } else { $first_name=trim($model->first_name); }
				if($model->last_name=='') { $err='Last Name'; } else { $last_name=trim($model->last_name); }
				if($model->cart=='') { $err = 'No Cart'; } 		else { $MyCart=$model->cart; }
				$tax = $model->tax;

			} elseif($UseSub=='guest') {  // Guest Bands
				if(!(float)$model->amount_due>0) { $err='Amount Due'; }	else { $cc_amount = $model->amount_due; }
				if($model->cc_state=='') { $err ='State'; }	else { $cc_state = $model->cc_state; }
				if($model->cc_zip=='') { $err ='Zip Code'; }else { $cc_zip = $model->cc_zip; }
				if($model->cc_address=='') { $err='Address'; }	else { $cc_address = $model->cc_address; }
				if($model->cc_city=='') { $err ='City'; }	else { $cc_city = $model->cc_city; }
				if($model->cc_name=='') { $err ='Name';}     else { $arr=preg_split("/\s+(?=\S*+$)/",$model->cc_name); $first_name=$arr[0];$last_name=$arr[1];}
				$tax = $model->tax;
				$price_ea=($cc_amount / $model->guest_count);
				$MyCart = "[".json_encode(["item"=>"Guest Bracelet Fee","sku"=>$confParams->guest_sku,"ea"=>number_format($price_ea, 2, '.', ''),"qty"=>$model->guest_count,"price"=>number_format($cc_amount, 2, '.', '') ])."]";

			} else {   		// Create
				if(!(float)$model->amt_due>0) { $err='Amount Due'; } 	else { $cc_amount = $model->amt_due; }
				if($model->state=='') { $err='State'; }		else { $cc_state = $model->state; }
				if($model->zip=='') { $err ='Zip Code'; }		else { $cc_zip = $model->zip; }
				if($model->address=='') { $err='Address'; }	else { $cc_address = $model->address; }
				if($model->city=='') { $err ='City'; }		else { $cc_city = $model->city; }
				if($model->first_name=='') { $err='First Name'; } else { $first_name=trim($model->first_name); }
				if($model->last_name=='') { $err ='Last Name'; } 	else { $last_name=trim($model->last_name); }
				if($model->mem_type=='') { $err ='Member Type'; } 	else { $memType=trim($model->mem_type); }
				$tax = $model->tax;
				$MyCart = "[".json_encode(["item"=>$_REQUEST['item_name'],"sku"=>$_REQUEST['item_sku'],"ea"=>$model->badge_fee ,"qty"=>"1","price"=>$model->badge_fee ])."]";
					// not storing multiple discounts yet.					
				if(is_array($_POST['Badges']['discounts'])) {
					$discount=0;
					foreach ($_POST['Badges']['discounts'] as $d_item ) {
						$d_discount = explode(":",$d_item);
						if($d_discount[0]=='s'){	
							$stu =  (new StoreItems)->find()->where(['sku'=>$confParams->sku_student])->one();
							yii::$app->controller->createLog(true, 'trex-sku_student', var_export($stu,true));
							if($stu){
								$discount += $stu->price;
								$MyCart = json_encode(array_merge(json_decode($MyCart),
									[ ["item"=>'Discount - Student',"sku"=>$confParams->sku_student,"ea"=>'-'.$stu->price ,"qty"=>"1","price"=>'-'.$stu->price ] ] ) );
							}
						}
					}
					$model->discounts=$discount;
				} else { $model->discounts=0; }
				if(isset($_POST['cart'])) {
					$MyCart = json_encode(array_merge(json_decode($MyCart),json_decode($_POST['cart'])));
				}
			}

			if($model->cc_cvc=='') { $err = 'CVC'; }
			if($model->cc_num=='') { $err = 'Credit Card Number'; }

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
						$savercpt->tax = $tax;
						$savercpt->amount = $response['ssl_amount'];
						$savercpt->authCode = $response['ssl_approval_code'];
						$savercpt->name = $first_name.' '.$last_name;
						$savercpt->cardNum = $response['ssl_card_number'];
						$savercpt->cardType = $response['ssl_card_short_description'];
						$savercpt->expYear = $response['ssl_exp_date'];
						$savercpt->expMonth = '0';
						if(is_string($MyCart)) {$savercpt->cart = $MyCart;} else {$savercpt->cart = json_encode($MyCart);}
						$savercpt->cashier = $_SESSION['user'];
						if(is_null($_SESSION['badge_number'])) {$savercpt->cashier_badge = 0;} else {$savercpt->cashier_badge = $_SESSION['badge_number'];}
						if($savercpt->save()) {
							yii::$app->controller->createLog(true, $_SESSION['user'], 'Processed_CC for '.$savercpt->name.' $'.
								$savercpt->amount.', AuthCode: '.$savercpt->authCode.', Card: '.$savercpt->cardNum);
						} else {
							yii::$app->controller->createLog(true, 'trex_C_PayCtl:204 savercpt', var_export($savercpt->errors,true));
						}
						return json_encode(["status"=>"success","message"=>$myrcpt]);

					} elseif($response['ssl_result_message']<>'') {
						//yii::$app->controller->createLog(false, 'trex-response', var_export($response,true));
						yii::$app->controller->createLog(true, $_SESSION['user'], "CC_Error: for $first_name $last_name - ".$response['ssl_result_message']);
						return json_encode(["status"=>"error","message"=>$response['ssl_result_message']]);

					} else {
						yii::$app->controller->createLog(true, 'trex_C_PayCtl:214 Response', var_export($response,true));
						return json_encode(["status"=>"error","message"=>$response]);
					}
				} elseif (isset($response['errorCode'])) {
					yii::$app->controller->createLog(true, $_SESSION['user'], "CC_ERROR:218 ".$response['errorCode']." ".$response['errorName']);
					return json_encode(["status"=>"error","message"=>"Error# ".$response['errorCode'].": ".$response['errorMessage']]);
				} else { //No Response
					yii::$app->controller->createLog(true, 'trex_C_PayCtl:221 No Response', var_export($response,true));
					return json_encode(["status"=>"error","message"=>"No Response or Network Timed Out.  Please use a difrent payment method or try again later."]);
				}
			} else  {
				if (isset($_SERVER['HTTP_REFERER'])) {$missing = $_SERVER['HTTP_REFERER'];} else {$missing='null'; }
				return json_encode(["status"=>"error","message"=>"Please Verify $err."]);
			}

		} else {
			yii::$app->controller->createLog(true, 'trex_C_PayCtl:230 ','not post');
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
