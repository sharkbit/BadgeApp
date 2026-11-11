<?php

use backend\models\Badges;
use backend\models\clubs;
use backend\models\Discount;
use backend\models\MembershipStatus;
use backend\models\Params;
use backend\models\StoreItems;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\money\MaskMoney;
use kartik\widgets\DatePicker;
use kartik\widgets\DepDrop;
use kartik\widgets\DateTimePicker;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model backend\models\Badges */
/* @var $form yii\widgets\ActiveForm */

$MyYr = (int) substr(yii::$app->controller->getNowTime(),0,4) -8;
$YearList = '';
for ($x = 0; $x <= 90; $x++) {
	$nxtYr = $MyYr-$x;
	$YearList .=json_encode([$nxtYr=>$nxtYr]);
}
$YearList = json_decode(str_replace('}{',',',$YearList));

if(yii::$app->controller->hasPermission('badges/all')) {$restrict=false;} else {$restrict=true;}

$nowDate = date('Y-m-d',strtotime(yii::$app->controller->getNowTime()));
$DateChk = date("Y-".$confParams['sell_date'], strtotime(yii::$app->controller->getNowTime()));

	if ($DateChk <= $nowDate) {
		$nextExpire = date('Y-01-31', strtotime("+2 years",strtotime($nowDate)));
	} else {
		$nextExpire = date('Y-01-31', strtotime("+1 years",strtotime($nowDate)));
	}
	$DateExpires = $model->BadgeYear;
	$WtExpiresDate = $DateExpires + 2;
	$model->badge_year = $badge_year_chk = date('Y', strtotime($nextExpire.' - 1 year'));

    if((int)$WtExpiresDate < (int)$badge_year_chk) {
        $New_WT_Needed = true;
	} else { $New_WT_Needed = false; }

	$isExpired = Badges::isExpired($model->badge_number,$confParams);
	$mem_prefill = json_decode(MembershipStatus::getPrefill());

	// mem_type needs to renew? Test:
	$hide_Renew_message='';
	if ((int)$DateExpires < (int)$badge_year_chk) {									// Is BadgeSubscription Current?
		if (yii::$app->controller->hasPermission('badges/renew-membership')) {		// Can User Process & Renew Badges?
			if ((new MembershipStatus)->getCanRenew($model->status)) {									// Is badge active / not restricted?
				$mem_renew = backend\models\MembershipType::findOne(['id'=>$model->mem_type])->renew_yearly;
				if ($mem_renew) { // Member needs to renew
					$hide_Renew=false;

					if (in_array($model->status, $mem_prefill)) {
						$badgeSubscriptions->wt_instru = $model->wt_instru;
					}

				} else { $hide_Renew_message= "Membership Type dosn't need to Renew."; $hide_Renew=true; }
			} else { $hide_Renew_message= 'Account Status not allowed to Renew.'; $hide_Renew=true; }
		} else { $hide_Renew=true; }
	} else { $hide_Renew_message= 'Badge Subscription is current.'; $hide_Renew=true; }

	// Show Certifications?
	if (yii::$app->controller->hasPermission('badges/add-certification')) { $hide_Cert=false; } else { $hide_Cert=true; }
	if ((new MembershipStatus)->getCanRenew($model->status)) {} else { $hide_Cert=true; }
	if ($isExpired) { $hide_Cert=true; }

	$Discounts = (new Discount)->getDiscounts('RenBG');
	$Discounts_def = (new Discount)->getDiscountDefault();

	$myList = backend\controllers\PaymentController::GetPaymentTypes($confParams);
