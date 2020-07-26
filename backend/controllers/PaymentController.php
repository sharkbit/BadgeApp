<?php
//http://wiki.consolibyte.com/wiki/doku.php/quickbooks_qbms_integration#getting_started_with_the_desktop_communication_model

namespace backend\controllers;

use Yii;
use QuickBooksOnline\Payments\PaymentClient;
use QuickBooksOnline\Payments\Operations\ChargeOperations;
use backend\components\CurlClient;
use backend\components\Client;
use backend\controllers\AdminController;
use backend\models\Badges;
use backend\models\Guest;
use backend\models\Sales;
use backend\models\BadgeSubscriptions;
use backend\models\BadgeCertification;
use backend\models\Params;
use backend\models\CardReceipt;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\Core\OAuth\OAuth1\OAuth1;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2LoginHelper;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\Payments\Interceptors\{StackTraceLoggerInterceptor, RequestResponseLoggerInterceptor, ExceptionOnErrorInterceptor};

use QuickBooksOnline\API\Facades\Invoice;

use QuickBooksOnline\API\Facades\Payment;
use QuickBooksOnline\API\Data\IPPPayment;

use QuickBooksOnline\API\Facades\Purchase;
use QuickBooksOnline\API\Data\IPPPurchase;

use QuickBooksOnline\API\PlatformService\PlatformService;

class PaymentController extends AdminController {

	public function OAuth() {  // Prep Data Services
		$confParams = Params::findOne('1');
		if($confParams->qb_env == 'prod') {
			$env = 'Production';
			$cID = $confParams->qb_oa2_id;
			$cSec = $confParams->qb_oa2_sec;
		} else {
			$env = 'Development';
			$cID = $confParams->qb_oauth_cust_key;
			$cSec = $confParams->qb_oauth_cust_sec;
		}

		if($confParams->qb_oa2_refresh_token) {
			$dataService = DataService::Configure(array(
				'auth_mode' => 'oauth2',
				'ClientID' => $cID,
				'ClientSecret' => $cSec,
				'refreshTokenKey' => $confParams->qb_oa2_refresh_token,
				'QBORealmID' => $confParams->qb_oa2_realmId,
				'baseUrl' => $env
			));

$oauth2LoginHelper = new OAuth2LoginHelper($cID,$cSec);
$accessTokenObj = $oauth2LoginHelper-> refreshAccessTokenWithRefreshToken($confParams->qb_oa2_refresh_token);
$_SESSION['_access_token'] = $accessTokenObj->getAccessToken();
$confParams->qb_oa2_refresh_token = $accessTokenObj->getRefreshToken();

		//	$OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
		//	$refreshedAccessTokenObj = $OAuth2LoginHelper->refreshToken();
		//	$dataService->updateOAuth2Token($refreshedAccessTokenObj);
		//	$_SESSION['_access_token'] = $refreshedAccessTokenObj->getAccessToken();
		//	$confParams->qb_oa2_refresh_token = $refreshedAccessTokenObj->getRefreshToken();
			$confParams->save();
		}
		else {
			header('Location: /payment/index');
			exit;
		}

		if(!$dataService) {Yii::$app->response->data = "No DS"; exit;}
		$LogDir = yii::$app->basePath."/runtime/transactions";
		if (!is_dir($LogDir)) { mkdir($LogDir, 0755, true); }
		$dataService->setLogLocation($LogDir);
		$dataService->throwExceptionOnError(true);
// yii::$app->controller->createLog(false, 'trex-LogDir', var_export($LogDir,true));
		return $dataService;
	}

	public static function configs() {
		return array(
		  'authorizationRequestUrl' => 'https://appcenter.intuit.com/connect/oauth2',
		  'tokenEndPointUrl' => 		'https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer',
		  'oauth_scope' => 	'com.intuit.quickbooks.accounting com.intuit.quickbooks.payment',
		//  'openID_scope' => 'openid agc email',						//Example 'openid profile email',
		  'oauth_redirect_uri'  => yii::$app->params['rootUrl'].'/payment/oauth2',
		  'openID_redirect_uri' => yii::$app->params['rootUrl'].'/payment/oauthopen',
		  'mainPage' => 		   yii::$app->params['rootUrl'].'/payment/index2',
		  'refreshTokenPage' =>    yii::$app->params['rootUrl'].'/payment/refreshtoken',
		);
	}

