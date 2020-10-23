<?php
use backend\models\Params;

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\Guest */
/* @var $form yii\widgets\ActiveForm */

if($_SESSION["badge_number"]>0) {$model->badge_number=$_SESSION["badge_number"];}

$model->time_in = yii::$app->controller->getNowTime();
$Payment_block=''; $msg='Register';
if ( isset( $_SESSION['stickyForm'] ) ) {
	$model->guest_count = $_SESSION['stickyForm']['guest_count'];
	$model->badge_number = $_SESSION['stickyForm']['badge_number'];
	$model->payment_type = $_SESSION['stickyForm']['payment_type'];
	if($_SESSION['stickyForm']['g_paid']) {$model->g_paid = $_SESSION['stickyForm']['g_paid'];}
	else {$model->g_paid='0';}
	$model->time_in = $_SESSION['stickyForm']['time_in'];
	if ($model->guest_count > 1) {$msg='Add Next Guest';}
	$Payment_block='display: none;';
}

if(!yii::$app->controller->hasPermission('guest/modify')) {
	$isguest = true;
} else {
	if(!empty($stickyGuest)) {
		$model->badge_number = $stickyGuest['badge_number'];
	}
	$isguest = false;
}

$confParams  = Params::findOne('1');
$sql="SELECT * FROM store_items WHERE sku=".$confParams->guest_sku;
$connection = Yii::$app->getDb();
$command = $connection->createCommand($sql);
$guest_count = $command->queryAll();
$guest_price = $guest_count[0]['price'];

if(yii::$app->controller->hasPermission('payment/charge') && (strlen($confParams->qb_token)>2 || strlen($confParams->qb_oa2_refresh_token)>2))  {
	if($confParams->qb_env == 'prod') {
		$myList=['cash'=>'Cash','check'=>'Check','creditnow'=>'Credit Card Now!'];
	} else { $myList=['cash'=>'Cash','check'=>'Check','creditnow'=>'TEST CC (Do not use)']; }
} else {
	$myList=['cash'=>'Cash','check'=>'Check'];
}
?>

<div class="guest-form" ng-controller="GuestFrom">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['id'=>'GuestForm',]); ?>
    <div class="row">
	  <div class="col-xs-12 col-sm-8" >
        <div class="col-xs-12 col-sm-2" style="display: none;" >
			<?= $form->field($model, 'id')->textInput(['readOnly'=>'true','value'=>$model->id]) ?>
		</div><div class="col-sm-2">

        <?php if($model->isNewRecord) {
			echo $form->field($model, 'badge_number')->textInput(['value'=>$model->badge_number,'readonly'=> $isguest]);
			//echo '</div><div class="col-sm-2">';
			//echo $form->field($model, 'tmp_badge')->textInput(['maxlength' => true]);
			echo '</div><div class="col-sm-2">';
			echo $form->field($model, 'time_in')->textInput(['readonly' => true,'value'=>(yii::$app->controller->getNowTime())]).PHP_EOL;
			echo $form->field($model, 'g_paid')->hiddenInput()->label(false).PHP_EOL;
		} else {
			echo $form->field($model, 'badge_number')->textInput(['readOnly'=>'true']);
			//echo '</div><div class="col-sm-2">';
			//echo $form->field($model, 'tmp_badge')->textInput(['maxlength' => true]);
			echo '</div><div class="col-sm-2">';
			echo $form->field($model, 'time_in')->textInput(['readOnly'=>'true','value'=>yii::$app->controller->pretydtg($model->time_in)]);
			echo '</div><div class="col-sm-2">';

			if ($model->g_paid==1) {
				echo "Guest has Paid."; echo $form->field($model, 'g_paid')->hiddenInput()->label(false).PHP_EOL;
			} else {
				echo $form->field($model, 'g_paid')->dropDownList(['0'=>'Pay Now','y'=>'Junior Event','m'=>'Minor','s'=>'Spouse','o'=>'Observer']).PHP_EOL;
				if ($model->g_paid=='0') {
					echo '</div><div class="col-sm-2">'."<b>$$$ ".Html::a('Please Pay Now','/sales/index?badge='.$model->badge_number)." $$$</b>".PHP_EOL;}
			}
		} ?>

        </div>

        <div class="col-xs-12 col-sm-4">
            <?= $form->field($model, 'g_first_name')->textInput([]) ?>
		</div><div class="col-xs-12 col-sm-4">
			<?= $form->field($model, 'g_last_name')->textInput([]) ?>
        </div>

		<div class="col-xs-6 col-sm-2">
		<?php if($model->isNewRecord) {
			echo $form->field($model, 'g_zip'); ?>
			</div><div class="col-xs-1 col-sm-1"><h3><i class="fa fa-thumbs-down" id="zip_check"></i></h3>
		    <?= $form->field($model, 'g_city')->hiddenInput()->label(false); ?>
            <?php }  else { ?>
                <?= $form->field($model, 'g_city') ?>
            <?php } ?>


		<?php if($model->isNewRecord) {
			echo $form->field($model, 'g_state')->hiddenInput()->label(false);
		} else { ?>
		</div><div class="col-xs-4 col-sm-1">
			<?= $form->field($model, 'g_state'); } ?>
		</div>
		<div class="col-xs-4 col-sm-2">
			<?= $form->field($model, 'g_yob')->label('Year of Birth') ?>
        </div>
		<div style="<?=$Payment_block?>" >
		<div class="col-xs-6 col-sm-2" ><p>
