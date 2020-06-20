<?php

use backend\controllers\PaymentController;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;



class TVarDumper {

    private static $_objects;
    private static $_output;
    private static $_depth;

    /**
     * Converts a variable into a string representation.
     * This method achieves the similar functionality as var_dump and print_r
     * but is more robust when handling complex objects such as PRADO controls.
     * @param mixed variable to be dumped
     * @param integer maximum depth that the dumper should go into the variable. Defaults to 10.
     * @return string the string representation of the variable
     */
    public static function dump($var,$depth=10,$highlight=false) {
        self::$_output='';
        self::$_objects=array();
        self::$_depth=$depth;
        self::dumpInternal($var,0);
        if($highlight) {
            $result=highlight_string("<?php\n".self::$_output,true);
            return preg_replace('/&lt;\\?php<br \\/>/','',$result,1);
        }
        else
            return self::$_output;
    }

    private static function dumpInternal($var,$level) {
        switch(gettype($var)) {
            case 'boolean':
                self::$_output.=$var?'true':'false';
                break;
            case 'integer':
                self::$_output.="$var";
                break;
            case 'double':
                self::$_output.="$var";
                break;
            case 'string':
                self::$_output.="'$var'";
                break;
            case 'resource':
                self::$_output.='{resource}';
                break;
            case 'NULL':
                self::$_output.="null";
                break;
            case 'unknown type':
                self::$_output.='{unknown}';
                break;
            case 'array':
                if(self::$_depth<=$level)
                    self::$_output.='array(...)';
                else if(empty($var))
                    self::$_output.='array()';
                else {
                    $keys=array_keys($var);
                    $spaces=str_repeat(' ',$level*4);
                    self::$_output.="array\n".$spaces.'(';
                    foreach($keys as $key) {
                        self::$_output.="\n".$spaces."    [$key] => ";
                        self::$_output.=self::dumpInternal($var[$key],$level+1);
                    }
                    self::$_output.="\n".$spaces.')';
                }
                break;
            case 'object':
                if(($id=array_search($var,self::$_objects,true))!==false)
                    self::$_output.=get_class($var).'#'.($id+1).'(...)';
                else if(self::$_depth<=$level)
                    self::$_output.=get_class($var).'(...)';
                else {
                    $id=array_push(self::$_objects,$var);
                    $className=get_class($var);
                    $members=(array)$var;
                    $keys=array_keys($members);
                    $spaces=str_repeat(' ',$level*4);
                    self::$_output.="$className#$id\n".$spaces.'(';
                    foreach($keys as $key)
                    {
                        $keyDisplay=strtr(trim($key),array("\0"=>':'));
                        self::$_output.="\n".$spaces."    [$keyDisplay] => ";
                        self::$_output.=self::dumpInternal($members[$key],$level+1);
                    }
                    self::$_output.="\n".$spaces.')';
                }
                break;
        }
    }
}



//$this->title = 'PayPal execute Test';
//$this->params['breadcrumbs'][] = ['label' => 'Admin Function', 'url' => ['badge/admin-function']];
//$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['payment/paypaltest']];

//echo $this->render('_view-tab-menu', ['confParams' => $confParams]).PHP_EOL;

function printResult($a,$b='',$c='',$d='',$e=''){
	echo "$a - $b:$c\n<br>";
	echo var_export($d);
	echo var_export($e);
}

// ### Approval Status
// Determine if the user approved the payment or not
if (isset($_GET['success']) && $_GET['success'] == 'true') {
	echo "Redirect to: ".$_GET['to']."<br />\n";
//$payment = $_SESSION['pp_payment'];
	// Get the payment Object by passing paymentId
	// payment id was previously stored in session in
	// CreatePaymentUsingPayPal.php
	$paymentId = $_GET['paymentId'];
	$payment = Payment::get($paymentId, $apiContext);

	// ### Payment Execute
	// PaymentExecution object includes information necessary
	// to execute a PayPal account payment.
	// The payer_id is added to the request query parameters
	// when the user is redirected from paypal back to your site
	$execution = new PaymentExecution();
	$execution->setPayerId($_GET['PayerID']);

	// ### Optional Changes to Amount
	// If you wish to update the amount that you wish to charge the customer,
	// based on the shipping address or any other reason, you could
	// do that by passing the transaction object with just `amount` field in it.
	// Here is the example on how we changed the shipping to $1 more than before.
	$transaction = new Transaction();
	$amount = new Amount();
	$details = new Details();

	$details->setShipping(2.2)
		->setTax(1.3)
		->setSubtotal(17.50);

	$amount->setCurrency('USD');
	$amount->setTotal(21);
	$amount->setDetails($details);
	$transaction->setAmount($amount);

	// Add the above transaction object inside our Execution object.
	$execution->addTransaction($transaction);

	try {
		// Execute the payment
		// (See bootstrap.php for more on `ApiContext`)
		$result = $payment->execute($execution, $apiContext);

		printResult("Executed Payment", "Payment", $payment->getId(), $execution, $result);
		echo " x1 <br />\n";
		try {
			$payment = Payment::get($paymentId, $apiContext);
			echo " x2 <br />\n";
		} catch (Exception $ex) {
			printResult("Get Payment Error", "Payment", null, null, $ex);
			echo " x3 <br />\n";
			exit(1);
		}
	} catch (Exception $ex) {
		echo " x4 <br />\n";
		echo "Executed Payment Error: Payment <br>\n";
		// yii::$app->controller->createLog(false, 'trex', var_export($ex,true));
		//echo substr(json_encode($ex),0,500) ;
		echo TVarDumper::dump($ex,1);
		exit(1);
	}
	echo " x5 <br /><b>FIN</b><br />\n";
	printResult("Get Payment", "Payment", $payment->getId(), null, $payment);

	//return $payment;
} else {
	echo " x6 <br />\n";
	printResult("User Cancelled the Approval", null);
	exit;
}