?>
<div class="badges-form">
<div class="row">
    <div class="col-xs-12 col-sm-8">
		<?php $form = ActiveForm::begin(['id'=>'badgeUpdate']); ?>
        <p class="help-block Top_space_block"></p>
 <?php if(yii::$app->controller->hasPermission('badges/barcode')) { ?>
        <div class="row">
			<div class="col-xs-12 col-sm-10">
			<p class="help-block Top_space_block"></p>
			</div>
		    <div class="col-xs-12 col-sm-2 pull-right">
				<?php if((substr($model->qrcode,-2) == " 0") || (strlen($model->qrcode)<14)) {
					echo Html::a('<i class="fa fa-refresh"> </i> Update Bar Code',['/badges/update?new=yep&badge_number='.$model->badge_number],['class' => 'btn btn-danger pull-right', 'id' => 'newNumber_btn']);
				} ?>
			<p class="help-block Top_space_qr_block"></p>
			</div>
         </div>
<?php } ?>

        <div class="row">
            <div class="col-xs-6 col-sm-3">
				<?= $form->field($model, 'prefix')->dropDownList(['Ms'=>'Ms','Miss'=>'Miss','Mrs'=>'Mrs','Mr'=>'Mr','Master'=>'Master','Fr'=>'Father (Fr)','Rev'=>'Reverend (Rev)','Dr'=>'Doctor (Dr)','Atty'=>'Attorney (Atty)','Hon'=>'Honorable (Hon)','Prof'=>'Professor (Prof)','Pres'=>'President (Pres)','VP'=>'Vice President (VP)','Gov'=>'Governor (Gov)','Ofc'=>'Officer (Ofc)'],['value'=>$model->prefix,'readonly'=> yii::$app->controller->hasPermission('badges/rename') ? false : true,]).PHP_EOL; ?>
            </div>
            <div class="col-xs-6 col-sm-3">
                <?= $form->field($model, 'first_name')->textInput(['readOnly'=>yii::$app->controller->hasPermission('badges/rename') ? false : true,]).PHP_EOL; ?>
            </div>
            <div class="col-xs-6 col-sm-3">
                <?= $form->field($model, 'last_name')->textInput(['readOnly'=>yii::$app->controller->hasPermission('badges/rename') ? false : true,]).PHP_EOL; ?>
            </div>
            <div class="col-xs-6 col-sm-3">
                <?= $form->field($model, 'suffix')->textInput(['readOnly'=>yii::$app->controller->hasPermission('badges/rename') ? false : true,]).PHP_EOL; ?>
            </div>
            <div class="col-xs-6 col-sm-6">
				<?= $form->field($model, 'mem_type')->dropDownList($model->getMemberShipList(),['disabled' =>($restrict)? true : false]).PHP_EOL; ?>
            </div>
            <div class="col-xs-12 col-sm-6">
				<?= $form->field($model, 'club_id')->dropDownList((new clubs)->getClubList(false,false,true),['value'=>(new clubs)->getMyClubs($model->badge_number),'multiple'=>true,'disabled'=>($restrict)? true : false]).PHP_EOL; ?>
            </div>
			<div  class="col-xs-12" id="primary-badge-summary">
				<div class="row">
					<div class="col-xs-6 col-sm-3" >
						<?php if($model->primary==null) { ?>

						  <?= $form->field($model, 'primary')->textInput(['value'=>'']).PHP_EOL; ?>

						<?php } else {?>
						 <?= $form->field($model, 'primary')->textInput(['readonly' => true ]).PHP_EOL; ?>
						<?php } ?>
					</div>
					<div class="col-xs-12 col-sm-9">
						<div>
							<h4 class="text-center" id="no-primary-error" style="display: none"> Sorry! could not find a user</h4>
							<div id="searchng-badge-animation" style="display: none">
								<img src="<?=yii::$app->params['rootUrl']?>/images/animation_processing.gif" style="width: 100px">Searching..</h4>
							</div>
						</div>
					</div>
				</div>
			</div>

            <div class="col-xs-12 col-sm-6">
                <?= $form->field($model, 'address')->textarea(['rows' => '1']).PHP_EOL; ?>
            </div>
            <div class="col-xs-6 col-sm-2">
                <?= $form->field($model, 'zip')->textInput([]).PHP_EOL; ?>
            </div>
            <div class="col-xs-6 col-sm-4">
                <?= $form->field($model, 'city')->textInput([]).PHP_EOL; ?>
            </div>

            <div class="col-xs-6 col-sm-2">
                <?= $form->field($model, 'state')->dropDownList(yii::$app->controller->getStates()) ?>
            </div>
            <div class="col-xs-6 col-sm-2">
                <?=  $form->field($model, 'gender')->radioList([ 'm'=>'Male', 'f'=> 'Female']).PHP_EOL; ?>
            </div>
            <div class="col-xs-6 col-sm-2">
                 <?= $form->field($model, 'yob')->dropDownList($YearList).PHP_EOL; //->textInput(['maxlength'=>true]).PHP_EOL; ?>
            </div>
            <div class="col-xs-10 col-sm-5">
                <?= $form->field($model, 'email')->textInput([]).PHP_EOL; ?>
            </div>
            <div class="col-xs-2 col-sm-1" ><br />
			<?php if($model->email_vrfy) {echo '<i class="fa fa-thumbs-up" title="Email Verified"></i>';} else {echo '<i class="fa fa-thumbs-down" title="Email Not Verified"></i>';}  ?>
            </div>

            <div class="col-xs-6 col-sm-6">
				<?php if ($model->phone) {$myPhone="(".substr($model->phone,0,3).") ".substr($model->phone,3,3)." - ".substr($model->phone,6,4);} else { $myPhone='';}
                echo $form->field($model, 'phone')->textInput(['maxlength'=>true,'value'=>$myPhone]); ?>
            </div>
            <div class="col-xs-6 col-sm-6">
				<?php if ($model->phone_op) {$myPhone_op="(".substr($model->phone_op,0,3).") ".substr($model->phone_op,3,3)." - ".substr($model->phone_op,6,4);} else {$myPhone_op='';}
				echo $form->field($model, 'phone_op')->textInput(['value'=>$myPhone_op]); ?>
            </div>

            <div class="col-xs-6 col-sm-6">
                <?= $form->field($model, 'ice_contact')->textInput([]).PHP_EOL; ?>
            </div>
            <div class="col-xs-6 col-sm-6">
				<?php if ($model->ice_phone) {$myice_phone="(".substr($model->ice_phone,0,3).") ".substr($model->ice_phone,3,3)." - ".substr($model->ice_phone,6,4);} else {$myice_phone='';}
                echo $form->field($model, 'ice_phone')->textInput(['value'=>$myice_phone]); ?>
            </div>

            <div class="col-xs-6 col-sm-6">
			<?php $model->incep = date('M d, Y h:i:s A',strtotime($model->incep)); ?>
            <?= $form->field($model, 'incep')->textInput(['disabled' => true,'value'=>date('M d, Y',strtotime($model->incep))]).PHP_EOL; ?>
            </div>

             <div class="col-xs-6 col-sm-4">
        <?php   $WTDate = strtotime($model->wt_date);
                $startDate = date('1999-01-01 12:00:00');
                if($WTDate < strtotime($startDate))  {
                    $model->wt_date =  date('M d, Y h:i:s A',strtotime($startDate));
                }

                $model->wt_date = date('M d, Y',strtotime($model->wt_date));

                echo $form->field($model, 'wt_date')->widget(DatePicker::classname(), [
					'options' => ['placeholder' => 'WT Date'],
					'type' => DatePicker::TYPE_INPUT,
					'disabled' => yii::$app->controller->hasPermission('badges/barcode') ? false : true,
					'pluginOptions' => [
						'format' => 'M dd, yyyy',
						'endDate' => date('M d, Y', strtotime("+90 days")),
						'autoclose'=>true,
						'convertFormat'=>true,
					]
				]); ?>
                </div>
             <div class="col-xs-6 col-sm-4">
                <?= $form->field($model, 'wt_instru')->textInput(['disabled' => yii::$app->controller->hasPermission('badges/barcode') ? false : true,]).PHP_EOL; ?>
            </div>
			<?php if(yii::$app->controller->hasPermission('badges/barcode')) {
			 echo '<div class="col-xs-6 col-sm-4">'.
			 $form->field($model, 'qrcode')->textInput(['readOnly'=>$model->qrcode==null ? false : true,'disabled' => true])->label('Barcode')."\n".
			"</div>\n";
			} else  {
				echo $form->field($model, 'qrcode')->hiddenInput(['readOnly'=>$model->qrcode==null ? false : true,'disabled' => true])->label(false),PHP_EOL;
			} ?>
        </div>
        <div class="row">
            <div class="col-xs-12">
            <?php	$remakrs_logs = json_decode($model->remarks,true);
                    if(!empty($remakrs_logs)) {
                        rsort($remakrs_logs);
                    } else {
                        $remakrs_logs = null;
                    }  ?>
            </div>
            <div class="col-xs-9 col-sm-9">
                <?= $form->field($model, 'remarks_temp')->textarea(['rows' => '2'])->label('Remarks').PHP_EOL; ?>
            </div>
            <div class="col-xs-3 col-sm-3">
		<?php if (($restrict) || ( in_array($model->status, $mem_prefill) )) {
               echo $form->field($model, 'status')->dropdownList((new MembershipStatus)->getMemberStatus(false,$model->status), ['disabled' => 'disabled']).PHP_EOL;
			} else {
				echo $form->field($model, 'status')->dropdownList((new MembershipStatus)->getMemberStatus(false,$model->status)).PHP_EOL;
			}?>
            </div>
            <div class="col-xs-12 col-sm-12">
                <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? 'Create' : '<i class="fa fa-pencil-square-o" aria-hidden="true"></i> Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary pull-right', 'name'=>'mem_update_btn','id' => 'mem_update_btn']).PHP_EOL; ?>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-xs-12">
                <h3> Remarks history </h3>
            </div>
            <div class="col-xs-12">
                <?=$this->render('_remarks',['remakrs_logs'=>$remakrs_logs])?>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
    </div>


