<?php
use backend\models\clubs;
use backend\models\Discount;
use backend\models\MembershipStatus;
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

$Club_List = (new clubs)->getClubList(false,false,true);
if(empty($Club_List)) {
	$_SESSION['myFlash'] = 'error^Please add a Club First.';
	Yii::$app->response->redirect(['clubs/index'])->send(); return;
}

$Discounts = (new Discount)->getDiscounts('NewBG');
$Discounts_def = (new Discount)->getDiscountDefault();

$myPaymentList = backend\controllers\PaymentController::GetPaymentTypes($confParams);
?>

<div class="badges-form">
<?php $form = ActiveForm::begin([
	'id' => 'badgeCreate',
	'enableAjaxValidation' => true,
	'options' => [
		'data-banned-name-check-url' => Url::to(['/guest/check-banned-name']),
		'data-banned-name-first-name' => '#badges-first_name',
		'data-banned-name-last-name' => '#badges-last_name',
		'data-banned-name-status' => '#badge-banned-name-status',
		'data-banned-name-override' => '#badge-override-banned',
		'data-banned-name-decline-url' => '/',
	],
]); ?>
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
			<?php if ($model->isNewRecord) { ?>
				<div class="col-xs-12"><div id="badge-banned-name-status" class="help-block" role="status" aria-live="polite"></div></div>
			<?php } ?>
            <div class="col-xs-6 col-sm-2">
                <?= $form->field($model, 'suffix')->textInput(['autocomplete' => 'off','readonly'=> $model->isNewRecord ? false : true,]) ?>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-4">
                <?= $form->field($model, 'mem_type')->dropDownList($model->getMemberShipList(),['prompt'=>'select']).PHP_EOL; ?>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-4">
                <?= $form->field($model, 'club_id')->dropDownList($Club_List, ['prompt'=>'select']).PHP_EOL; ?>
            </div>
			<div  class="col-xs-12" id="primary-badge-summary">
			<div class="row">
			<div class="col-xs-6 col-sm-3" >
				<?= $form->field($model, 'primary')->textInput(['value'=>''])->label("Primary Family Member") ?>
			</div>
			<div class="col-xs-12 col-sm-9">
				<h4 class="text-center" id="no-primary-error" ><br> <p>Please Enter Primary Badge Holder</p> <br> </h4>
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
                <?=  $form->field($model, 'gender')->radioList([ 'm'=>'Male', 'f'=> 'Female'],['value'=>'m']) ?>
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

            <div class="col-xs-6 col-sm-6">
                <?= $form->field($model, 'incep')->textInput(['readonly' => true,'value'=>date('M d, Y h:i A',strtotime(yii::$app->controller->getNowTime()))]) ?>
            </div>

		<?php
			$DateChk = date("Y-".$confParams['sell_date'], strtotime(yii::$app->controller->getNowTime()));
			$nowDate = date('Y-m-d',strtotime(yii::$app->controller->getNowTime()));
			echo '<div class="col-xs-6 col-sm-2">';
			if ($DateChk <= $nowDate) {
				$nextExpire = date('Y-01-31', strtotime("+1 years",strtotime($nowDate)));
				$myYear=date('Y', strtotime($nextExpire.' - 1 year'));
				$BadgeYearList = json_decode(str_replace('}{',',',json_encode([$myYear=>$myYear,$myYear+1=>$myYear+1]) ));
				echo $form->field($model, 'badge_year')->dropDownList($BadgeYearList,['value'=>$myYear+1]). PHP_EOL;
			} else {
				$nextExpire = date('Y-01-31', strtotime("+1 years",strtotime($nowDate)));
				echo $form->field($model, 'badge_year')->textInput(['readonly' => true, 'value'=>date('Y',  strtotime($nextExpire.' - 1 year'))]). PHP_EOL;
			}
			echo '</div>';
		if(1==2) { //** section curently disabled ** Only enable for timed badges  ...  15yr .. ?>
            <div class="col-xs-6 col-sm-4">
                <?php $model->expires = date('M d, Y',strtotime($nextExpire)); ?>
                <?= $form->field($model, 'expires')->textInput(['readOnly'=>true]) ?>
                <input type="hidden" value='<?php echo date('M d, Y',strtotime($nextExpire)); ?>' id='defDate' />
            </div>
		<?php } ?>

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
<?php $memstatus = (new MembershipStatus)::getIssueMemStatus($confParams);
	if (count($memstatus)>1) { ?>	
			<div class="col-xs-6 col-sm-2">
                <?= $form->field($model, 'status')->dropDownList($memstatus) ?>
            </div>	
<?php } else { ?>
			<?= $form->field($model, 'status')->hiddenInput(['value'=>key($memstatus)])->label(false).PHP_EOL;?>
<?php } ?>	
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
					<?= $form->field($model, 'discounts')->dropDownList($Discounts,['value'=>$Discounts_def,'multiple'=>true,'size'=>2]).PHP_EOL; ?>
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
				<div  class="col-xs-6" >
					<div id="discount_amt_div"  style="display:none" > </div>
				</div>
				<div class="col-xs-6">
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
					'<td><input class="right" type="text" name="qty" size="3" value=0 onfocus="doCheckField()" onKeyUp="doCalcNew()" /></td>'.
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
      <?php if($model->amt_due <=0) $model->payment_method='cash';
			echo $form->field($model, 'payment_method')->dropDownList($myPaymentList,['prompt'=>'select']).PHP_EOL; ?>
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
                    <?= $form->field($model, 'sticker')->dropDownList((new backend\models\Stickers)->getlist(),['prompt'=>'Select Sticker']) ?>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>
