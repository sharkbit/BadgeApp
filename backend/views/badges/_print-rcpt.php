<?php 
use backend\models\BadgeSubscriptions;

$subscript = BadgeSubscriptions::find()->where(['cc_x_id'=>$MyRcpt->id])->one();
?>

<div class="reciept-form">
<div class="row">
	<div class="col-xs-4">
		<img src="<?=yii::$app->params['rootUrl']?>/images/AGC_Logo.jpg" width="100px" >
	</div>
	<div class="col-xs-8">
		<p>11518 Marriottsville Rd,<br />
		Marriottsville, MD, 21104-1220<br />
		(410) 461-8532</p>
	</div>
</div>
<div class="row">
	<?php

	echo '<div class="col-xs-12"><hr />'."\n";

	echo '<table border=1 width="100%">'."\n<tr>".
	'<th width="50%">Item Name</th><th width="10%">Ea</th>'.
	'<th width="20%">Qty</th><th width="20%">Price</th></tr>'."\n";

	foreach(json_decode($MyRcpt->cart) as &$item ) {
		echo '<tr><td>'.$item->item.'</td><td align="right">'.number_format($item->ea,2).'</td><td align="right">'.$item->qty;
		echo '</td><td align="right">'.number_format($item->price,2)."</td></tr>\n";
	}
	echo "</table>\n";

	echo "</div>\n".'<div class="col-xs-12">';
	
	echo "\n<p align='right'>";
	if(isset($subscript->discount) && $subscript->discount > 0)
		{echo "Discount: ".number_format($subscript->discount,2)."</br>\n";}
	
	echo "<b>Tax: ".$MyRcpt->tax."</b></br /> \n";
	echo "<b>Total: ".$MyRcpt->amount."</b></p> <hr />\n";

	if (isset($subscript->transaction_type)) {
		if ($subscript->transaction_type <>'') {
			{echo "<P> Transaction Type: ".$subscript->transaction_type." Badge</p>\n";} } }

	if(strlen($MyRcpt->cardNum) >3 ) {
		echo "<p>Name: ".$MyRcpt->name."</p>\n";
		echo "<p>Credit Card: ".$MyRcpt->cardNum."<br />\n";
		echo "Type: ".$MyRcpt->cardType." &nbsp &nbsp Exp: ".$MyRcpt->expMonth."/".$MyRcpt->expYear."<br />\n";
		echo "Auth: ".$MyRcpt->authCode."<br />\n";
		echo "Status: Approved</p>\n";  // $MyRcpt->status
	} else {
		echo "<p>Paid By: ".$MyRcpt->tx_type."</p>\n";
	}
	echo "<p>Date: ".$MyRcpt->tx_date."</p>\n";
	echo "<p>Cashier: ".$MyRcpt->cashier."</p>";
	echo "<p><b>Thank You for your Purchase</b></p> \n";
	?>
</div>
</div>
</div>