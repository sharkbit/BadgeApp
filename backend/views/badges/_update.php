<?php

use backend\models\Badges;
use backend\models\clubs;
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

$confParams  = Params::findOne('1');
if(yii::$app->controller->hasPermission('badges/all')) {$restrict=false;} else {$restrict=true;}

$nowDate = date('Y-m-d',strtotime(yii::$app->controller->getNowTime()));
$DateChk = date("Y-".$confParams['sell_date'], strtotime(yii::$app->controller->getNowTime()));

	if ($DateChk <= $nowDate) {
		$nextExpire = date('Y-01-31', strtotime("+2 years",strtotime($nowDate)));
	} else {
		$nextExpire = date('Y-01-31', strtotime("+1 years",strtotime($nowDate)));
	}

    $DateExpires = date('Y-m-d',strtotime($model->expires));
	$WtExpiresDate = date('Y-01-31', strtotime("-2 years",strtotime($nowDate)));

    if($DateExpires <= $WtExpiresDate) {
        $New_WT_Needed = true;
	} else { $New_WT_Needed = false; }

	if ($DateExpires >= $nextExpire){ // Show Renew?
		$hide_Renew=true;
	} else {
		if ($model->status=='approved' || $model->status=='pending' ) {
			$hide_Renew=false; } else { $hide_Renew=true; }
	}

	// Show Certifications?
	if (yii::$app->controller->hasPermission('badges/add-certification')) { $hide_Cert=false; } else { $hide_Cert=true; }
	if ($model->status=='approved' || $model->status=='pending') {} else { $hide_Cert=true; }
	if ($nowDate > $DateExpires ) { $hide_Cert=true; }
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
	<?php 	if($restrict) {
				echo $form->field($model, 'mem_type')->textInput(['value'=>$model->getMembershipType($model->mem_type),'disabled' => true] ).PHP_EOL;
			} else {
				echo $form->field($model, 'mem_type')->dropDownList($model->getMemberShipList(),['options'=>['approved'=>['title'=>'blaa blaa'],
			'pending'=>['title'=>'blaa blaa'],
			'prob'=>['title'=>'blaa blaa'],
			'suspended'=>['title'=>'blaa blaa'],
			'revoked'=>['title'=>'blaa blaa'],
			'retired'=>['title'=>'blaa blaa']
			]]).PHP_EOL;
			}  ?>
            </div>
            <div class="col-xs-12 col-sm-6">
	<?php 	if($restrict) {
				$ClubName=(new clubs)->getClubList();
		        echo $form->field($model, 'club_name')->textInput(['value'=>$ClubName[$model->club_id],'disabled'=>true]).PHP_EOL;
			} else { ?>

				<div class="form-group" >
				<?php echo Html::label("Club Name"); ?>
				<?php echo Html::dropDownList("new_club",(new clubs)->getMyClubs($model->badge_number) ,(new clubs)->getClubList(false,false,true),['id'=>'badges-clubs', 'class'=>"chosen_select", 'multiple'=>true, 'size'=>false]), PHP_EOL; ?>
				</div>
			<?php } ?>
            </div>
        <div  id="primary-badge-summary">
            <div class="col-xs-3">
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
                <?= $form->field($model, 'state')->textInput([]).PHP_EOL; ?>
            </div>
            <div class="col-xs-6 col-sm-2">
                <?=  $form->field($model, 'gender')->radioList([ '0'=>'Male', '1'=> 'Female']).PHP_EOL; ?>
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
            <div class="col-xs-6 col-sm-6">
                <?php $model->expires = date('M d, Y',strtotime($model->expires)); ?>
                <?= $form->field($model, 'expires')->textInput(['readOnly'=>true]).PHP_EOL; ?>
                <input type="hidden" value='<?php echo date('M d, Y',strtotime($nextExpire)); ?>' id='defDate' />
            </div>

			<?php if(yii::$app->controller->hasPermission('badges/barcode')) {
			 echo '<div class="col-xs-6 col-sm-4">'.
			 $form->field($model, 'qrcode')->textInput(['readOnly'=>$model->qrcode==null ? false : true,'disabled' => true])->label('Barcode')."\n".
			"</div>\n";
			} else  {
				echo $form->field($model, 'qrcode')->hiddenInput(['readOnly'=>$model->qrcode==null ? false : true,'disabled' => true])->label(false),PHP_EOL;
			}
			?>

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
					'disabled' => yii::$app->controller->hasPermission('badges/delete') ? false : true,
					'pluginOptions' => [
						'format' => 'M dd, yyyy',
						'endDate' => date('M d, Y', strtotime("+90 days")),
						'autoclose'=>true,
						'convertFormat'=>true,
					]
				]); ?>
                </div>
             <div class="col-xs-6 col-sm-4">
                <?= $form->field($model, 'wt_instru')->textInput(['disabled' => yii::$app->controller->hasPermission('badges/delete') ? false : true,]).PHP_EOL; ?>
            </div>
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
		<?php if ($restrict) {
                echo $form->field($model, 'status')->textInput(['readonly' => true]).PHP_EOL;
			} else {
				echo $form->field($model, 'status')->dropdownList((new Badges)->getMemberStatus()).PHP_EOL;
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
		<?php echo Html::hiddenInput("isCurent",$hide_Renew,['id'=>'badgesubscriptions-isCurent','class'=>'form-control']), PHP_EOL; ?>
		<?php echo Html::hiddenInput("sell_date",$confParams['sell_date'],['id'=>'badges-sell_date']), PHP_EOL; ?>

		<?php $badgeSubscriptions->expires = date('M d, Y',strtotime($nextExpire)); ?>
		<?= $form1->field($badgeSubscriptions, 'expires')->textInput(['readOnly'=>true]).PHP_EOL; ?>

		<?= $form1->field($badgeSubscriptions, 'badge_fee')->textInput(['readOnly'=>true]).PHP_EOL; ?>
		<?= $form1->field($badgeSubscriptions, 'redeemable_credit')->textInput(['value'=>'']).PHP_EOL; ?>
		<?= $form1->field($badgeSubscriptions, 'discount')->textInput([]).PHP_EOL; ?>
		<div class="col-xs-12">
		<p class="pull-right"><a href="" class="badge_store_div" > Extras </a></p></div>
		<div class="form-group" id="extras_store_div" style="display:none" > <!-- class="col-xs-12 col-sm-12"  -->
		<table id='store_items' border=1 width="100%">
		<tr><th>Item</th><th>Ea</th><th>Qty #</th><th>Price</th></tr>
<?php $ItemsList = StoreItems::find()->where(['like','type','inventory'])->all();
		foreach($ItemsList as $item){
			echo '<tr><td><input type="hidden" name="item" value="'.$item['item'].'" />'.$item['item'].
				'<input type=hidden name="sku" value="'.$item['sku'].'" /></td>'.
				'<td><input type="text" name="ea" size="3" value='.$item['price'].' disabled /></td>'.
				'<td><input type="text" name="qty" size="3" value=0 onKeyUp="doCalcUp()" /></td>'.
				'<td><input type="text" name="price" size="3" readonly /></td></tr>'."\n";
		} ?>
		</table>
		<input type="hidden" name="cart" id="cart" />
		</div>
		

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
		<?php }
			if(yii::$app->controller->hasPermission('payment/charge') && (strlen($confParams->qb_token)>2 || strlen($confParams->qb_oa2_refresh_token)>2))  {
				if($confParams->qb_env == 'prod') {
					$myList=['cash'=>'Cash','check'=>'Check','credit'=>'Credit Card','creditnow'=>'Credit Card Now!','online'=>'Online','other'=>'Other'];
				} else { $myList=['cash'=>'Cash','check'=>'Check','credit'=>'Credit Card','creditnow'=>'TEST CC (Do not use)','online'=>'Online','other'=>'Other']; }
			} else {
				$myList=['cash'=>'Cash','check'=>'Check','credit'=>'Credit Card','online'=>'Online','other'=>'Other'];
			}
		?>
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
		<?= $form1->field($badgeSubscriptions, 'sticker')->textInput([]).PHP_EOL; ?>
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

