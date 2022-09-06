<?php

/* @var $this yii\web\View */
/* @var $model backend\models\Params */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Badge Renewal Help';
$this->params['breadcrumbs'][] = ['label' => 'Range Badges', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->badge_number, 'url' => ['/badges/view','badge_number'=>$model->badge_number]];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['badges/help']];

?>

<?=$this->render('_view-tab-menu',['model' => $model]).PHP_EOL ?>
<style>
li {
  line-height: 1.8;
}
</style>
<div class="row">
	<div class="col-12">
<h2>Renewing a Range Badge</h2>
<ul>
<li>Collect the range badge and current year club membership card from the badge holder.
<li>Confirm the range badge has a sticker issued within the last two years.
<li>Confirm the bar code on the back of the range badge does not end in a zero.
<li>Confirm the club membership is for the current year (a date must show on the club card).  A digital copy of a card or club membership confirmation is acceptable.
<hr />
<li>Sign in to the <u>AGC BADGE</u> App
<li>Click on <u>Range Badges</u>
<li>Type the <u>badge number</u> in the left column and press enter
<li>Click on the badge number needing renewal
<li>Click Update at the top left of the badge screen
<li>Confirm name, address, club, phone number, email address, and emergency contact and make any changes needed
<li>On the right side of the screen in the Badge Renewal fields confirm the amount due and select the payment type.
<li>If paying by credit, select Credit Card Now!  Swipe card through black card reader or manually enter card number.  Enter the 3-digit CVV number from the back of the credit card and click Process.
<li>If presenting a receipt showing on line payment, select On Line for the payment type.  The receipt can be a copy on their phone.  Record the on line order number in the remarks box with a note stating they paid for their renewal with an on line order.
<hr />
<li>SELECT the <u>STICKER number</u> being issued and place sticker on range badge.
<li>Click <u><mark>Renew Badge</mark></u> at bottom right of Badge Renewal Box on right side of screen.  This will save all the changes and additions you made in the badge screen and return you to the details page.
<li>On the details page confirm the expiration date is correct.  It should read January 31 of the following year.
<li>Return range badge, club card, and credit card to badge holder.
</ul>
<p> </p>
<h3>Special Circumstances</h3>
<ol>
<li>If a range badge bar code ends with a zero the badge will not work with our current computer system and should not be renewed as is.  These badge holders will need to renew during hours when they can have a new range badge printed to replace the old style card.
<li>If it has been more than two years since the badge has been renewed the system will require an Orientation Walk Through to be completed before the badge can be renewed.  The badge holder will need to present a signed and dated AGC Range and Safety Orientation Affidavit.
<ol>
<li>Confirm all lines are initialed and/or filled in and the form is signed and dated.
<li>Enter the WT date and WT Instructor in the appropriate boxes in the Badge Renewal section on the right side of the screen.
<li>Follow the rest of the regular renewal procedure.  Keep the Affidavit and drop it with your other paperwork at the end of your shift.</li>
</ol>
</ol>
<hr />
<p>** You can extend your Time (until Logout) by clicking on the clock in the upper right corner of the screen</p>
</div>
</div>