<?php if($model->isNewRecord) { ?>
			<?php echo Html::checkbox('guest-isShooter' ,true,['value'=>1,'id'=>'guest-isShooter']), PHP_EOL; ?><b> - Shooter? </b></p>
			<div class="help-block"></div>
		</div>
		<div class="col-xs-6 col-sm-2" ><p>
			<?php echo Html::checkbox('guest-isSpouse' ,false,['value'=>0,'id'=>'guest-isSpouse']), PHP_EOL; ?><b> - Spouse? </b></p>
			<div class="help-block"></div>
		</div>
		<div class="col-xs-6 col-sm-2" ><p>
			<?php echo Html::checkbox('guest-isYouth' ,false,['value'=>0,'id'=>'guest-isYouth']), PHP_EOL; ?><b> - Junior Event? </b></p>
			<div class="help-block"></div>
		</div>
		<div class="col-xs-6 col-sm-2" ><p>
			<?php echo Html::checkbox('guest-isMinor' ,false,['value'=>0,'id'=>'guest-isMinor']), PHP_EOL; ?><b> - Minor (<18yr)? </b></p>
			<div class="help-block"></div>
		</div>
		<div class="col-xs-6 col-sm-2" ><p>
			<?php echo Html::checkbox('guest-isObserver' ,false,['value'=>0,'id'=>'guest-isObserver']), PHP_EOL; ?><b> - Observer?</b></p>
<?php } else { echo "<b><font size=+1>";
					switch ($model->g_paid) {
						case 'm': echo "is a Minor"; break;
						case 'o': echo "is an Observer"; break;
						case 's': echo "is a Spouse"; break;
						case 'y': echo "is a Youth Participant"; break;
					}; 
					echo "</font></b></p>";
} ?>
			<div class="help-block"></div>
		</div>
		</div>
		<div id="search_error"> </div>

		<div class="btn-group pull-right" id="guest_save_div" <?php if($model->isNewRecord) {echo 'style="display:none"';}?>> <br /><br /><div id="CC_Save">
			<?= Html::submitButton($model->isNewRecord ? $msg : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success done-Guest' : 'btn btn-primary done-Guest','id'=>'save_btn']).PHP_EOL;  ?>
		</div></div>



<?php if($model->isNewRecord) { ?>
<div class="col-xs-12 col-sm-12">

<p><b>Guest Safety Acknowledgement:</b></p>
<ol>
  <li><b>Guests or Responsible Adult agrees to AGC <a href="<?=yii::$app->params['wp_site']?>/waiver" target="_blank">Waiver of Liability</a>.</b></li>
  <li>Assume every gun is always loaded.</li>
  <li>Never allow your firearm to point in any direction other than downrange (toward your target) or straight up.</li>
  <li>Keep your finger off the trigger until your sights are on the target and you are ready to shoot.</li>
  <li>Be sure of your target and what is beyond it.</li>
  <li>When “CEASE FIRE” is called, stop shooting immediately and allow the Badge Holder to make the firearm safe.</li>
  <li>During a Cease Fire, you are not to have any contact with any firearm.  Step off the concrete pad and remain off. You can go with the Badge Holder downrange to change your targets. When you return from downrange, you shall remain off of the concrete pad.</li>
  <li>Your Badge Holder will be held accountable for any range rules you break.  You are to be “closely supervised and monitored” by the Badge Holder who signed you in.</li>
  <li>If your Badge Holder needs to leave the firing line, make sure the firearm is made safe (unloaded, ECI inserted) step off the concrete pad and do not handle any firearms until your Badge Holder returns.</li>
</ol>
<p>Please ask your Guest to acknowledge the above statements and that they understand each statement. If your guest is a minor, you can acknowledge for them.</p>
 <?= Html::Button('<i class="fa fa-thumbs-up"> I Agree</i>', ['id'=>'guest-agree','class' => 'btn btn-primary']), PHP_EOL ?>
</div>
<?php } ?>

		</div>
<?php if($model->isNewRecord) { ?>
	  <div class="col-xs-12 col-sm-4">
		<div class="row summary-block-payment" style="margin: 0px;<?=$Payment_block?>" id="Div_Payment_Block">
			<h3>$<?=$guest_price ?>  per Guest</h3><hr />
			<input type="hidden" id='guest_price' value='<?=$guest_price ?>' />
			<?= $form->field($model, 'guest_count')->dropDownList(['1'=>1,'2'=>2,'3'=>3,'4'=>4,'5'=>5,'6'=>6,'7'=>7,'8'=>8,'9'=>9,'10'=>10,'11'=>11,'12'=>12,'13'=>13,'14'=>14,'15'=>15,'16'=>16,'17'=>17,'18'=>18,'19'=>19],['prompt'=>'select']).PHP_EOL; ?>
			<?= $form->field($model, 'amount_due').PHP_EOL; ?>
			<?= $form->field($model, 'payment_type')->dropdownList($myList,['prompt'=>'Payment Type']).PHP_EOL;?>

		<div id="cc_form_div" style="margin-left: 25px">
			<div id="CC_success">
			<div class="col-xs-12 col-sm-12">
				<input type="radio" id="Who_pays" name="Who_pays" onclick="g_handleClick(this);" /> &nbsp;<label for="guest"> Guest info:</label> &nbsp; &nbsp; | &nbsp; &nbsp;
				<input type="radio" id="Who_pays" name="Who_pays" onclick="m_handleClick(this);" /> &nbsp;<label for="member"> Member info:</label>
			</div>
			<div class="col-xs-8 col-sm-8">
				<?= $form->field($model, 'cc_name')->textInput().PHP_EOL; ?>
			</div>
			<div class="col-xs-4 col-sm-4">
				<?= $form->field($model, 'cc_zip')->textInput().PHP_EOL; ?>
			</div>
			<div class="col-xs-12 col-sm-12">
				<?= $form->field($model, 'cc_address')->textInput().PHP_EOL; ?>
			</div>
			<div class="col-xs-8 col-sm-8">
				<?= $form->field($model, 'cc_city')->textInput().PHP_EOL; ?>
			</div>
			<div class="col-xs-4 col-sm-4">
				<?= $form->field($model, 'cc_state')->textInput().PHP_EOL; ?>
			</div>
			<div class="col-xs-12 col-sm-12">
				<?= $form->field($model, 'cc_num')->textInput(['maxlength'=>true]).PHP_EOL; ?>
			</div>
			<div class="col-xs-4 col-sm-4">
				<?= $form->field($model, 'cc_exp_mo')->dropDownList(['01'=>'01 Jan','02'=>'02 Feb','03'=>'03 Mar','04'=>'04 Apr','05'=>'05 May','06'=>'06 Jun','07'=>'07 Jul','08'=>'08 Aug','09'=>'09 Sep','10'=>'10 Oct','11'=>'11 Nov','12'=>'12 Dec']).PHP_EOL; ?>
			</div>
			<div class="col-xs-5 col-sm-5">
<?php 	$curYr = date('Y',strtotime(yii::$app->controller->getNowTime()));
$ccYear = range($curYr,$curYr+25);  ?>
				<?= $form->field($model, 'cc_exp_yr')->dropDownList($ccYear).PHP_EOL; ?>
			</div>
			<div class="col-xs-3 col-sm-3">
				<?= $form->field($model, 'cc_cvc')->textInput(['maxlength'=>true]).PHP_EOL; ?>
			</div>
			</div>
			<?= $form->field($model, 'cc_x_id')->hiddenInput()->label(false).PHP_EOL; ?>
			<div class="col-xs-4 col-sm-4 form-group">
				<?= Html::Button('<i class="fa fa-credit-card"> Process</i>', ['id'=>'guest-Process_CC','class' => 'btn btn-danger']), PHP_EOL ?>
			</div>
			<div class="col-xs-8 col-sm-8">
				<p id="cc_info"> </p>
			</div>
			<div class="col-xs-12" id="cert_online_search" style="display: none">
			<center><img src="<?=yii::$app->params['rootUrl']?>/images/animation_processing.gif" style="width: 50px" />Searching..</center>
			<p>  </p>
			</div><?php if($confParams->qb_env=='dev') {echo "Test CC: 4111-1111-1111-1111";} ?>
		</div>
		<p> </P>

		</div>
		<div class="row">
		<div class="col-xs-12" id="cert_search" ><!--style="display: none"> -->
			<center><img src="<?=yii::$app->params['rootUrl']?>/images/animation_processing.gif" style="width: 50px" />Searching..</center>
			<p>  </p>
		</div>
		<div class="col-xs-12 text-center" id="cert_search_results" > </div>
		</div>
			<div class="clearfix"> </div>
		</div>
	  </div><?php } ?>
	</div>
</div>
<input type="hidden" id='m_first_n' />
<input type="hidden" id='m_last_n' />
<input type="hidden" id='m_address' />
<input type="hidden" id='m_city' />
<input type="hidden" id='m_state' />
<input type="hidden" id='m_zip' />
<?php ActiveForm::end(); ?>

<script>
	$("#cert_search").hide();
	$("#cc_form_div").hide();
	var g_amount_due = document.getElementById("guest-amount_due");
	if(g_amount_due){ g_amount_due.disabled=true; }

	if($("#guest-badge_number").val()) {get_member($("#guest-badge_number").val());};

$('#GuestForm').on('submit', function() {
    $('input, select').prop('disabled', false);
});

 <?php if($model->isNewRecord) { ?>
    $("#guest-guest_count").change(function(e) {
        addTime();
        var guest_price = document.getElementById("guest_price").value;
		var guest_count = parseInt(document.getElementById("guest-guest_count").value);
		document.getElementById("guest-amount_due").value = (guest_price * guest_count).toFixed(2);
		if(guest_count>1) {
			document.getElementById("save_btn").innerHTML = 'Add Next Guest';
		} else {
			document.getElementById("save_btn").innerHTML = 'Save';
		}
	});

	$("#guest-payment_type").change(function(e) {
        addTime();
        var pay_meth = document.getElementById("guest-payment_type");
        var selectedVal = pay_meth.options[pay_meth.selectedIndex].value;
        if(selectedVal=="creditnow") {
            $("#cc_form_div").show(500);
			$("#CC_Save").hide();
			document.getElementById("guest-g_paid").value = '';
        } else if(selectedVal=="cash") {
			$("#cc_form_div").hide(500);
			$("#CC_Save").show();
			document.getElementById("guest-g_paid").value = 'a';
		} else {
            $("#cc_form_div").hide(500);
			$("#CC_Save").show();
			document.getElementById("guest-g_paid").value = 'h';
        }
    });


   document.getElementById('guest-cc_num').addEventListener('keyup',function(evt){
        var ccnum = document.getElementById('guest-cc_num');
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        ccnum.value = ccFormat(ccnum.value);
    });
 <?php } ?>

    $("#guest-isObserver").change(function() {
        if (document.getElementById("guest-isObserver").checked == true){
			document.getElementById("guest-isShooter").checked = false;
			document.getElementById("guest-isSpouse").checked = false;
			document.getElementById("guest-isMinor").checked = false;
			document.getElementById("guest-isYouth").checked = false;
			$("#guest-g_paid").val('o');
			$("#guest-guest_count").val('1');
			document.getElementById("guest-guest_count").disabled=true;
			$("#guest-payment_type").val('cash');
			document.getElementById("guest-payment_type").disabled=true;
        } else  {
			document.getElementById("guest-isShooter").checked = true;
			$("#guest-g_paid").val('');
			$("#guest-guest_count").val('');
			document.getElementById("guest-guest_count").disabled=false;
			$("#guest-payment_type").val('');
			document.getElementById("guest-payment_type").disabled=false;
		}
	});

    $("#guest-isMinor").change(function() {
        if (document.getElementById("guest-isMinor").checked == true){
			document.getElementById("guest-isShooter").checked = false;
			document.getElementById("guest-isSpouse").checked = false;
			document.getElementById("guest-isObserver").checked = false;
			document.getElementById("guest-isYouth").checked = false;
			
			if(document.getElementById('guest-g_yob')) {
				if (document.getElementById('guest-g_yob').value=='') {
					alert("Enter your Birth Year.");
					document.getElementById("guest-isMinor").checked = false;
					document.getElementById("guest-isShooter").checked = true;
				} else if (new Date().getFullYear() - document.getElementById('guest-g_yob').value <= 18 ) {
					$("#guest-g_paid").val('m');
					$("#guest-guest_count").val('1');
					document.getElementById("guest-guest_count").disabled=true;
					$("#guest-payment_type").val('cash');
					document.getElementById("guest-payment_type").disabled=true;
				} else {
					alert("No Longer A Minor");
					document.getElementById("guest-isMinor").checked = false;
					document.getElementById("guest-isShooter").checked = true;
				}
			} else {
				alert(" Please Check Year Of Birth ");
				document.getElementById("guest-isMinor").checked = false;
				document.getElementById("guest-isShooter").checked = true;
			}
        } else  {
			document.getElementById("guest-isShooter").checked = true;
			$("#guest-g_paid").val('');
			$("#guest-guest_count").val('');
			document.getElementById("guest-guest_count").disabled=false;
			$("#guest-payment_type").val('');
			document.getElementById("guest-payment_type").disabled=false;
		}
	});

    $("#guest-isShooter").change(function() {
        if (document.getElementById("guest-isShooter").checked == true){
			document.getElementById("guest-isObserver").checked = false;
			document.getElementById("guest-isSpouse").checked = false;
			document.getElementById("guest-isMinor").checked = false;
			document.getElementById("guest-isYouth").checked = false;
			$("#guest-g_paid").val('');
			$("#guest-guest_count").val('1');
			document.getElementById("guest-guest_count").disabled=false;
			document.getElementById("guest-payment_type").disabled=false;
        } else  {
			$("#guest-g_paid").val('');
			$("#guest-guest_count").val('');
			document.getElementById("guest-guest_count").disabled=false;
			$("#guest-payment_type").val('');
			document.getElementById("guest-payment_type").disabled=false;
		}
	});

    $("#guest-isSpouse").change(function() {
        if (document.getElementById("guest-isSpouse").checked == true){
			document.getElementById("guest-isShooter").checked = false;
			document.getElementById("guest-isObserver").checked = false;
			document.getElementById("guest-isMinor").checked = false;
			document.getElementById("guest-isYouth").checked = false;
			$("#guest-g_paid").val('s');
			$("#guest-guest_count").val('1');
			document.getElementById("guest-guest_count").disabled=true;
			$("#guest-payment_type").val('cash');
			document.getElementById("guest-payment_type").disabled=true;
        } else  {
			document.getElementById("guest-isShooter").checked = true;
			$("#guest-g_paid").val('');
			$("#guest-guest_count").val('');
			document.getElementById("guest-guest_count").disabled=false;
			$("#guest-payment_type").val('');
			document.getElementById("guest-payment_type").disabled=false;
		}
    });

    $("#guest-isYouth").change(function() {
        if (document.getElementById("guest-isYouth").checked == true){
			document.getElementById("guest-isShooter").checked = false;
			document.getElementById("guest-isSpouse").checked = false;
			document.getElementById("guest-isObserver").checked = false;
			document.getElementById("guest-isMinor").checked = false;
			$("#guest-g_paid").val('y');
			$("#guest-guest_count").val('1');
			document.getElementById("guest-guest_count").disabled=true;
			$("#guest-payment_type").val('cash');
			document.getElementById("guest-payment_type").disabled=true;
        } else  {
			document.getElementById("guest-isShooter").checked = true;
			$("#guest-g_paid").val('');
			$("#guest-guest_count").val('');
			document.getElementById("guest-guest_count").disabled=false;
			$("#guest-payment_type").val('');
			document.getElementById("guest-payment_type").disabled=false;
		}
    });

    $("#guest-agree").click(function(e) {
        e.preventDefault();
		addTime();
        $("#guest_save_div").show();
        $("#guest-agree").hide();
    });

	$("#guest-Process_CC").click(function(e) {
		e.preventDefault();
console.log('_form:228: here');
		e.preventDefault();
		document.getElementById("guest-Process_CC").disabled=true;
		$("p#cc_info").html("Processing...");
		document.getElementById("guest-amount_due").disabled=false;

		var formDataB = $("#GuestForm,#form_badge_cert").serializeArray();
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
						$("#guest-cc_x_id").val(responseData.message.id);
						$("#guest-Process_CC").hide();
						$("#guest-g_paid").val('1');
						document.getElementById("guest-isObserver").disabled = true;
						document.getElementById("guest-isSpouse").disabled = true;
						document.getElementById("guest-payment_type").disabled = true;
						document.getElementById("guest-guest_count").disabled=true;
						$("#CC_Save").show();
						$("#CC_success").hide();
					} else {
						$("p#cc_info").html( "Card: "+ responseData.message);
					}
				} else {
					console.log("Data error " + JSON.stringify(responseData));
					SwipeError(JSON.stringify(responseData),'b-v-g-f:253');
					$("p#cc_info").html(responseData.message);
				}

			},
			error: function (responseData, textStatus, errorThrown) {
				$("p#cc_info").html("PHP error:<br>"+responseData.responseText);
				SwipeError(JSON.stringify(responseData),'b-v-g-f:430');
				console.log("error "+ JSON.stringify(responseData.responseText));
			},
		});
		document.getElementById("guest-Process_CC").disabled=false;
		document.getElementById("guest-amount_due").disabled=true;
	});

	function g_handleClick(myRadio) {  // Guest Radio
		$("#guest-cc_name").val($("#guest-g_first_name").val()+ ' '+$("#guest-g_last_name").val());
		document.getElementById("guest-cc_name").disabled = true;
		$("#guest-cc_zip").val(document.getElementById("guest-g_zip").value);
		document.getElementById("guest-cc_zip").disabled = true;
		$("#guest-cc_state").val(document.getElementById("guest-g_state").value);
		document.getElementById("guest-cc_state").disabled = true;
		$("#guest-cc_city").val(document.getElementById("guest-g_city").value);
		//document.getElementById("guest-g_city").disabled = true;
		$("#guest-cc_address").val('');
	};
	function m_handleClick(myRadio) {  // Member Radio
		document.getElementById("guest-cc_name").disabled = false;
		$("#guest-cc_name").val($("#m_first_n").val()+' '+$("#m_last_n").val());
		document.getElementById("guest-cc_zip").disabled = false;
		$("#guest-cc_zip").val($("#m_zip").val());
		$("#guest-cc_state").val($("#m_state").val());
		document.getElementById("guest-cc_state").disabled = false;
		$("#guest-cc_city").val($("#m_city").val());
		$("#guest-cc_address").val($("#m_address").val());
	};

    $("#guest-badge_number").change(function(e) {
        if(this.value) {
			get_member(this.value);
        }
    });

	function get_member(badge_number) {
		jQuery.ajax({
			method: 'GET',
			dataType:'json',
			url: '<?=yii::$app->params['rootUrl']?>/badges/api-request-family?badge_number='+badge_number,
			crossDomain: false,
			success: function(responseData, textStatus, jqXHR) {
				if(responseData.status=='success') {
					console.log(responseData);

					document.getElementById("search_error").innerHTML ='';
					document.getElementById("m_first_n").value =responseData.first_name;
					document.getElementById("m_last_n").value =responseData.last_name;
					document.getElementById("m_address").value = responseData.address;
					document.getElementById("m_city").value = responseData.city;
					document.getElementById("m_state").value = responseData.state;
					document.getElementById("m_zip").value = responseData.zip;

				} else if(responseData.status=='error') {
					console.log(responseData);
					document.getElementById("search_error").innerHTML  = "<font color='red'><b>Sorry! could not find a Badge Holder</b></font>";
					document.getElementById("m_first_n").value ='';
					document.getElementById("m_last_n").value ='';
					document.getElementById("m_address").value = '';
					document.getElementById("m_city").value = '';
					document.getElementById("m_state").value = '';
					document.getElementById("m_zip").value = '';
					$("#guest-badge_number").val('');
				}
			},
			error: function (responseData, textStatus, errorThrown) {
				console.log(responseData);
			},
		});
	}

	function ProcessSwipe(cleanUPC) {

		if  ((cleanUPC.indexOf('ANSI 6360') > 0) || (cleanUPC.indexOf('AAMVA6360') > 0)) {
			console.log('barcode scanned: ', cleanUPC);
			var FName=false;

			if (newUPC.indexOf('DAC') > 0) {  //Parse Name
			  var fsName = newUPC.indexOf('DAC')+3;
			  var feName = newUPC.indexOf("ArrowDown",fsName);
			  var FName = newUPC.slice(fsName,feName);
			  FName = titleCase(FName);
			  var msName = newUPC.indexOf('DAD')+3;
			  var meName = newUPC.indexOf("ArrowDown",msName);
			  var MName = newUPC.slice(msName,meName);
			  MName = MName.charAt(0).toUpperCase();
			  var lsName = newUPC.indexOf('DCS')+3;
			  var leName = newUPC.indexOf("ArrowDown",lsName);
			  var LName = newUPC.slice(lsName,leName);
			  LName = titleCase(LName);
			} //Parse Name Second Try
			else if  (newUPC.indexOf('DAA') > 0) {
			  var nsName = newUPC.indexOf('DAA')+3;
			  var neName = newUPC.indexOf("ArrowDown",nsName);
			  var FullName = newUPC.slice(nsName,neName);
			  FullName = FullName.split(",");
			  var LName = titleCase(FullName[0]);
			  var FName = titleCase(FullName[1]);
			  var MName = FullName[2].charAt(0).toUpperCase();
			}
            console.log("Full Name: "+FName+' - '+MName+' - '+LName);
			document.getElementById("guest-g_first_name").value = FName+' '+MName;
			document.getElementById("guest-g_last_name").value = LName;

			if (newUPC.indexOf('DBB') > 0) {  //Parse Date of Birth
			  var fDOB = newUPC.indexOf('DBB')+3;
			  var lDOB = newUPC.indexOf("ArrowDown",fDOB);
			  var DOB = newUPC.slice(fDOB,lDOB);

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
			  document.getElementById("guest-g_yob").value = DOBy;
              console.log("DOB: m "+DOBm+" d "+DOBd+" y "+DOBy);
			}

			if (newUPC.indexOf('DAI') > 0) {  //Parse City
			  var fCty = newUPC.indexOf('DAI')+3;
			  var lCty = newUPC.indexOf("ArrowDown",fCty);
			  var Cty = newUPC.slice(fCty,lCty);
			  Cty = titleCase(Cty);
			  document.getElementById("guest-g_city").value = Cty;
              console.log("City: "+Cty);
			}

			if (newUPC.indexOf('DAJ') > 0) {  //Parse State
			  var fST = newUPC.indexOf('DAJ')+3;
			  var lST = newUPC.indexOf("ArrowDown",fST);
			  var Stat = newUPC.slice(fST,lST);
			  document.getElementById("guest-g_state").value = Stat;
              console.log("State: "+Stat);
            }

			if (newUPC.indexOf('DAK') > 0) {  //Parse ZIP
			  var fZIP = newUPC.indexOf('DAK')+3;
			  var lZIP = newUPC.indexOf("ArrowDown",fZIP);
			  var ZIP = newUPC.slice(fZIP,lZIP);
			  ZIP = ZIP.substring(0,5);
			  document.getElementById("guest-g_zip").value = ZIP;
              console.log("ZIP: "+ZIP);
			}
		} else { SwipeError(cleanUPC,'b_v_g_f:205'); }
		cleanUPC = '';
	}
</script>