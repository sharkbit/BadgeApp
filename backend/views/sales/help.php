<?php

/* @var $this yii\web\View */
/* @var $model backend\models\Params */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Sales Store Help';
$this->params['breadcrumbs'][] = ['label' => 'Store', 'url' => ['sales/index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['sales/help']];
?>

<?=$this->render('_view-tab-menu').PHP_EOL ?>
<style>
li {
  line-height: 1.8;
}
</style>
<div class="container">
<div class="row">
	<div class="col-12 ">
		<h2>Selling Items in the Store</h2>
		<h3>(not a RANGE BADGE) That is a different transaction</h3>
		<ul>
			<li>Every transaction you make on your shift needs to be completed in the computer as it takes place.</li>
			<li>Sign in to the AGC App using your badge.</li>
			<li>Click on Store</li>
			<li>In the top left corner enter the badge number of the customer and then click anywhere else on the screen.  This will pull up the badge holder’s information.</li>
			<li>If the customer is a guest click on the For a Guest button on the top left of the screen. Enter the guest’s information if paying with a Credit Card.</li>
			<li>If paying with cash click the Paying with Cash checkbox. This will autofill the info required</li>
			<li>Find the item and enter the quantity for each item being sold in the chart on the right side of the screen.  This will automatically fill in the price column to the right as well as the Total box in the center of your screen.</li>
			<li>Choose a payment method.</li>
			<li>If paying by credit select Credit Card Now! </li>
			<ul>
				<li>Swipe card through black card reader or manually enter card number.</li>
				<li>Enter the 3 digit CVV number from the back of the credit card and</li>
				<li>click Process.</li>
			</ul>
			<li>If previously paid online select On Line as the payment method.</li>
			<li>Click Purchase to complete the sale.</li>
		</ul>
		<hr />
<p>** You can extend your Time (until Logout) by clicking on the clock in the upper right corner of the screen</p>

	</div>
</div>
</div>