<div class="col-xs-12 col-sm-4" >
<?php if(yii::$app->controller->hasPermission('badges/barcode')) { ?>
<div class="row">
	<style type="text/css">
		svg.barcode.pull-right {
		height: 105px;
		margin-top: 0px;
		margin-left: 0px;
		margin-right: 0px;
		}
	</style>
	<svg class="barcode pull-right"
		jsbarcode-value="<?=$model->qrcode?>"
		jsbarcode-textmargin="0"
		jsbarcode-format="CODE128">
	</svg>
	<script type="text/javascript">
		JsBarcode(".barcode").init();
	</script>
</div>

<?php } ?>
	<?php if ($hide_Renew) { ?>
	<div class="row">
	<h4><?=$hide_Renew_message ?></h4>
	</div>
	<?php } ?>
<div class="row" id="purchases_block" <?php if(!yii::$app->controller->hasPermission('badges/update')) {echo 'style="display:none"';} ?> >

	<div class="box" id="badge_renual_form" <?php if($hide_Renew) {echo 'style="display:none"';} ?> >
		<?php $form1 = ActiveForm::begin([
			'layout' => 'horizontal',
			'fieldConfig' => [
				'horizontalCssClasses' => [
					'label' => 'col-sm-6',
					'wrapper' => 'col-sm-6',
				],
			],
			'id' => 'form_badge_renew', 'enableClientValidation' => true, 'enableAjaxValidation' => false,
			'action' => ['badges/renew-membership','membership_id'=>$model->badge_number],
			'options' =>['enctype' => 'multipart/form-data']
		]); ?>
		<h3 class="text-center"> BADGE RENEWAL </h3>
		<div class="container" style="margin-top: -25px; margin-left: 15px">
		<?= $form1->field($badgeSubscriptions, 'total_credit')->hiddenInput(['value'=>$model->work_credits])->label(false).PHP_EOL; ?>
		<?= $form1->field($badgeSubscriptions, 'badge_number')->hiddenInput(['value'=>$model->badge_number,])->label(false).PHP_EOL;?>
		<?php echo Html::hiddenInput("item_name",'',['id'=>'badgesubscriptions-item_name']), PHP_EOL; ?>
		<?php echo Html::hiddenInput("item_sku",'',['id'=>'badgesubscriptions-item_sku']), PHP_EOL; ?>
		<?php echo Html::hiddenInput("isCurent",$hide_Renew,['id'=>'badgesubscriptions-isCurent','class'=>'form-control']), PHP_EOL; ?>
		<?php echo Html::hiddenInput("sell_date",$confParams['sell_date'],['id'=>'badges-sell_date']), PHP_EOL; ?>

		<?php $badge_year_minus = $badge_year_chk -1;
		$BadgeYearList = json_decode(str_replace('}{',',',json_encode([$badge_year_minus=>$badge_year_minus,$badge_year_chk=>$badge_year_chk]) ));
		echo $form1->field($badgeSubscriptions, 'badge_year')->dropDownList($BadgeYearList,['value'=>$badge_year_chk]).PHP_EOL; ?>

		<?= $form1->field($badgeSubscriptions, 'badge_fee')->textInput(['readOnly'=>true]).PHP_EOL; ?>
		<?= $form1->field($badgeSubscriptions, 'redeemable_credit')->textInput(['value'=>'']).PHP_EOL; ?>
		<?= $form1->field($badgeSubscriptions, 'discount')->dropDownList($Discounts,['value'=>$Discounts_def,'multiple'=>true,'size'=>2]).PHP_EOL; ?>
		<div  class="col-xs-6" >
			<div id="discount_amt_div"  style="display:none" > </div>
		</div>
		<div class="col-xs-6">
			<p class="pull-right"><a href="" class="badge_store_div" > Extras </a></p>
		</div>
		<div class="form-group" id="extras_store_div" style="display:none" > <!-- class="col-xs-12 col-sm-12"  -->
		<style type="text/css"> .right { text-align:right; } </style>
		<table id='store_items' border=1 width="100%">
		<tr><th>Item</th><th>Ea</th><th>Qty #</th><th>Price</th></tr>