<?php if ($model->isNewRecord) { ?>
	<?= Html::hiddenInput('override_banned', '0', ['id' => 'badge-override-banned']) ?>
<?php } ?>
<?php ActiveForm::end(); ?>
</div>
<?php $this->registerJsFile('@web/js/badge-form-logic.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php if ($model->isNewRecord) { ?>
	<?php $this->registerJsFile('@web/js/banned-name-check.js', ['depends' => [\yii\web\JqueryAsset::class]]); ?>
<?php } ?>
<script>
	$(".badge_store_div").click(function(e) {
        e.preventDefault();
		if($('#extras_store_div').is(':visible')) {
			$("#extras_store_div").hide();
        } else  {$("#extras_store_div").show();}
	});

	$("#badges-status").change(function(e) {
		var act_Status = document.getElementById('badges-status');
		const getPrefill = <?=(new MembershipStatus)::getPrefill(); ?> ;
		const stickerIndex = BadgeFormLogic.getPrefillStickerIndex(
			act_Status.value,
			getPrefill,
			document.getElementById("badges-sticker").options.length
		);
		if (stickerIndex !== null) {
			const dropdown = document.getElementById("badges-sticker");
			dropdown.selectedIndex = stickerIndex;
		}
	});

	function doCheckField(e) {
		e = e || window.event;
		e.target.value = BadgeFormLogic.normalizeZeroValue(e.target.value);
	};

	function doCalcNew() {
		var ContainerID = document.getElementById('store_items');
		var inputs = ContainerID.getElementsByTagName('input');
		var groupedInputs = { item: [], sku: [], ea: [], tax_rate: [], qty: [], price: [] };
		for (var i = 0; i < inputs.length; i++) {
			groupedInputs[inputs[i].name] = groupedInputs[inputs[i].name] || [];
			groupedInputs[inputs[i].name].push(inputs[i]);
		}
		var items = [];
		groupedInputs.ea.forEach(function (ea, index) {
			items.push({
				item: groupedInputs.item[index].value,
				sku: groupedInputs.sku[index].value,
				ea: ea.value,
				taxRate: groupedInputs.tax_rate[index].value,
				qty: groupedInputs.qty[index].value,
			});
		});
		var selectedDiscounts = Array.from(document.getElementById('badges-discounts').options)
			.filter(function (option) { return option.selected; })
			.map(function (option) { return option.value; });
		var result = BadgeFormLogic.calculateCart(
			items,
			$("#badges-badge_fee").val(),
			selectedDiscounts,
			false
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
			$("#badges-payment_method").val('cash');
		}
		$("#badges-tax").val(result.taxTotal.toFixed(2));
		$("#badges-amt_due-disp").val(result.amountDue.toFixed(2));
		$("#badges-amt_due").val(result.amountDue.toFixed(2));
	}

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
			document.getElementById('badges-cc_num').value = result.fields.cardNumber;
			document.getElementById('badges-cc_exp_mo').value = result.fields.expirationMonth;
			document.getElementById('badges-cc_exp_yr').value = result.fields.expirationYear;
		} else {
			SwipeError(cleanUPC, 'b_v_b_f:461');
		}
		cleanUPC = '';
	};
</script>