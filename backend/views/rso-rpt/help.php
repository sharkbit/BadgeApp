<?php

/* @var $this yii\web\View */
/* @var $model backend\models\Params */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'RSO Report Help';
$this->params['breadcrumbs'][] = ['label' => 'RSO Reports', 'url' => ['rso-rpt/index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['rso-rpt/help']];

?>

<?=$this->render('_view-tab-menu').PHP_EOL ?>
<style>
li {
  line-height: 1.8;
}
</style>
<div class="row">
	<div class="col-12 col-md-6">
<h2> RSO REPORT  </h2>
<ul>
<li>Only <u>ONE SHIFT</u> can be signed in at a time</li>
<li>Complete your <u>SHIFT</u> report and Click <u>FINALIZE</u> the RSO report before the next shift starts. We have an overlap in SHIFTS so work it out best you can.</li>
<li>Sign in and start your <u>RSO REPORT</U> immediately when your shift starts.  This needs to happen so all the work items listed below will be generated into your report as they happen. </li>
<li>Use the drop-down list in the <u>ACTIVE REPORT</u> page under <u>ALL RSO's</u> to sign in.</li>
<li>You can add multiple RSO's in a SHIFT</li>
<li>All RSO's will be listed in the DROPDOWN list.</li>
<li>The <u>DATE</u> is AUTOMATICALLY generated. </li>
<li>Fill out the basic info on the <u>ACTIVE REPORT</u> page:<br />
SHIFT, RSO'S, Opening notes or Anomalies, Wristband color, MIC status,<br />
WOBBLE CASES, CASH at beginning of shift.</li>
<li>During the day or towards end of shift fill out PARTICIPATION</li>
<li>Type in any <u>Closing Notes</u> </li>
<li>Click <u>SAVE</u> on the report anytime to save what you have entered during the SHIFT. If you do not it will clear your work and you will have to start all over.</li>
<li><mark>IMPORTANT:</mark> You MUST Click <u>FINALIZE</u> when you have completed your SHIFT. </li>
<br />
<mark>ALL transactions should be completed in the computer as <u>they take place!</u></mark>  There is no need to save transactions for later entry into the computer.  </li>
<hr />
</ul>

<h2>RANGE VIOLATIONS</h2>
<ul>
<li>Fill out <u>RANGE VIOLATIONS</u></li>
<li>The <u>RANGE VIOLATION</u> data will automatically be imported into the <u>RSO REPORT</u>  </li>
<hr />
</ul>

<h2>RANGE BADGE STICKERS</h2>
<ul>
<li>When you process RANGE BADGE RENEWALs the RSO REPORT will automatically generate a record of the STICKERS used.</li>
<li>View this in the STICKER TAB. It shows STICKER NUMBERS (used and remaining) also STICKER NUMBERS plus BADGE NUMBER assigned</li>
<li>The Office Manager will manage the sticker numbers on the report and push new stickers to the RSO's report when required. If they do not match, please report to the Office Manager.</li>
<hr />
</ul>

</div>
<div class="col-12 col-md-6">
 
<h2>BADGE APP STORE SALES by RSO</h2>
<ul>
<li>RSO REPORT will record all <u>Cash Sales</u></li>
<li>RSO REPORT will record all <u>Check Sales</u></li>
<li><u>Credit Card Sales</u> are no longer required in the RSO REPORT</li>
<li>When paying with a Credit Card all the purchaser’s information will need to be entered into the store and match the credit card they are using. </li>
<br />
NOTE: All <u>cash and check sales</u> completed in the STORE will automatically be imported into the RSO REPORT. Credit Card transactions will not be imported into the RSO REPORT.</li>
<hr />
</ul>

<h2>BADGE HOLDER CHECKING IN A GUEST</h2>
<ul>
<li>Badge Holders checking in a guest can only fully complete the transaction with a Credit Card. <u><i>Cash or Check payments “MUST” be completed by the RSO</i></u> after the guest has been registered.
</ul><ol>
<li>Badge Holder approaches RSO to pay cash or check for guest check-in.</li>
<li>RSO will open the <u>GUEST CHECKOUT</u></li>
<li>The Badge Holders transaction will be listed with PayNow cash shown in blue. Click on it. </li>
<li>Complete the sale in the store when it comes up.</li>
<li>Collect the cash or check.</li>
<hr />
</ol>

<h2>BADGE HOLDER PURCHASES on BADGE APP</h2>
<ul>
<li>Badge Holders making purchases in the <u>BADGE APP STORE</u> can only pay with Credit Card. <u><i>Cash or Check payments “MUST” be completed by the RSO.</i><u></li>
<hr />
</ul>

<h2>AGCRANGE.ORG ONLINE SALES</h2>
<ul>
<li>When a Badge Holder presents an <u>ONLINE</u> purchase receipt, enter the sale the same way as any other sale in the <u>STORE</u> (see above)</li>
<li>Click <u>ONLINE</u> under Payment method.</li>
<li>Verify the PURCHASE. (Receipt or transaction number)</li>
<li>All Credit Card Sales will not be added to the RSO REPORT. </li>
<hr />
</ul>

<h2>GUEST SALES “check box” </h2>
<h3>(Selling an item to a guest using the store)</h3>
<ul>
<li>In the STORE there is a checkbox <u>For A Guest?</u></li>
<li>If paying CASH or CHECK Click <u>Paying Cash</u>, it will Generate a badge number <u>99999</u> and fill in all the boxes.</li>
<li>After filling in the quantities to be purchased, Click <u>Cash or Check</u> under the payment options</li>
<li>Collect the cash or check. give change if necessary. The sale will automatically generate into your report.</li>
<li>If Guest is paying with a Credit Card (<u>Do not click “Paying Cash or Check”</u>) all the purchaser’s information will still need to be entered and match the credit card they are using. </li>
<li>Credit Card transactions will not be added to the RSO REPORT </li>
</ul>
<hr />
<p>** You can extend your Time (until Logout) by clicking on the clock in the upper right corner of the screen</p>
</div>
</div>