<?php $ItemsList = StoreItems::find()->where(['like','type','inventory'])->andWhere(['active'=>1])->all();
		foreach($ItemsList as $item){
			echo '<tr><td><input type="hidden" name="item" value="'.htmlspecialchars($item['item']).'" />'.$item['item'].
				'<input type=hidden name="sku" value="'.$item['sku'].'" />'.
				'<input type=hidden name="tax_rate" value="'.$item['tax_rate'].'" /></td>'.
				'<td><input type="text" name="ea" size="3" value='.$item['price'].' disabled /></td>'.
				'<td><input class="right" type="text" name="qty" size="3" value=0 onfocus="doCheckField()" onKeyUp="doCalcUp()" /></td>'.
				'<td><input class="right" type="text" name="price" size="3" readonly /></td></tr>'."\n";
		} ?>
		</table>
		<input type="hidden" name="cart" id="cart" />
		</div>

		<?= $form->field($badgeSubscriptions, 'tax')->hiddenInput()->label(false).PHP_EOL;?>

		<?= $form1->field($badgeSubscriptions, 'amount_due')->textInput(['readOnly'=>true]).PHP_EOL; ?>

		<?php if($New_WT_Needed) { ?>
		   <?php if($badgeSubscriptions->wt_date=='') {$badgeSubscriptions->wt_date =  date('M d, Y',strtotime($nowDate)); }
			echo $form1->field($badgeSubscriptions, 'wt_date')->widget(DatePicker::classname(), [
						'options' => ['placeholder' => 'WT Date'],
						'type' => DatePicker::TYPE_INPUT,
						'pluginOptions' => [
						'format' => 'M dd, yyyy',
						'endDate' => date('M d, Y', strtotime("+90 days")),
						'autoclose'=>true,
						'convertFormat'=>true,
					]
				])->label('WT Date').PHP_EOL;;
			?>
				<?= $form1->field($badgeSubscriptions, 'wt_instru')->textInput([])->label('WT Instru').PHP_EOL; ?>
		<?php } ?>
		<?= $form1->field($badgeSubscriptions, 'payment_type')->dropdownList($myList,['prompt'=>'Payment Type']).PHP_EOL;?>

				<div id="cc_form_div" style="display:none; margin-left: 10px">
					<div class="col-xs-12 col-sm-12">
						<?= $form->field($badgeSubscriptions, 'cc_num')->textInput(['maxlength'=>true]).PHP_EOL; ?>
					</div>
					<div class="col-xs-4 col-sm-4">
						<?= $form->field($badgeSubscriptions, 'cc_exp_mo')->dropDownList(['01'=>'01 Jan','02'=>'02 Feb','03'=>'03 Mar','04'=>'04 Apr','05'=>'05 May','06'=>'06 Jun','07'=>'07 Jul','08'=>'08 Aug','09'=>'09 Sep','10'=>'10 Oct','11'=>'11 Nov','12'=>'12 Dec']).PHP_EOL; ?>
					</div>
					<div class="col-xs-5 col-sm-5">