<?php if($New_WT_Needed) { ?>
<script>
    $(document).ready(function() {
        $('<div id="error_msg" class="err_date error_msg" style="margin-top: 5px;margin-bottom: 10px;color: #a94442;">WT Date cannot be blank.</div>').insertAfter("#badgesubscriptions-wt_date");
        $('<div id="error_msg" class="err_ins error_msg" style="margin-top: 5px;margin-bottom: 10px;color: #a94442;">WT Instru cannot be blank.</div>').insertAfter("#badgesubscriptions-wt_instru");
        $(".error_msg").hide();

        $("#renew_btn").click(function(e) {
            if($("#badgesubscriptions-wt_date").val() == '') {
                $(".err_date").show();
                e.preventDefault();
            } else {
                $(".err_date").hide();
            }
            if($("#badgesubscriptions-wt_instru").val() == '') {
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
	

	$(".badge_store_div").click(function(e) {
        e.preventDefault();
		if($('#extras_store_div').is(':visible')) {
			$("#extras_store_div").hide();
        } else  {$("#extras_store_div").show();}
	});

	function doCalcUp(){
		var ContainerID = document.getElementById('store_items');

		var arrItem = new Array();
		var arrSku = new Array();
		var arrEa = new Array();
		var arrQty = new Array();
		var arrPrice = new Array();
		var cart =  new Array();
		var ItemTotal = 0;
		var ContainerIDElements = new Array( 'input');
		//var ContainerIDElements = new Array('input', 'textarea', 'select');

		for( var i = 0; i < ContainerIDElements.length; i++ ){
			els = ContainerID.getElementsByTagName( ContainerIDElements[i] );
			for( var j = 0; j < els.length; j++ ){
				if(els[j].name == 'item') arrItem.push(els[j]);
				if(els[j].name == 'sku') arrSku.push(els[j]);
				if(els[j].name == 'ea') arrEa.push(els[j]);
				if(els[j].name == 'qty') arrQty.push(els[j]);
				if(els[j].name == 'price') arrPrice.push(els[j]);
			}
		}

		for( var j = 0; j < arrEa.length; j++ ) {
			if(Number(arrQty[j].value)>0) {
				arrPrice[j].value = parseFloat(Math.round(Number(arrEa[j].value) * Number(arrQty[j].value) * 100) / 100).toFixed(2);
				ItemTotal += Number(arrPrice[j].value);
				var item = { "item":arrItem[j].value, "sku":arrSku[j].value, "ea":arrEa[j].value, "qty":arrQty[j].value, "price":arrPrice[j].value };
				cart.push(item);
			} else { arrPrice[j].value=null; }
		}
		$("#cart").val(JSON.stringify(cart));

		var badgeFee = $("#badgesubscriptions-badge_fee").val();
		var discount = $("#badgesubscriptions-discount").val();
		var amountDue = badgeFee - discount;
		if(amountDue<0) {
			amountDue = 0.00;
		}
		amountDue = amountDue + ItemTotal;
		$("#badgesubscriptions-amount_due-disp").val(parseFloat(Math.round(amountDue * 100) / 100).toFixed(2));
		$("#badgesubscriptions-amount_due").val(parseFloat(Math.round(amountDue * 100) / 100).toFixed(2));
	//	console.log('Badge Total: '+badgeFee+ '; Grand total: '+ amountDue);
	}

	$("#badgecertification-certification_type").change(function(e) {
		document.getElementById("cert_add").disabled = true;
		$("#cert_search").show();
		var myCert = document.getElementById("badgecertification-certification_type");
		var selectedText = myCert.options[myCert.selectedIndex].text;
		var myEmail = $("#badges-email").val();
		if(selectedText=='Steel') {
			var testId=3;
			document.getElementById("badgecertification-cert_amount_due").value = '10.00';
		}else if(selectedText=='Holster') {
			var testId=1;
			document.getElementById("badgecertification-cert_amount_due").value = '20.00';
		} else {
	//		console.log('not Steel or Holster');
			$("#cert_search").hide();
			$("#cert_search_results").html("No Test Data");
			document.getElementById("badgecertification-cert_amount_due").value = '';
			document.getElementById("cert_add").disabled = false;
			return;
		}
		if(myEmail) {
			var myUrl = 'https://agcrange.org/comms.php?email='+myEmail+'&id='+testId;
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
							var d = new Date(entry.testdate*1000);
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
console.log('main:631: here');			
        var pay_meth = document.getElementById("badgecertification-cert_payment_type");
        var selectedVal = pay_meth.options[pay_meth.selectedIndex].value;
        if(selectedVal=="creditnow") {
            $("#cert_cc_form_div").show();
            $("#cert_add").hide();
			$("#cert_online_search").hide();
        } else if(selectedVal=="online") {
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
console.log('main:651: here');
		e.preventDefault();
		document.getElementById("badgecertification-Process_CC").disabled=true;
		$("p#cc_info").html("Processing...");

		var formDataB = $("#badgeUpdate,#form_badge_cert").serializeArray();
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
					}
				} else {
					console.log("Data error " + JSON.stringify(responseData));
					SwipeError(JSON.stringify(responseData),'b-v-l-m:788');
					$("p#cc_info").html(responseData.message);
				}

			},
			error: function (responseData, textStatus, errorThrown) {
				$("p#cc_info").html("PHP error:<br>"+responseData.responseText);
				SwipeError(JSON.stringify(responseData),'b-v-l-m:795');
				console.log("error "+ JSON.stringify(responseData));
			},
		});
		document.getElementById("badgecertification-Process_CC").disabled=false;
	});
		
	function CheckOnline() {
		document.getElementById("renew_btn").disabled = true;
		var myBadge = $("#badgesubscriptions-badge_number").val();
		var myLast = $("#badges-last_name").val();
		if(myBadge) {
			var myUrl = 'https://agcrange.org/comms.php?online='+myBadge;
			console.log('url: '+myUrl);
			jQuery.ajax({
				method: 'GET',
				url: myUrl,
				success: function(responseData, textStatus, jqXHR) {
					console.log(responseData);
					var obj = JSON.parse( responseData );
					if(obj.status == 'success') {
						var entry ="";
						var myData ='<table border-width=1><tr><th>Date</th><th>Name Found</th><th>Total</th></tr>';
						obj.data.forEach(function(entry) {
							if (myLast.toUpperCase() === entry.l_name.toUpperCase()) {
								myData += "<tr><td>"+ entry.tx_date.split(" ")[0] + "</td><td>" + entry.f_name +" "+ entry.l_name +  "</td><td>" +entry.total+"</td></tr>\n";
							}
						});
						myData +="</table><p> </p>";
						$("#online_search_results").html(myData);


					} else {
						var myData = 'No Results Found';
		//				console.log('no data');
					}
		//			console.log(myData);
					$("#online_search_results").html(myData);
					$("#online_search").hide();
					document.getElementById("renew_btn").disabled = false;
				},
				error: function (responseData, textStatus, errorThrown) {
					$("#online_search_results").html("Error, Something went Wrong!");
					console.log("error " + JSON.stringify(responseData));
					$("#online_search").hide();
					document.getElementById("renew_btn").disabled = false;
				},
			});
		} else {
			$("#online_search_results").html("Requires a Valid Email to Check Tests!");
			$("#online_search").hide();
			document.getElementById("renew_btn").disabled = false;
		};
	};

	function ProcessSwipe(cleanUPC) {
		if  ((cleanUPC.indexOf('ANSI 6360') > 0) || (cleanUPC.indexOf('AAMVA6360') > 0)) { // Matched Drivers Licence
			console.log('Drivers Licence Scanned: ', cleanUPC);
			var FName=false;

			if (cleanUPC.indexOf('DAC') > 0) {  //Parse Name
			  var fsName = cleanUPC.indexOf('DAC')+3;
			  var feName = cleanUPC.indexOf("ArrowDown",fsName);
			  var FName = cleanUPC.slice(fsName,feName);
			  FName = titleCase(FName);
			  var msName = cleanUPC.indexOf('DAD')+3;
			  var meName = cleanUPC.indexOf("ArrowDown",msName);
			  var MName = cleanUPC.slice(msName,meName);
			  MName = MName.charAt(0).toUpperCase();
			  var lsName = cleanUPC.indexOf('DCS')+3;
			  var leName = cleanUPC.indexOf("ArrowDown",lsName);
			  var LName = cleanUPC.slice(lsName,leName);
			  LName = titleCase(LName);
			} //Parse Name Second Try
			else if  (cleanUPC.indexOf('DAA') > 0) {
			  var nsName = cleanUPC.indexOf('DAA')+3;
			  var neName = cleanUPC.indexOf("ArrowDown",nsName);
			  var FullName = cleanUPC.slice(nsName,neName);
			  FullName = FullName.split(",");
			  var LName = titleCase(FullName[0]);
			  var FName = titleCase(FullName[1]);
			  var MName = FullName[2].charAt(0).toUpperCase();
			}
            console.log("Full Name: "+FName+' - '+MName+' - '+LName);
			document.getElementById("badges-first_name").value = FName+' '+MName;
			document.getElementById("badges-last_name").value = LName;

			if (cleanUPC.indexOf('DBB') > 0) {  //Parse Date of Birth
			  var fDOB = cleanUPC.indexOf('DBB')+3;
			  var lDOB = cleanUPC.indexOf("ArrowDown",fDOB);
			  var DOB = cleanUPC.slice(fDOB,lDOB);

			  var DOBtest=true;
			  var DOBy=DOB.substring(0,4);
			  var DOBm=DOB.substring(4,6);
			  var DOBd=DOB.substring(6);

			  if (DOBm > 12) {DOBtest=false;}
			  if (DOBd > 31) {DOBtest=false;}
			  if (DOBy < 1900) {DOBtest=false;}
			  if (!DOBtest) {
			    var DOBy=DOB.substring(4);
			    var DOBm=DOB.substring(0,2);
			    var DOBd=DOB.substring(2,4);
			  }
              console.log("DOB: m "+DOBm+" d "+DOBd+" y "+DOBy);
			  document.getElementById("badges-yob").value = DOBy;
			}

			if (cleanUPC.indexOf('DAG') > 0) {  //Parse Address
			  var fAddr = cleanUPC.indexOf('DAG')+3;
			  var lAddr = cleanUPC.indexOf("ArrowDown",fAddr);
			  var Addr = cleanUPC.slice(fAddr,lAddr);
			  Addr = titleCase(Addr);
              console.log("Addr: "+Addr);
			  document.getElementById("badges-address").value = Addr;
			}

			if (cleanUPC.indexOf('DAI') > 0) {  //Parse City
			  var fCty = cleanUPC.indexOf('DAI')+3;
			  var lCty = cleanUPC.indexOf("ArrowDown",fCty);
			  var Cty = cleanUPC.slice(fCty,lCty);
			  Cty = titleCase(Cty);
              console.log("City: "+Cty);
			  document.getElementById("badges-city").value = Cty;
			}

			if (cleanUPC.indexOf('DAJ') > 0) {  //Parse State
			  var fST = cleanUPC.indexOf('DAJ')+3;
			  var lST = cleanUPC.indexOf("ArrowDown",fST);
			  var Stat = cleanUPC.slice(fST,lST);
              console.log("State: "+Stat);
			  document.getElementById("badges-state").value = Stat;
            }

			if (cleanUPC.indexOf('DAK') > 0) {  //Parse ZIP
			  var fZIP = cleanUPC.indexOf('DAK')+3;
			  var lZIP = cleanUPC.indexOf("ArrowDown",fZIP);
			  var ZIP = cleanUPC.slice(fZIP,lZIP);
			  ZIP = ZIP.substring(0,5);
              console.log("ZIP: "+ZIP);
			  document.getElementById("badges-zip").value = ZIP;
			}

		}
		else if (cleanUPC.match(/B\d{16}/g)) {  // Matched Credit Card!
			console.log('Credit Card Scanned: ', cleanUPC);
			var ccNum = cleanUPC.substring(1,17);
			var fExp = cleanUPC.indexOf('^')+1;
			var fExp = cleanUPC.indexOf('^',fExp)+1;
			var ExpYr = cleanUPC.substring(fExp,fExp+2);
			var ExpMo = cleanUPC.substring(fExp+2,fExp+4);
			ccNum = ccFormat(ccNum);
			console.log("Num: "+ccNum+" Exp Yr: "+ExpYr+" Exp Mo: "+ExpMo);
			ExpYr = ExpYr - (new Date().getFullYear().toString().substr(-2));

			document.getElementById("badgesubscriptions-cc_num").value = ccNum;
			document.getElementById("badgesubscriptions-cc_exp_mo").value = ExpMo;
			document.getElementById("badgesubscriptions-cc_exp_yr").value = ExpYr;
		} else { SwipeError(cleanUPC,'b-v-g-u:686'); }
		cleanUPC = '';
	};
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
