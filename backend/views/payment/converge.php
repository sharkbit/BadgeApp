<?php

use yii\helpers\Html;

$this->title = 'Converge ';
$this->params['breadcrumbs'][] = ['label' => 'Admin Function', 'url' => ['badge/admin-function']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['payment/process']];

echo $this->render('_view-tab-menu', ['confParams' => $confParams]).PHP_EOL;

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
	

	echo "Runing :<br>ID: $merchantID, <br>User: $merchantUserID, <br> Pin: $merchantPIN, <br>Is Test: ";
	if ($payenv) { echo "Production"; } else { echo "Dev"; }
	echo "<br>url: $URL <hr>";
// Create new PaymentProcessor object
	$PaymentProcessor = new \markroland\Converge\ConvergeApi( $merchantID, $merchantUserID, $merchantPIN, $payenv);

// Submit a purchase
$response = $PaymentProcessor->ccsale(
    array(
		// 'ssl_amount' => '9.99',		// Approve
		'ssl_amount' => '9.89',			// Declind
		// 'ssl_amount' => '9.41',	// Stollen
		// 'ssl_amount' => '9.53',	// expired
		// 'ssl_card_number' => '5121212121212124',		//MC
		// 'ssl_card_number' => '4000000000000002',		// Visa
		'ssl_card_number' => '4159288888888882', //visa 2
		'ssl_cvv2cvc2' => '123',
		'ssl_exp_date' => '1222',
		'ssl_avs_zip' => '21075',
		'ssl_avs_address' => '123 main',
		'ssl_city' => 'Elkridge',
		'ssl_state' => 'MD',
		'ssl_first_name' => 'John',
		'ssl_last_name' => 'Smith'
    )
);

// Display Converge API response
echo('ConvergeApi->ccsale Response:' . "<br>");
print_r($response);

yii::$app->controller->createLog(false, 'trex_response', print_r($response,true));

if(isset($response['ssl_result_message'])) {
echo "<hr>".
"<br>Message: ".$response['ssl_result_message'].
"<br>Approval code: ".$response['ssl_approval_code'].
"<br>Card Type: ".$response['ssl_card_short_description'].
"<br>Card#: ".$response['ssl_card_number'];
}
	
?>