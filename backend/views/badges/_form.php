<?php
use backend\models\clubs;
use backend\models\Params;
use backend\models\StoreItems;
use kartik\money\MaskMoney;
use kartik\widgets\DatePicker;
use kartik\widgets\DepDrop;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Badges */
/* @var $form yii\widgets\ActiveForm */

$MyYr = (int) substr(yii::$app->controller->getNowTime(),0,4) -8;
$YearList = '';
for ($x = 1; $x <= 90; $x++) {
	$nxtYr = $MyYr-$x;
	$YearList .=json_encode([$nxtYr=>$nxtYr]);
}
$YearList = json_decode(str_replace('}{',',',$YearList));

$confParams  = Params::findOne('1');
?>

<div class="badges-form">
<?php $form = ActiveForm::begin(['id'=>'badgeCreate','enableAjaxValidation' => true]); ?>
<div class="row">

    <div class="col-xs-12 col-sm-8">
		<div class="row">

            <div class="col-xs-6 col-sm-2">
                <?= $form->field($model, 'badge_number')->textInput(['readonly'=>true, 'value' =>str_pad($model->badge_number, 5, '0', STR_PAD_LEFT) ]) ?>
            </div>
			<div class="help-block"></div>
  
            <div class="col-xs-6 col-sm-2">
				<?= $form->field($model, 'prefix')->dropDownList(['Mr'=>'Mr','Ms'=>'Ms','Miss'=>'Miss','Mrs'=>'Mrs','Master'=>'Master','Fr'=>'Father (Fr)','Rev'=>'Reverend (Rev)','Dr'=>'Doctor (Dr)','Atty'=>'Attorney (Atty)','Hon'=>'Honorable (Hon)','Prof'=>'Professor (Prof)','Pres'=>'President (Pres)','VP'=>'Vice President (VP)','Gov'=>'Governor (Gov)','Ofc'=>'Officer (Ofc)'],['readonly'=> $model->isNewRecord ? false : true,]) ?>
            </div>
            <div class="col-xs-6 col-sm-4">
                <?= $form->field($model, 'first_name')->textInput(['autocomplete' => 'off','readonly'=> $model->isNewRecord ? false : true,'placeholder' => 'First M.']) ?>
            </div>
            <div class="col-xs-6 col-sm-4">
                <?= $form->field($model, 'last_name')->textInput(['autocomplete' => 'off','readonly'=> $model->isNewRecord ? false : true,'placeholder' => 'Last']) ?>
            </div>
            <div class="col-xs-6 col-sm-2">
                <?= $form->field($model, 'suffix')->textInput(['autocomplete' => 'off','readonly'=> $model->isNewRecord ? false : true,]) ?>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-4">
                <?= $form->field($model, 'mem_type')->dropDownList($model->getMemberShipList(),['prompt'=>'select']).PHP_EOL; ?>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-4">
                <?= $form->field($model, 'club_id')->dropDownList((new clubs)->getClubList(false,false), ['prompt'=>'select']).PHP_EOL; ?>
            </div>
			<div  class="col-xs-12" id="primary-badge-summary">
			<div class="row">
			<div class="col-xs-6 col-sm-3" >
				<?= $form->field($model, 'primary')->textInput(['value'=>''])->label("Primary Family Member") ?>
			</div>
			<div class="col-xs-12 col-sm-9">
				<h4 class="text-center" id="no-primary-error" ><br> <p>Please Enter Primary badge Holder</p> <br> </h4>
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
                <?= $form->field($model, 'zip')->textInput([]) ?>
            </div>
            <div class="col-xs-6 col-sm-4">
                <?= $form->field($model, 'city')->textInput(['autocomplete' => 'off','readonly'=> $model->isNewRecord ? false : true,]) ?>
            </div>

            <div class="col-xs-6 col-sm-2">
                <?= $form->field($model, 'state')->dropDownList(yii::$app->controller->getStates(),['value'=>'MD']) ?>
            </div>
            <div class="col-xs-6 col-sm-2">
                <?=  $form->field($model, 'gender')->radioList([ '0'=>'Male', '1'=> 'Female'],['value'=>0]) ?>
            </div>
            <div class="col-xs-6 col-sm-2">
                <?= $form->field($model, 'yob')->dropDownList($YearList,['value'=>$MyYr-13 ]) ?>
            </div>
            <div class="col-xs-12 col-sm-6">
                <?= $form->field($model, 'email', ['enableAjaxValidation' => true])->textInput(['autocomplete' => 'off','class'=>'form-control','placeholder'=>'Optional']) ?>
            </div>

            <div class="col-xs-6 col-sm-6">
                <?= $form->field($model, 'phone')->textInput(['autocomplete' => 'off','maxlength'=>true,'readonly'=> $model->isNewRecord ? false : true,]) ?>
            </div>
            <div class="col-xs-6 col-sm-6">
                <?= $form->field($model, 'phone_op')->textInput(['autocomplete' => 'off','maxlength'=>true,'readonly'=> $model->isNewRecord ? false : true,'placeholder'=>'Optional']) ?>
            </div>

            <div class="col-xs-6 col-sm-6">
                <?= $form->field($model, 'ice_contact')->textInput(['autocomplete' => 'off','readonly'=> $model->isNewRecord ? false : true,]) ?>
            </div>
            <div class="col-xs-6 col-sm-6">
                <?= $form->field($model, 'ice_phone')->textInput(['autocomplete' => 'off','maxlength'=>true,'readonly'=> $model->isNewRecord ? false : true,]) ?>
            </div>

            <?php
				$DateChk = date("Y-".$confParams['sell_date'], strtotime(yii::$app->controller->getNowTime()));
				$nowDate = date('Y-m-d',strtotime(yii::$app->controller->getNowTime()));
                if ($DateChk <= $nowDate) {
				    $nextExpire = date('Y-01-31', strtotime("+2 years",strtotime($nowDate)));
                } else {
                    $nextExpire = date('Y-01-31', strtotime("+1 years",strtotime($nowDate)));
                }
            ?>
            <div class="col-xs-6 col-sm-6">
                <?= $form->field($model, 'incep')->textInput(['readonly' => true,'value'=>date('M d, Y h:i A',strtotime(yii::$app->controller->getNowTime()))]) ?>
            </div>
            <div class="col-xs-6 col-sm-4">
                <?php $model->expires = date('M d, Y',strtotime($nextExpire)); ?>
                <?= $form->field($model, 'expires')->textInput(['readOnly'=>true]) ?>
                <input type="hidden" value='<?php echo date('M d, Y',strtotime($nextExpire)); ?>' id='defDate' />
            </div>
             <div class="col-xs-6 col-sm-4">
                <?php $model->wt_date = date('M d, Y',strtotime(yii::$app->controller->getNowTime())) ?>
                <?= $form->field($model, 'wt_date')->widget(DatePicker::classname(), [
                                'options' => ['placeholder' => 'WT Date'],
                                'type' => DatePicker::TYPE_INPUT,
                                'pluginOptions' => [
                                'format' => 'M dd, yyyy',
                                'endDate' => date('M d, Y', strtotime("+90 days")),
                                'autoclose'=>true,
                                'convertFormat'=>true,
                            ]
                ]); ?>

            </div>
             <div class="col-xs-6 col-sm-4">
                <?= $form->field($model, 'wt_instru')->textInput(['placeholder'=>'Required']) ?>
            </div>
             <div class="col-xs-6 col-sm-4">
                <?= $form->field($model, 'qrcode')->textInput(['readOnly'=>true])->label('Barcode') ?>
            </div>
            <div class="col-xs-12 col-sm-12">
                <?= $form->field($model, 'remarks')->textarea(['rows' => '4']) ?>
			</div>
		</div>
		<div class="row">
		<div class="col-xs-3" id="HideMySubmit">
			<?= Html::submitButton($model->isNewRecord ? '<i class="fa fa-save"> </i> SAVE' : 'Update', ['id'=>'badges_submin_btn','class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		</div>
		<div class="col-sm-3">
			<?= Html::a('<i class="fa fa-eraser"> </i> Clear',['/badges/create'],['class' => 'btn btn-danger']) ?>
		</div>
		</div>
        <div class="row">
            <div class="col-xs-12 col-sm-3" ng-if="membershipType=='2'">
			</div>
		</div>
		<p> </p>
	</div>
    <div class="col-xs-12 col-sm-4">
        <div class="row">
            <div class="col-xs-4  pull-right">
<style type="text/css">
       svg.barcode.pull-right {
    height: 103px;
    margin-top: -68px;
    margin-right: -28px;
}
   </style>
                <svg class="barcode pull-right"
                    jsbarcode-value=""
                    jsbarcode-textmargin="0"
					jsbarcode-format="CODE128">
                </svg>
                <script type="text/javascript">
                    JsBarcode(".barcode").init();
                </script>
            </div>
        </div>
        <div class="row">
            <div class="summary-block-payment box">
                <div class="col-xs-6 col-sm-12">
                    <?= $form->field($model, 'badge_fee')->textInput(['readOnly'=>true,'class'=>'form-control Money']); ?>
                </div>
				<div class="col-xs-6 col-sm-12">
					<?= $form->field($model, 'discounts')->dropDownList(['n:0'=>'None','s:10'=>'Student'],['value'=>'n:0','multiple'=>true,'size'=>2]).PHP_EOL; ?>
                </div>
				<div id="div_friend_block" style="display:none" >
                <div class="col-xs-6 col-sm-12" >
					<?php echo Html::checkbox('badges-FriendHelp' ,'',['id'=>'badges-FriendHelp']), PHP_EOL; ?> Friend's help?
					<div class="help-block"></div>
				</div>
				<div class="col-xs-12 col-sm-12" id="badges-firbadiv" style="display:none" >
					<?php echo Html::label("Friend's Badge"), PHP_EOL; ?>

					<?php echo Html::hiddenInput("item_name",'',['id'=>'badges-item_name']), PHP_EOL; ?>
					<?php echo Html::hiddenInput("item_sku",'',['id'=>'badges-item_sku']), PHP_EOL; ?>
					<?php echo Html::hiddenInput("FriendCredits",'',['id'=>'badges-FriendCredits']), PHP_EOL; ?>
					<?php echo Html::textinput("FriendBadge" ,'',['class'=>"form-control",'id'=>'badges-FriendBadge']), PHP_EOL; ?>
					<p id="badges-FrendStatus"> </p>
					<div class="help-block" ></div>
				</div>
				</div>
				<div class="col-xs-6 col-sm-12">
					<p class="pull-right"><a href="" class="badge_store_div" > Extras </a></p>
				</div>
					<div  class="form-group" id="extras_store_div" style="display:none" > <!--   -->
					<style type="text/css"> .right { text-align:right; } </style>
					<table id='store_items' border=1 width="100%">
					<tr><th>Item</th><th>Ea</th><th>Qty #</th><th>Price</th></tr>
	<?php $ItemsList = StoreItems::find()->where(['like','type','inventory'])->andWhere(['active'=>1])->all();
			foreach($ItemsList as $item){
				echo '<tr><td><input type="hidden" name="item" value="'.htmlspecialchars($item['item']).'" />'.$item['item'].
					'<input type=hidden name="sku" value="'.$item['sku'].'" />'.
					'<input type=hidden name="tax_rate" value="'.$item['tax_rate'].'" /></td>'.
					'<td><input type="text" name="ea" size="3" value='.$item['price'].' disabled /></td>'.
					'<td><input class="right" type="text" name="qty" size="3" value=0 onKeyUp="doCalcNew()" /></td>'.
					'<td><input class="right" type="text" name="price" size="3" readonly /></td></tr>'."\n";
			} ?>
					</table>
					<input type="hidden" name="cart" id="cart" >
					</div>
				
                <div class="col-xs-12 col-sm-12">
                    <?= $form->field($model, 'amt_due')->widget(MaskMoney::classname(), [
                        'pluginOptions' => ['allowNegative' => false,]]); ?>
					<?= $form->field($model, 'tax')->hiddenInput()->label(false).PHP_EOL;?>
                </div>
                <div class="col-xs-12 col-sm-12">
      <?php if(yii::$app->controller->hasPermission('payment/charge') && (strlen($confParams->conv_p_pin)>2 || strlen($confParams->conv_d_pin)>2))  {
				if($confParams->qb_env == 'prod') {
					$myList=['cash'=>'Cash','check'=>'Check','credit'=>'Credit Card','creditnow'=>'Credit Card Now!','online'=>'Online','other'=>'Other'];
				} else { $myList=['cash'=>'Cash','check'=>'Check','credit'=>'Credit Card','creditnow'=>'TEST CC (Do not use)','online'=>'Online','other'=>'Other']; }

			} else {
				$myList=['cash'=>'Cash','check'=>'Check','credit'=>'Credit Card','online'=>'Online','other'=>'Other'];
			}
			echo $form->field($model, 'payment_method')->dropDownList($myList,['prompt'=>'select']).PHP_EOL; ?>
                </div>

				<div id="cc_form_div" style="display:none;">
					<div class="col-xs-12 col-sm-12">
						<?= $form->field($model, 'cc_num')->textInput(['maxlength'=>true]) ?>
					</div>
					<div class="col-xs-4 col-sm-4">
						<?= $form->field($model, 'cc_exp_mo')->dropDownList(['01'=>'01 Jan','02'=>'02 Feb','03'=>'03 Mar','04'=>'04 Apr','05'=>'05 May','06'=>'06 Jun','07'=>'07 Jul','08'=>'08 Aug','09'=>'09 Sep','10'=>'10 Oct','11'=>'11 Nov','12'=>'12 Dec'],['style'=>'padding:2px; min-width: 20px;']) ?>
					</div>
					<div class="col-xs-5 col-sm-5">
<?php 	$curYr = date('Y',strtotime(yii::$app->controller->getNowTime()));
		$ccYear = range($curYr,$curYr+25);  ?>
						<?= $form->field($model, 'cc_exp_yr')->dropDownList($ccYear,['style'=>'padding:2px;']) ?>
					</div>
					<div class="col-xs-3 col-sm-3">
						<?= $form->field($model, 'cc_cvc')->textInput(['maxlength'=>true,'style'=>'padding:2px;']) ?>
						<?= $form->field($model, 'cc_x_id')->hiddenInput()->label(false) ?>
					</div>
					<div class="col-xs-4 col-sm-4 form-group">
						<?= Html::Button('<i class="fa fa-credit-card"> Process</i>', ['id'=>'badges-Process_CC','class' => 'btn btn-danger']), PHP_EOL ?>
					</div>
					<div class="col-xs-8 col-sm-8">
						<p id="cc_info"> </p>
					</div>
				</div>
				<div class="col-xs-12 col-sm-12">
                    <?= $form->field($model, 'sticker')->textInput(['maxlength'=>true]) ?>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
</div>
<script>
	$(".badge_store_div").click(function(e) {
        e.preventDefault();
		if($('#extras_store_div').is(':visible')) {
			$("#extras_store_div").hide();
        } else  {$("#extras_store_div").show();}
	});

	function CheckOnline() {
		// Only For Renuals!
	}

	function doCalcNew() {
		var ContainerID = document.getElementById('store_items');

		var arrItem = new Array();
		var arrSku = new Array();
		var arrEa = new Array();
		var arrQty = new Array();
		var arrTax = new Array();
		var arrPrice = new Array();
		var cart =  new Array();
		var ItemTotal = 0; var TaxTotal = 0; var TotalTotal = 0;
		var ContainerIDElements = new Array( 'input');
		//var ContainerIDElements = new Array('input', 'textarea', 'select');

		for( var i = 0; i < ContainerIDElements.length; i++ ){
			els = ContainerID.getElementsByTagName( ContainerIDElements[i] );
			for( var j = 0; j < els.length; j++ ){
				if(els[j].name == 'item') arrItem.push(els[j]);
				if(els[j].name == 'sku') arrSku.push(els[j]);
				if(els[j].name == 'ea') arrEa.push(els[j]);
				if(els[j].name == 'tax_rate') arrTax.push(els[j]);
				if(els[j].name == 'qty') arrQty.push(els[j]);
				if(els[j].name == 'price') arrPrice.push(els[j]);
			}
		}

		for( var j = 0; j < arrEa.length; j++ ) {
			if(Number(arrQty[j].value)>0) {
				var itto = parseFloat(Math.round((Number(arrEa[j].value) * Number(arrQty[j].value)) * 100) / 100).toFixed(2);
				var tato = parseFloat(Math.round((Number(arrEa[j].value) * Number(arrQty[j].value) * Number(arrTax[j].value)) * 100) / 100).toFixed(2);
				arrPrice[j].value = parseFloat(Number(itto) + Number(tato)).toFixed(2);
				ItemTotal += Number(itto);
				TaxTotal += Number(tato);
				TotalTotal += Number(arrPrice[j].value);
				var item = { "item":arrItem[j].value, "sku":arrSku[j].value, "ea":arrEa[j].value, "qty":arrQty[j].value, "price":arrPrice[j].value };
				cart.push(item);
			} else { arrPrice[j].value=null; }
		}
		$("#cart").val(JSON.stringify(cart));

		var badgeFee = parseInt($("#badges-badge_fee").val());
		var discount = 0;   
        for (var option of document.getElementById('badges-discounts').options)	{
			if (option.selected) {
				if(option.value.length > 2){
					var d_opt=option.value.split(":")
					discount +=d_opt[1];
				}
			}
		}
		var amountDue = badgeFee - discount;
		if(amountDue<0) {
			amountDue = 0.00;
		}
		amountDue = amountDue + TotalTotal;
		$("#badges-tax").val(TaxTotal.toFixed(2));
		$("#badges-amt_due-disp").val(parseFloat(Math.round(amountDue * 100) / 100).toFixed(2));
		$("#badges-amt_due").val(parseFloat(Math.round(amountDue * 100) / 100).toFixed(2));
		console.log('Badge Total: '+badgeFee+ '; Tax: '+ TaxTotal+ '; Grand total: '+ amountDue);
	}

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

			document.getElementById("badges-cc_num").value = ccNum;
			document.getElementById("badges-cc_exp_mo").value = ExpMo;
			document.getElementById("badges-cc_exp_yr").value = ExpYr;
		} else { SwipeError(cleanUPC,'b-v-b-f:443'); }
		cleanUPC = '';
	};
</script>