<?php

$this->title = 'PayPal Process Test';
$this->params['breadcrumbs'][] = ['label' => 'Admin Function', 'url' => ['badge/admin-function']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['payment/paypaltest']];

echo $this->render('_view-tab-menu', ['confParams' => $confParams]).PHP_EOL;

// Step 3
$payer = new \PayPal\Api\Payer();
$payer->setPaymentMethod('paypal');

$amount = new \PayPal\Api\Amount();
$amount->setTotal('4.00');
$amount->setCurrency('USD');

$transaction = new \PayPal\Api\Transaction();
$transaction->setAmount($amount);

$redirectUrls = new \PayPal\Api\RedirectUrls();
$redirectUrls->setReturnUrl(yii::$app->params['rootUrl']."/payment/paypalprocess?success=true&to=test")
    ->setCancelUrl(<yii::$app->params['rootUrl']."/payment/paypalprocess?success=false&to=test");

$payment = new \PayPal\Api\Payment();
$payment->setIntent('sale')
    ->setPayer($payer)
    ->setTransactions(array($transaction))
    ->setRedirectUrls($redirectUrls);


//Step 4
try {
    $payment->create($apiContext);
    echo $payment;
	$approvalUrl = $payment->getApprovalLink();
    echo "<br /><br />\n\nRedirect user to approval_url: <a href='$approvalUrl' >$approvalUrl</a> <br />\n";
	//echo "Created Payment Using PayPal. Please visit the URL to Approve.", "Payment", "<a href='$approvalUrl' >$approvalUrl</a>", $request, $payment);

}
catch (\PayPal\Exception\PayPalConnectionException $ex) {
    // This will print the detailed information on the exception.
    //REALLY HELPFUL FOR DEBUGGING
    echo $ex->getData();
}

?>