<?php 	$curYr = date('Y',strtotime(yii::$app->controller->getNowTime()));
		$ccYear = range($curYr,$curYr+25);  ?>
						<?= $form->field($badgeSubscriptions, 'cc_exp_yr')->dropDownList($ccYear).PHP_EOL; ?>
					</div>
					<div class="col-xs-3 col-sm-3">
						<?= $form->field($badgeSubscriptions, 'cc_cvc')->textInput(['maxlength'=>true]).PHP_EOL; ?>
						<?= $form->field($badgeSubscriptions, 'cc_x_id')->hiddenInput()->label(false).PHP_EOL; ?>
					</div>
					<div class="col-xs-4 col-sm-4 form-group">
						<?= Html::Button('<i class="fa fa-credit-card"> Process</i>', ['id'=>'badgesubscriptions-Process_CC','class' => 'btn btn-danger']), PHP_EOL ?>
					</div>
					<div class="col-xs-8 col-sm-8">
						<p id="cc_info"> </p>
					</div>
				</div>
		<p> </P>
		<?= $form1->field($badgeSubscriptions, 'sticker')->dropDownList((new backend\models\Stickers)->getlist(),['prompt'=>'Select Sticker']) ?>

		<?php if(yii::$app->controller->hasPermission('badges/overideprice')) { ?><center>
			<?= $form1->field($badgeSubscriptions, 'amount_override')->checkbox().PHP_EOL; ?>
		<?php } ?>
			<div class="form-group">

				<?= Html::submitButton( '<i class="fa fa-refresh" aria-hidden="true"></i> RENEW BADGE', ['class' => 'btn btn-primary pull-right', 'id' => 'renew_btn']).PHP_EOL; ?>
			</div>
			</div>
			<div class="col-xs-12" id="online_search" style="display: none">
			<center><img src="<?=yii::$app->params['rootUrl']?>/images/animation_processing.gif" style="width: 50px" />Searching..</center>
			<p>  </p>
			</div>
			<div class="col-xs-12 text-center" id="online_search_results" > </div>
		</div>

		<div class="clearfix"> </div>
		<?php ActiveForm::end(); ?>

	<div class="box" id="badges-certifications-form" <?php if ($hide_Cert) {echo 'style="display:none"';} ?> >
		<?php $form2 = ActiveForm::begin([
			'layout' => 'horizontal',
			'fieldConfig' => [
				'horizontalCssClasses' => [
					'label' => 'col-sm-6',
					'wrapper' => 'col-sm-6',
				],
			],
		    'id' => 'form_badge_cert', //'enableClientValidation' => true, 'enableAjaxValidation' => false,
			'action' => ['badges/add-certification','membership_id'=>$model->badge_number],
			'options' =>['enctype' => 'multipart/form-data']
		]); ?>
		<h3 class="text-center"> CERTIFICATIONS </h3>
		<div class="container" style="margin-top: 25px; margin-left: 15px">
		<?= $form2->field($badgeCertification, 'proc_date')->textInput(['value'=>date('M d, Y',strtotime(yii::$app->controller->getNowTime()))])->label('Date').PHP_EOL; ?>
		<?= $form2->field($badgeCertification, 'certification_type')->dropdownList($badgeCertification->getcertificationList(),['prompt'=>'certification type']).PHP_EOL; ?>
		<?= $form1->field($badgeCertification, 'cert_amount_due')->textInput(['readOnly'=>true]).PHP_EOL; ?>

		<?= $form1->field($badgeCertification, 'cert_payment_type')->dropdownList($myList,['prompt'=>'Payment Type']).PHP_EOL;?>

		<div id="cert_cc_form_div" style="margin-left: 25px">
			<div class="col-xs-12 col-sm-12">
				<?= $form->field($badgeCertification, 'cc_num')->textInput(['maxlength'=>true]).PHP_EOL; ?>
			</div>
			<div class="col-xs-4 col-sm-4">
				<?= $form->field($badgeCertification, 'cc_exp_mo')->dropDownList(['01'=>'01 Jan','02'=>'02 Feb','03'=>'03 Mar','04'=>'04 Apr','05'=>'05 May','06'=>'06 Jun','07'=>'07 Jul','08'=>'08 Aug','09'=>'09 Sep','10'=>'10 Oct','11'=>'11 Nov','12'=>'12 Dec']).PHP_EOL; ?>
			</div>
			<div class="col-xs-5 col-sm-5">