	public static function paypalConfig() {
				// Step 2
		$apiContext = new \PayPal\Rest\ApiContext(
				new \PayPal\Auth\OAuthTokenCredential(
					'AfuNWmEFeVu8NZnV07zfSGqljrmW5XZ-X9qSiAV67FYb74F_tj5cFcphpeFlVVxD8vGMdPSk1LgyvZJS',     // ClientID
					'EFArx_CefPe-Vv9fR4X2ynsnYeWeJCzfbEogCpOPpgDHXZn8gj8xeKxe_j8NROSOBzaujCJcyZ8DMrK5'      // ClientSecret
				)
		);

		// Step 2.1 : Between Step 2 and Step 3
		$apiContext->setConfig(
			  array(
				'log.LogEnabled' => true,
				'log.FileName' => 'PayPal.log',
				'log.LogLevel' => 'DEBUG'
			  )
		);
		return $apiContext;
	}

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
			$dataService = $this->OAuth();
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
						yii::$app->controller->createLog(false, 'trex-response', var_export($response,true));
						yii::$app->controller->createLog(true, $_SESSION['user'], "CC_Error: for $first_name $last_name - ".$response['ssl_result_message']);
						return json_encode(["status"=>"error","message"=>$response['ssl_result_message']]);

					} else {
						yii::$app->controller->createLog(true, 'trex_C_PC:286 Response', var_export($response,true));
						return json_encode(["status"=>"error","message"=>$response]);
					}
				} elseif (isset($response['errorCode'])) {
					yii::$app->controller->createLog(true, $_SESSION['user'], "CC_ERROR:318 ".$response['errorCode']." ".$response['errorName']);
					return json_encode(["status"=>"error","message"=>$response['errorMessage']]);
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

	public function actionChargereq() {  //render  Charge Req
		$confParams = Params::findOne('1');
		return $this->render('chargereq', [
			'confParams' => $confParams
		]);
	}

	public function actionConverge(){
		$confParams = Params::findOne('1');
		return $this->render('converge', ['confParams' => $confParams]);
	}

	public function actionDisconnect() {  //render  disconn
		// Prep Data Services
		$dataService = $this->OAuth();
		$confParams = Params::findOne('1');
		return $this->render('disconnect', [
			'confParams' => $confParams,
			'dataService' => $dataService
		]);
	}

/*	public function actionIndex() {  //redirect Index
		return $this->redirect('payment/index2');
	} */

	public function actionIndex() {  //render Oauth2 Index
		$confParams = Params::findOne('1');
		return $this->render('index', [
			'confParams' => $confParams
		]);
	}

	public function actionOauthopen() {  //render
		$confParams = Params::findOne('1');
		return $this->render('oauthopen', [
			'confParams' => $confParams
		]);
	}

	public function actionPaypalprocess() {  //test Paypal
		$apiContext = $this->paypalConfig();
		$confParams = Params::findOne('1');
		return $this->render('paypalexecute', [
			'apiContext'=> $apiContext,
			'confParams' => $confParams,
		]);
	}

	public function actionPaypalsetup() {  //test Paypal
		$apiContext = $this->paypalConfig();
		$confParams = Params::findOne('1');
//yii::$app->controller->createLog(false, 'trex_C_PC:339', var_export($_REQUEST,true));
yii::$app->controller->createLog(false, 'trex_C_PC:335',"ppxx1");
		$payer = new \PayPal\Api\Payer();
		$payer->setPaymentMethod('paypal');
		$amount = new \PayPal\Api\Amount();

		if(isset($_REQUEST['Sales']['total']) && $_REQUEST['Sales']['total'] > 0) {
yii::$app->controller->createLog(false, 'trex_C_PC:335',"ppxx2");
			$returnTo="store";
			$myDescription = "AGC Store";

			$cart = json_decode($_REQUEST['Sales']['cart']);
			$i=0;
			foreach($cart as $name => $thing) {
				$items[$i] = new \PayPal\Api\Item();
				$items[$i]->setName($thing->item)
					->setCurrency('USD')
					->setQuantity($thing->qty)
					->setSku($thing->sku)
					->setPrice($thing->ea);
				$i++;
			}
			$details = new \PayPal\Api\Details();
			$details->setSubtotal($_REQUEST['Sales']['total']);
			$amount->setCurrency('USD')
				->setTotal($_REQUEST['Sales']['total'])
				->setDetails($details);


		} else {
yii::$app->controller->createLog(false, 'trex_C_PC:335',"ppxx3");
			$returnTo="test";
			$myDescription = "Test Purchas";

			$items[0] = new \PayPal\Api\Item();
			$items[0]->setName('Ground Coffee 40 oz')
				->setCurrency('USD')
				->setQuantity(1)
				->setSku("123123")
				->setPrice(7.5);
			$items[1] = new \PayPal\Api\Item();
			$items[1]->setName('Granola bars')
				->setCurrency('USD')
				->setQuantity(5)
				->setSku("321321") // Similar to `item_number` in Classic API
				->setPrice(2);

			$details = new \PayPal\Api\Details();
			//$details->setShipping(1.2)->setTax(1.3)->setSubtotal(17.50);
			$details->setSubtotal(17.50);
			$amount->setCurrency('USD')
				->setTotal(17.50)
				->setDetails($details);
		}

		// Step 3
		$itemList = new \PayPal\Api\ItemList();
		$itemList->setItems($items);

		$transaction = new \PayPal\Api\Transaction();
		$transaction->setAmount($amount)
			->setItemList($itemList)
			->setDescription($myDescription);

		$redirectUrls = new \PayPal\Api\RedirectUrls();
		$redirectUrls->setReturnUrl(yii::$app->params['rootUrl']."/payment/paypalprocess?success=true&to=".$returnTo)
			->setCancelUrl(yii::$app->params['rootUrl']."/payment/paypalprocess?success=false&to=".$returnTo);

		$payment = new \PayPal\Api\Payment();
		$payment->setIntent('sale')
			->setPayer($payer)
			->setTransactions(array($transaction))
			->setRedirectUrls($redirectUrls);

		//Step 4
		try {
			$payment->create($apiContext);
			//echo $payment;
			$approvalUrl = $payment->getApprovalLink();
			echo "<br /><br />\n\nRedirect user to approval_url: <a href='$approvalUrl' >$approvalUrl</a> <br />\n";
			//echo "Created Payment Using PayPal. Please visit the URL to Approve.", "Payment", "<a href='$approvalUrl' >$approvalUrl</a>", $request, $payment);

		}
		catch (\PayPal\Exception\PayPalConnectionException $ex) {
			// This will print the detailed information on the exception.
			//REALLY HELPFUL FOR DEBUGGING
			echo $ex->getData();
		}
	}

	public function actionRefreshtoken($auto = false) {  //render
		$confParams = Params::findOne('1');

		$configs = $this->configs();
		$mainPage = $configs['mainPage'];

		if (!isset($_GET["deleteSession"])) {
			$dataService = $this->OAuth();

			if(!$auto) {
				Yii::$app->response->data = '<script type="text/javascript">
					window.location.href = \'' .$mainPage . '\';
				  </script>';
			}
		} else {
			$confParams->qb_oa2_realmId = null;
			$confParams->qb_oa2_access_token = null;
			$confParams->qb_oa2_access_date = null;
			$confParams->qb_oa2_refresh_token = null;
			$confParams->qb_oa2_refresh_date = null;
			$confParams->save();

			Yii::$app->response->data = '<script type="text/javascript">
					  window.location.href = \'' .$mainPage . '\';
					</script>';
		}
	}

	public function actionInfo() { // save to file ??
		$fp = fopen('runtime/out.txt', 'a+');

		foreach (getallheaders() as $name => $value) {
			fwrite($fp, print_r("$name: $value\n", true));
		}

		fwrite($fp, print_r($_SERVER, true));
		fwrite($fp, print_r($_POST, true));
		fwrite($fp, print_r($_GET, true));
		fclose($fp);
	}

	public function actionInvoice() {   //Add a new Invoice
		// Prep Data Services
		$dataService = $this->OAuth();

		$confParams = Params::findOne('1');
		return $this->render('invoice', [
			'dataService' => $dataService,
			'confParams' => $confParams
		]);
	}

	public function actionInventory() {
		// Prep Data Services
		$dataService = $this->OAuth();

		$confParams = Params::findOne('1');
		return $this->render('inventory', [
			'dataService' => $dataService,
			'confParams' => $confParams
		]);

	}

	public function actionOauth() {  //render Oauth
		$confParams = Params::findOne('1');
		return $this->render('oauth', [
			'confParams' => $confParams
		]);
	}

	public function actionOauth2() {  //render Oauth2
		$confParams = Params::findOne('1');
		return $this->render('oauth2', [
			'confParams' => $confParams
		]);
	}

	public function actionPage() {  //render  page
		$dataService = $this->OAuth();
		$confParams = Params::findOne('1');
		return $this->render('page', [
			'dataService' => $dataService,
			'confParams' => $confParams
		]);
	}

	public function actionProcess() { //Test CC Process
		$confParams = Params::findOne('1');
		if ($confParams->qb_oa2_access_date < yii::$app->controller->getNowTime()) {
			// token only good for an hour, so get a new one if expired.
			$this->actionRefreshtoken(true);
			$confParams = Params::findOne('1');
		}

		return $this->render('process', [
			'confParams' => $confParams
		]);
	}

	public function actionPurchase() {  //Spend money...
		// Prep Data Services
		$dataService = $this->OAuth();

		// Create a new Purchase Object
		$randomPurchaseObj = Purchase::create([
		  "AccountRef" => [
		 "value"=> "42",
		 "name"=> "Visa"
		],
		"PaymentType"=> "CreditCard",
		"Line"=> [
		 [
		   "Amount"=> 10.00,
		   "DetailType"=> "AccountBasedExpenseLineDetail",
		   "AccountBasedExpenseLineDetail"=> [
			"AccountRef"=> [
			   "name"=> "Meals and Entertainment",
			   "value"=> "11"
			 ]
		   ]
		 ]
		]
		]);
		$purchaseObjConfirmation = $dataService->Add($randomPurchaseObj);
		Yii::$app->response->data = "Created Purchase object, and received Id={$purchaseObjConfirmation->Id} <br />\n";

		// Find the recently-created Purchase Object by Id
		$purchaseObj = new IPPPurchase();
		$purchaseObj->Id=$purchaseObjConfirmation->Id;
		$purchaseObj->domain=$purchaseObjConfirmation->domain;
		$crudResultObj = $dataService->FindById($purchaseObj);
		if ($crudResultObj) {
			Yii::$app->response->data = "Found the purchase object that we just created.\n";
			print "<pre>";
			print_r($crudResultObj);
			print "</pre>";
		} else {
			Yii::$app->response->data = "Did not find the purchase object that we just created.\n";
		}

		/*  Example output:

		Created Purchase object, and received Id=807
		Found the purchase object that we just created.
		*/
	}

	public function actionReconnect() {  // render  disconn
		// Prep Data Services
		$dataService = $this->OAuth();

		$confParams = Params::findOne('1');
		return $this->render('reconnect', [
			'confParams' => $confParams,
			'dataService' => $dataService
		]);
	}

	public function GetInventory() {
		$dataService = PaymentController::OAuth();
		$inventory = $dataService->Query('SELECT * FROM Item');
		return $inventory;
	}
}
