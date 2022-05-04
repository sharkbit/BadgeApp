<?php
use QuickBooksOnline\API\Core\HttpClients\CurlHttpClient;

use QuickBooksOnline\Payments\PaymentClient;
use QuickBooksOnline\Payments\Operations\ChargeOperations;
use QuickBooksOnline\Payments\Interceptors\{StackTraceLoggerInterceptor, RequestResponseLoggerInterceptor, ExceptionOnErrorInterceptor};

use backend\controllers\PaymentController;

$this->title = 'Process ';
$this->params['breadcrumbs'][] = ['label' => 'Admin Function', 'url' => ['badge/admin-function']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['payment/process']];

echo $this->render('_view-tab-menu', ['confParams' => $confParams]).PHP_EOL;

	//Change your Access Token Here
	$dataService = yii::$app->controller->OAuth();
	$accessToken = "Bearer " . $_SESSION['_access_token'];
	//Add your request ID here
	$requestId = "Som3Th1ngUique".rand(100000,999999);

	if(Yii::$app->params['env'] == 'prod') {
		$baseURL = "https://api.intuit.com/quickbooks/v4/payments/charges";          // Production
		$payenv = 'production';
	} else {
		$baseURL = "https://sandbox.api.intuit.com/quickbooks/v4/payments/charges";
		$payenv = 'sandbox';
	}

	$client = new PaymentClient([
		'access_token' => $_SESSION['_access_token'],
		'environment' => $payenv //  or 'environment' => "production"
	]);
	$LogFolder = Yii::getAlias('@webroot/');
	$client->addInterceptor("FileInterceptor", new RequestResponseLoggerInterceptor($LogFolder, 'America/New_York'));
	//$client->addInterceptor("LoggerInterceptor", new StackTraceLoggerInterceptor($LogFolder."qb_errorLog.txt"));

	echo 'Process Url: '.$baseURL."<br /><br />\n";

	$body = [
	  "amount" => "10.55",
	  "card" => [
		"expYear" => "2035",
		"expMonth" => "02",
		"address" => [
		  "region" => "CA",
		  "postalCode" => "94086",
		  "streetAddress" => "1130 Kifer Rd",
		  "country" => "US",
		  "city" => "Sunnyvale"
		],
		"name" => "emulate=0",
		"cvc" => "123",
		"number" => "4111111111111111"
	  ],
	  "currency" => "USD",
	  "context" => [
			"mobile" => "false",
			"isEcommerce" => "true"
	  ]
	];

$charge = ChargeOperations::buildFrom($body);
$response = $client->charge($charge);
/*
$cardbad = ChargeOperations::buildFrom([
        "expMonth"=> "12",
            "address"=> [
              "postalCode"=> "44112",
              "city"=> "Richmond",
              "streetAddress"=> "1245 Hana Rd",
              "region"=> "VA",
              "country"=> "US"
            ],
            "number"=> "4131979708684369",
            "name"=> "Test User",
            "expYear"=> "2026"
      ]);
$clientId = rand();
$response = $client->createCard($cardbad, $clientId, rand() . "abd");
*/
if($response->failed()){
    $code = $response->getStatusCode();
    $errorMessage = $response->getBody();
    echo "code is $code \n";
    echo "body is $errorMessage \n";
}else{
  $responseCharge = $response->getBody();
  //Get the Id of the charge request
  $id = $responseCharge->id;
  //Get the Status of the charge request
  $status = $responseCharge->status;
  echo "Id is " . $id . "\n";
  echo "status is " . $status . "\n<br /><hr />";
  
  var_dump($responseCharge);
}

?>