<?php 	$curYr = date('Y',strtotime(yii::$app->controller->getNowTime()));
$ccYear = range($curYr,$curYr+25);  ?>
				<?= $form->field($badgeCertification, 'cc_exp_yr')->dropDownList($ccYear).PHP_EOL; ?>
			</div>
			<div class="col-xs-3 col-sm-3">
				<?= $form->field($badgeCertification, 'cc_cvc')->textInput(['maxlength'=>true]).PHP_EOL; ?>
				<?= $form->field($badgeCertification, 'cc_x_id')->hiddenInput()->label(false).PHP_EOL; ?>
			</div>
			<div class="col-xs-4 col-sm-4 form-group">
				<?= Html::Button('<i class="fa fa-credit-card"> Process</i>', ['id'=>'badgecertification-Process_CC','class' => 'btn btn-danger']), PHP_EOL ?>
			</div>
			<div class="col-xs-8 col-sm-8">
				<p id="cc_info"> </p>
			</div>
			<div class="col-xs-12" id="cert_online_search" style="display: none">
			<center><img src="<?=yii::$app->params['rootUrl']?>/images/animation_processing.gif" style="width: 50px" />Searching..</center>
			<p>  </p>
			</div>
		</div>
		<p> </P>

		<?= $form2->field($badgeCertification, 'sticker')->textInput(['maxlength'=>true]).PHP_EOL; ?>
		<?= $form2->field($badgeCertification, 'status')->hiddenInput(['value'=>'0'])->label(false).PHP_EOL; ?>

		<?= Html::submitButton( '<i class="fa fa-plus-square" aria-hidden="true"></i> ADD CERTIFICATION', ['class' => 'btn btn-primary pull-right','id'=>'cert_add']).PHP_EOL; ?>
		</div>
		<div class="row">
		<div class="col-xs-12" id="cert_search" ><!--style="display: none"> -->
			<center><img src="<?=yii::$app->params['rootUrl']?>/images/animation_processing.gif" style="width: 50px" />Searching..</center>
			<p>  </p>
		</div>
		<div class="col-xs-12 text-center" id="cert_search_results" > </div>
		</div>
		<div class="clearfix"> </div>
		<?php ActiveForm::end(); ?>
	</div>
