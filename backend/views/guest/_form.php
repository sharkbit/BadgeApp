<?php
use backend\models\Params;

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\Guest */
/* @var $form yii\widgets\ActiveForm */

if ( ($model->isNewRecord) && ($_SESSION["badge_number"]>0) ) { $model->badge_number=$_SESSION["badge_number"]; }

$model->time_in = yii::$app->controller->getNowTime();
$Payment_block=''; $msg='Register';

if(!yii::$app->controller->hasPermission('guest/modify')) {
	$isguest = true;
} else {
	if(!empty($stickyGuest)) {
		$model->badge_number = $stickyGuest['badge_number'];
	}
	$isguest = false;
}

$confParams = Params::findOne('1');
$guest_band = (new backend\models\StoreItems)->find()->where(['sku'=>$confParams->guest_sku])->one();
if(!$guest_band) { echo '<h2>Please verify Guest Sku in App->Admin->Settings</h2>'; return;}
?>

<div class="guest-form" ng-controller="GuestFrom">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['id'=>'GuestForm',
				'enableAjaxValidation' => true,
				'enableClientValidation'=>true,
				'validateOnSubmit'=>true,]); ?>
    <div class="row">
	  <div class="col-xs-12 col-sm-8" >
        <div class="col-xs-12 col-sm-2" style="display: none;" >
			<?= $form->field($model, 'id')->textInput(['readOnly'=>'true','value'=>$model->id]) ?>
		</div>

	<div class="row">
		<div class="col-sm-2">
        <?php if($model->isNewRecord) {
			echo $form->field($model, 'badge_number')->textInput(['value'=>$model->badge_number,'readonly'=> $isguest]);
			echo '</div><div class="col-sm-2">';
			echo $form->field($model, 'time_in')->textInput(['readonly' => true,'value'=>(yii::$app->controller->getNowTime())]).PHP_EOL;
			echo $form->field($model, 'g_paid')->hiddenInput(['value'=>(isset($_SESSION['stickyForm'])) ? $_SESSION['stickyForm']['g_paid'] : 'a'])->label(false).PHP_EOL;
		} else {
			echo $form->field($model, 'badge_number')->textInput(['readOnly'=>'true']);
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
		<div class="col-sm-8">
		</div>
	</div>
	<div class="row">
        <div class="col-xs-12 col-sm-4">
            <?= $form->field($model, 'g_first_name')->textInput([]) ?>
		</div>
		<div class="col-xs-12 col-sm-5">
			<?= $form->field($model, 'g_last_name')->textInput([]) ?>
        </div>
		<div class="col-xs-4 col-sm-2">
			<?= $form->field($model, 'g_yob')->label('Year of Birth') ?>
        </div>
		<div class="col-sm-1">
		</div>
	</div>
	<div class="row">
		<div class="col-xs-6 col-sm-2">
			<?php if($model->isNewRecord) {
				echo $form->field($model, 'g_zip'); ?>
				</div>
				<div class="col-xs-6 col-sm-2"><h3><i class="fa fa-thumbs-down" id="zip_check"></i></h3>
				<?= $form->field($model, 'g_city')->hiddenInput()->label(false); ?>
				<?php }  else { ?>
					<?= $form->field($model, 'g_city') ?>
				<?php } ?>

			<?php if($model->isNewRecord) {
				echo $form->field($model, 'g_state')->hiddenInput()->label(false);
			} else { ?>
			</div>
			<div class="col-xs-6 col-sm-8">
				<?= $form->field($model, 'g_state'); } ?>
		</div>
	</div>
	<h3><p><b>Type of Visitor?</b></p></h3>
	<div class="row">
		<div class="col-xs-6 col-sm-3" ><p>
	<?php if($model->isNewRecord) { ?>
			<?php echo Html::checkbox('guest-isShooter' ,true,['value'=>1,'id'=>'guest-isShooter']), PHP_EOL; ?><b> - Shooter</b></p>
			<div class="help-block"></div>
		</div>
		<div class="col-xs-6 col-sm-2" ><p>
			<?php echo Html::checkbox('guest-isSpouse' ,false,['value'=>0,'id'=>'guest-isSpouse']), PHP_EOL; ?><b> - Spouse</b></p>
			<div class="help-block"></div>
		</div>
		<div class="col-xs-6 col-sm-2" ><p>
			<?php echo Html::checkbox('guest-isYouth' ,false,['value'=>0,'id'=>'guest-isYouth']), PHP_EOL; ?><b> - Jr. Event</b></p>
			<div class="help-block"></div>
		</div>
		<div class="col-xs-6 col-sm-2" ><p>
			<?php echo Html::checkbox('guest-isMinor' ,false,['value'=>0,'id'=>'guest-isMinor']), PHP_EOL; ?><b> - Minor (<18yr)</b></p>
			<div class="help-block"></div>
		</div>
		<div class="col-xs-6 col-sm-2" ><p>
			<?php echo Html::checkbox('guest-isObserver' ,false,['value'=>0,'id'=>'guest-isObserver']), PHP_EOL; ?><b> - Observer</b></p>
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
		<div class="col-sm-1">
		</div>
	</div>

		<div id="search_error"> </div>

	<?php if($model->isNewRecord) { ?>
	<div class="row">
		<div class="col-xs-12 col-sm-12">

		<h4><p><b>Guest Safety Acknowledgement:</b></p></h4>
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
		 <?= Html::submitButton('<i class="fa fa-thumbs-up"> I Agree</i>', ['id'=>'guest-agree','class' => 'btn btn-primary']), PHP_EOL ?>
		</div>
	</div>
	<?php } ?>
	</div>
<?= $form->field($model, 'guest_count')->hiddenInput(['value'=>'1'])->label(false).PHP_EOL; ?>
<?= $form->field($model, 'payment_type')->hiddenInput(['value'=>'cash'])->label(false).PHP_EOL; ?>
<input type="hidden" id='m_first_n' />
<input type="hidden" id='m_last_n' />
<input type="hidden" id='m_address' />
<input type="hidden" id='m_city' />
<input type="hidden" id='m_state' />
<input type="hidden" id='m_zip' />
<?php ActiveForm::end(); ?>
</div>
<script>
	$("#cert_search").hide();
	$("#cc_form_div").hide();
	var g_amount_due = document.getElementById("guest-amount_due");
	if(g_amount_due){ g_amount_due.disabled=true; }

	if($("#guest-badge_number").val()) {get_member($("#guest-badge_number").val());};

    $("#guest-isObserver").change(function() {
        if (document.getElementById("guest-isObserver").checked == true){
			document.getElementById("guest-isShooter").checked = false;
			document.getElementById("guest-isSpouse").checked = false;
			document.getElementById("guest-isMinor").checked = false;
			document.getElementById("guest-isYouth").checked = false;
			$("#guest-g_paid").val('o');
        } else  {
			document.getElementById("guest-isShooter").checked = true;
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
		}
	});

    $("#guest-isShooter").change(function() {
        if (document.getElementById("guest-isShooter").checked == true){
			document.getElementById("guest-isObserver").checked = false;
			document.getElementById("guest-isSpouse").checked = false;
			document.getElementById("guest-isMinor").checked = false;
			document.getElementById("guest-isYouth").checked = false;
			$("#guest-g_paid").val('');
        }
	});

    $("#guest-isSpouse").change(function() {
        if (document.getElementById("guest-isSpouse").checked == true){
			document.getElementById("guest-isShooter").checked = false;
			document.getElementById("guest-isObserver").checked = false;
			document.getElementById("guest-isMinor").checked = false;
			document.getElementById("guest-isYouth").checked = false;
			$("#guest-g_paid").val('s');
        } else  {
			document.getElementById("guest-isShooter").checked = true;
			$("#guest-g_paid").val('');
		}
    });

    $("#guest-isYouth").change(function() {
        if (document.getElementById("guest-isYouth").checked == true){
			document.getElementById("guest-isShooter").checked = false;
			document.getElementById("guest-isSpouse").checked = false;
			document.getElementById("guest-isObserver").checked = false;
			document.getElementById("guest-isMinor").checked = false;
			$("#guest-g_paid").val('y');
        } else  {
			document.getElementById("guest-isShooter").checked = true;
			$("#guest-g_paid").val('');
		}
    });

	$("#guest-Process_CC").click(function(e) {
		console.log('_form:465: here');

		var $form = $("#GuestForm"),data = $form.data("yiiActiveForm");
		$.each(data.attributes, function() {
		   this.status = 3;
		});
		$form.yiiActiveForm("validate");

		if ($("#GuestForm").find(".has-error").length) {
			console.log('_form:476: Validation Failed');
			return false;
		} else {
			//document.getElementById("self_save").disabled=false;
			console.log('_form:478: Pass');

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
						$("#CC_Save").show();
						$("#CC_success").hide();
					} else {
						$("p#cc_info").html( "Card: "+ responseData.message);
					}
				} else {
					console.log("Data error " + JSON.stringify(responseData));
					SwipeError(JSON.stringify(responseData),'b_v_g_f:518');
					$("p#cc_info").html(responseData.message);
				}

			},
			error: function (responseData, textStatus, errorThrown) {
				$("p#cc_info").html("PHP error:<br>"+responseData.responseText);
				SwipeError(JSON.stringify(responseData),'b_v_g_f:525');
				console.log("error "+ JSON.stringify(responseData.responseText));
			},
		});
		document.getElementById("guest-Process_CC").disabled=false;
		document.getElementById("guest-amount_due").disabled=true;
		}
	});

	function g_handleClick(myRadio) {  // Guest Radio
		$("#guest-cc_name").val($("#guest-g_first_name").val()+ ' '+$("#guest-g_last_name").val());
		//document.getElementById("guest-cc_name").disabled = true;
		$("#guest-cc_zip").val(document.getElementById("guest-g_zip").value);
		//document.getElementById("guest-cc_zip").disabled = true;
		$("#guest-cc_state").val(document.getElementById("guest-g_state").value);
		//document.getElementById("guest-cc_state").disabled = true;
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
		var csrf = $('meta[name="csrf-token"]').attr('content');
		jQuery.ajax({
			method: 'POST',
			data: {'_csrf-backend':csrf},
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

		if ((cleanUPC.indexOf('ANSI 6360') > 0) || (cleanUPC.indexOf('AAMVA6360') > 0)) {
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
		}
		else if (cleanUPC.match(/[Bb]\d{16}/g)) {  // Matched Credit Card!
			console.log('Credit Card Scanned: ', cleanUPC);
			var ccNum = cleanUPC.substring(1,17);
			var fExp = cleanUPC.indexOf('^')+1;
			var fExp = cleanUPC.indexOf('^',fExp)+1;
			var ExpYr = cleanUPC.substring(fExp,fExp+2);
			var ExpMo = cleanUPC.substring(fExp+2,fExp+4);
			ccNum = ccFormat(ccNum);
			console.log("Num: "+ccNum+" Exp Yr: "+ExpYr+" Exp Mo: "+ExpMo);
			ExpYr = ExpYr - (new Date().getFullYear().toString().substr(-2));

			document.getElementById("guset-cc_num").value = ccNum;
			document.getElementById("guset-cc_exp_mo").value = ExpMo;
			document.getElementById("guset-cc_exp_yr").value = ExpYr;
		}
		else { SwipeError(cleanUPC,'b_v_g_f:696'); }
		cleanUPC = '';
	}
</script>