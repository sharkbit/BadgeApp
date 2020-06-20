<?php
require ("CurlClient.php");

use backend\controllers\PaymentController;
use QuickBooksOnline\API\Core\OAuth\OAuth1\OAuth1;


$this->title = 'Charge Request';
$this->params['breadcrumbs'][] = ['label' => 'Payment', 'url' => ['payment/index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['payment/chargereq']];

echo $this->render('_view-tab-menu').PHP_EOL;

if (!isset($_SESSION) || !isset($_SESSION['token']) || strlen($_SESSION['token']) < 1 ) {header('Location: /payment/index');	exit; }
$token = unserialize($_SESSION['token']);

$client = new CurlClient();

$baseURL = "https://sandbox.api.intuit.com/quickbooks/v4/payments/charges"; 

$body = [
"amount" => "44.66",
"card" => [
"expYear" => "2020",
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

	$token=unserialize($confParams->token);
	$myOauth = new OAuth1($confParams->consumerKey,$confParams->consumerSecret,$token['oauth_token'],$token['oauth_token_secret']);

	$accessToken = $myOauth->getOAuthHeader($baseURL,  array(), "POST");
	
	$http_header = array(
		  'Accept' => 'application/json',
		  'Request-Id' => time().'-'.rand(999999,10000000),
		  'Authorization' => $accessToken,
		  'Content-Type' => 'application/json;charset=UTF-8'
	);

	echo "Headder: <br />"; echo var_dump($http_header)."<br /><br />\n";
 
	
	$intuitResponse = $client->makeAPICall($baseURL, "POST", $http_header, json_encode($body), null, 0);
	echo "Response: <br />"; echo var_dump($intuitResponse)."<br /><br />\n";

	
?>