</div>
</div>
</div>
</div>
</div>

	<?php $this->registerJsFile('@web/js/badge-form-logic.js', ['position' => \yii\web\View::POS_HEAD]); ?>
	<?php if($New_WT_Needed) { ?>
	<script>
    $(document).ready(function() {
        $('<div id="error_msg" class="err_date error_msg" style="margin-top: 5px;margin-bottom: 10px;color: #a94442;">WT Date cannot be blank.</div>').insertAfter("#badgesubscriptions-wt_date");
        $('<div id="error_msg" class="err_ins error_msg" style="margin-top: 5px;margin-bottom: 10px;color: #a94442;">WT Instru cannot be blank.</div>').insertAfter("#badgesubscriptions-wt_instru");
        $(".error_msg").hide();

        $("#renew_btn").click(function(e) {
			var validation = BadgeFormLogic.validateRenewalFields(
				$("#badgesubscriptions-wt_date").val(),
				$("#badgesubscriptions-wt_instru").val()
			);
            if(validation.missingDate) {
                $(".err_date").show();
                e.preventDefault();
            } else {
                $(".err_date").hide();
            }
            if(validation.missingInstructor) {
                $(".err_ins").show();
                e.preventDefault();
            } else {
                $(".err_ins").hide();
            }
        });
    });
</script>
<?php } ?>
<script>
	$("#cert_search").hide();
	$("#online_search").hide();
	$("#cert_cc_form_div").hide();

    $("#badges-club_id").select2({placeholder_text_multiple:'Choose Clubs',width: "100%"});

	$(".badge_store_div").click(function(e) {
        e.preventDefault();
		if($('#extras_store_div').is(':visible')) {
			$("#extras_store_div").hide();
        } else  {$("#extras_store_div").show();}
	});

	$("#badges-status").change(function(e) {
		var act_Status = document.getElementById('badges-status');
		const getPrefill = <?=(new MembershipStatus)::getPrefill(); ?> ;
		const dropdown = document.getElementById("badgesubscriptions-sticker");
		const stickerIndex = BadgeFormLogic.getPrefillStickerIndex(
			act_Status.value,
			getPrefill,
			dropdown.options.length
		);
		if (stickerIndex !== null) {
			dropdown.selectedIndex = stickerIndex;
		}
	});

	$("#badgesubscriptions-amount_override").change(function(e) {
		var isEditable = BadgeFormLogic.isAmountOverrideEditable(
			document.getElementById("badgesubscriptions-amount_override").checked
		);
		if (isEditable) {
			document.getElementById("badgesubscriptions-amount_due").readOnly = false;
			console.log('editable');
		} else {
			document.getElementById("badgesubscriptions-amount_due").readOnly = true;
			console.log('locked');
		}
	});

	function doCheckField(e) {
		e = e || window.event;
		e.target.value = BadgeFormLogic.normalizeZeroValue(e.target.value);
	};

	function doCalcUp(){
		var ContainerID = document.getElementById('store_items');
		var inputs = ContainerID.getElementsByTagName('input');
		var groupedInputs = { item: [], sku: [], ea: [], tax_rate: [], qty: [], price: [] };
		for (var i = 0; i < inputs.length; i++) {
			groupedInputs[inputs[i].name] = groupedInputs[inputs[i].name] || [];
			groupedInputs[inputs[i].name].push(inputs[i]);
		}
		var items = groupedInputs.ea.map(function (ea, index) {
			return {
				item: groupedInputs.item[index].value,
				sku: groupedInputs.sku[index].value,
				ea: ea.value,
				taxRate: groupedInputs.tax_rate[index].value,
				qty: groupedInputs.qty[index].value,
			};
		});
		var selectedDiscounts = Array.from(document.getElementById('badgesubscriptions-discount').options)
			.filter(function (option) { return option.selected; })
			.map(function (option) { return option.value; });
		var result = BadgeFormLogic.calculateCart(
			items,
			$("#badgesubscriptions-badge_fee").val(),
			selectedDiscounts,
			true
		);
		groupedInputs.price.forEach(function (input, index) {
			input.value = result.prices[index] || '';
		});
		$("#cart").val(JSON.stringify(result.cart));

		const myDivDiscount = document.getElementById("discount_amt_div");
		if (result.discount > 0) {
			$("#discount_amt_div").show();
			myDivDiscount.textContent = 'Discount: ' + result.discount;
		} else {
			myDivDiscount.textContent = '';
			$("#discount_amt_div").hide();
		}

		if (result.setCash) {
			$("#badgesubscriptions-payment_type").val('cash');
		}
		$("#badgesubscriptions-tax").val(result.taxTotal.toFixed(2));
		$("#badgesubscriptions-amount_due-disp").val(result.amountDue.toFixed(2));
		$("#badgesubscriptions-amount_due").val(result.amountDue.toFixed(2));
	}

	$("#badgecertification-certification_type").change(function(e) {
		document.getElementById("cert_add").disabled = true;
		$("#cert_search").show();
		var myCert = document.getElementById("badgecertification-certification_type");
		var selectedIndex = myCert.options[myCert.selectedIndex];
		document.getElementById("badgecertification-cert_amount_due").value = selectedIndex.value.split("|")[1];

		var testId = BadgeFormLogic.getCertificationTestId(selectedIndex.text);
		if (!testId) {
			$("#cert_search").hide();
			$("#cert_search_results").html("No Test Data");
			document.getElementById("badgecertification-cert_amount_due").value = '';
			document.getElementById("cert_add").disabled = false;
			return;
		}
		var myEmail = $("#badges-email").val();
		var wp_site = "<?=yii::$app->params['wp_site']?>";
		if (BadgeFormLogic.canSearchCertification(testId, myEmail, wp_site)) {
			var myUrl = '<?=yii::$app->params['wp_site']?>/comms.php?email='+myEmail+'&id='+testId;
			console.log('url: '+myUrl);
			jQuery.ajax({
				method: 'GET',
				url: myUrl,
				success: function(responseData, textStatus, jqXHR) {
					var obj = JSON.parse( responseData );
					if(obj.status == 'success') {
						var entry ="";
						var myData ='<table border-width=1><tr><th>Test Date</th><th>Score</th><th>Badge</th></tr>';
						obj.data.forEach(function(entry) {
							var d = new Date(entry.testdate);
							myData += "<tr><td>"+ d.toLocaleString('en-US') + "</td><td>" +
										entry.score + "</td><td>" +
										entry.badge+"</td></tr>\n";
						});
						myData +="</table><p> </p>";
						$("#cert_search_results").html(myData);


					} else {
						var myData = 'No Results Found';
		//				console.log('no data');
					}
		//			console.log(myData);
					$("#cert_search_results").html(myData);
					$("#cert_search").hide();
					document.getElementById("cert_add").disabled = false;
				},
				error: function (responseData, textStatus, errorThrown) {
					$("#cert_search_results").html("Error, Something went Wrong!");
					console.log("error " + JSON.stringify(responseData));
					$("#cert_search").hide();
					document.getElementById("cert_add").disabled = false;
				},
			});
		} else {
			$("#cert_search_results").html("Requires a Valid Email to Check Tests!");
			$("#cert_search").hide();
			document.getElementById("cert_add").disabled = false;
		};
	});

	$("#badgecertification-cert_payment_type").change(function(e) {
        var pay_meth = document.getElementById("badgecertification-cert_payment_type");
        var selectedVal = pay_meth.options[pay_meth.selectedIndex].value;
		var visibility = BadgeFormLogic.getPaymentVisibility(selectedVal);
        if (visibility.showCardForm) {
            $("#cert_cc_form_div").show();
            $("#cert_add").hide();
			$("#cert_online_search").hide();
        } else if(visibility.showOnlineSearch) {
			//CheckCertOnline();
            $("#cert_cc_form_div").hide();
            $("#cert_add").show();
			$("#cert_online_search").show();
		} else {
            $("#cert_cc_form_div").hide();
            $("#cert_add").show();
			$("#cert_online_search").hide();
        }
    });

	$("#badgecertification-Process_CC").click(function(e) {
		e.preventDefault();
		$("#badgecertification-Process_CC").hide();
		document.getElementById("badgecertification-Process_CC").disabled=true;
		$("p#cc_info").html("Processing...");

		var formDataB = $("#badgeUpdate,#form_badge_cert,#form_badge_renew").serializeArray();
		jQuery.ajax({
			method: 'POST',
			crossDomain: false,
			data: formDataB,
			dataType: 'json',
			url: '<?=yii::$app->params['rootUrl']?>/payment/charge',
			success: function(responseData, textStatus, jqXHR) {
				if(responseData.status=="success") {
					console.log("success " + JSON.stringify(responseData));
					if(responseData.message.status=="CAPTURED") {
						$("p#cc_info").html( "Card Captured, Auth Code: "+ responseData.message.authCode);
						$("#badgecertification-cc_x_id").val(responseData.message.id);
						$("#badgecertification-Process_CC").hide();
						$("#cert_add").show();
					} else {
						$("p#cc_info").html( "Card: "+ responseData.message);
						$("#badgecertification-Process_CC").show();
					}
				} else {
					console.log("Data error " + JSON.stringify(responseData));
					SwipeError(JSON.stringify(responseData),'b_v_b_u:670');
					$("p#cc_info").html(responseData.message);
					$("#badgecertification-Process_CC").show();
				}

			},
			error: function (responseData, textStatus, errorThrown) {
				$("p#cc_info").html("PHP error:<br>"+responseData.responseText);
				SwipeError(JSON.stringify(responseData),'b_v_b_u:678');
				console.log("error "+ JSON.stringify(responseData));
				$("#badgecertification-Process_CC").show();
			},
		});
		document.getElementById("badgecertification-Process_CC").disabled=false;
	});

	function ProcessSwipe(cleanUPC) {
		var result = BadgeFormLogic.parseSwipe(cleanUPC, new Date().getFullYear());
		if (result && result.type === 'license') {
			var fields = {
				firstName: 'badges-first_name',
				lastName: 'badges-last_name',
				yob: 'badges-yob',
				address: 'badges-address',
				city: 'badges-city',
				state: 'badges-state',
				zip: 'badges-zip',
			};
			Object.keys(result.fields).forEach(function (key) {
				document.getElementById(fields[key]).value = result.fields[key];
			});
		} else if (result && result.type === 'card') {
			document.getElementById('badgesubscriptions-cc_num').value = result.fields.cardNumber;
			document.getElementById('badgesubscriptions-cc_exp_mo').value = result.fields.expirationMonth;
			document.getElementById('badgesubscriptions-cc_exp_yr').value = result.fields.expirationYear;
		} else {
			SwipeError(cleanUPC, 'b_v_b_u:843');
		}
		cleanUPC = '';
	};

	$("#form_badge_cert").on('submit', function(e,messages){
		var f = document.forms.badgeUpdate;
		var postData = $("#badgeUpdate,#form_badge_cert,#form_badge_renew").serializeArray();
		var xhr = new XMLHttpRequest();
		xhr.open("POST", '/badges/update?badge_number=<?=$model->badge_number?>', true);
		xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhr.send(postData.join("&"));
	});
</script>

<style>
.wrap {
    min-height: 100%;
    height: auto;
    margin: 0 auto -60px;
    padding: 0 0 0px !important;
}
table {
    margin: 0 auto;
  }
td, th {
	padding: 3px;
	font-size:12px;
    border: 1px solid black;
}
</style>
