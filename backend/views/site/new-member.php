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

$this->title = 'Register New Member (Self-service)';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="Self-Member-create">

    <h2><?= Html::encode($this->title) ?></h2>
<div class="container row" id="div_Ack" >
<style>
#AckTbl {
	width: 100%;
	background-color: #f1f1c1;
}
table, td {
	border: 1px solid black;
	padding: 8px;
	margin: 8px;
	font-size: 18px;
	text-align:left;
}
input[type='checkbox'] {
    width:30px;
    height:30px;
}
</style>
	<br><hr>
	<h2>Please Read and Agree to our Terms of Service:</h2>
	<table id="AckTbl">
		<tr>
			<td style="width:4%; text-align:center"> <input type=checkbox name='agreed' onClick="ChkAgreed()"></td>
			<td>1. You agree to the AGC <a href="<?=yii::$app->params['wp_site']?>/waiver" target="waver">Waiver of Liability</a>.</td>
		</tr>
		<tr>
			<td style="width:4%; text-align:center"> <input type=checkbox name='agreed' onClick="ChkAgreed()"></td>
			<td>2. You will assume every gun is always loaded.</td>
		</tr>
		<tr>
			<td style="width:4%; text-align:center"> <input type=checkbox name='agreed' onClick="ChkAgreed()"></td>
			<td>3. You will never allow your firearm to point in any direction other than downrange (toward your target) or straight up.</td>
		</tr>
		<tr>
			<td style="width:4%; text-align:center"> <input type=checkbox name='agreed' onClick="ChkAgreed()"></td>
			<td>4. You will keep your finger off the trigger until your sights are on the target and you are ready to shoot.</td>
			</tr>
		<tr>
			<td style="width:4%; text-align:center"> <input type=checkbox name='agreed' onClick="ChkAgreed()"></td>
			<td>5. When “CEASE FIRE” is called, you will stop shooting immediately and make the firearm safe.</td>
		</tr>
		<tr>
			<td style="width:4%; text-align:center"> <input type=checkbox name='agreed' onClick="ChkAgreed()"></td>
			<td>6. During a Cease Fire, you will not have any contact with any firearm. </td>
		<tr>
		<tr>
			<td style="width:4%; text-align:center"> <input type=checkbox name='agreed' onClick="ChkAgreed()"></td>
			<td>7. You will be held accountable for any range rules you break.</td>
		</tr>
		<tr>
			<td style="width:4%; text-align:center"> <input type=checkbox name='agreed' onClick="ChkAgreed()"></td>
			<td>8. You have completed a Range Safety Orientation and possess a signed Orientation Affidavit.</td>
		</tr>
		<tr>
			<td style="width:4%; text-align:center"> <input type=checkbox name='agreed' onClick="ChkAgreed()"></td>
			<td>9. You have joined an AGC member club and possess a club membership ID.</td>
		</tr>
	</table>
	<br>
	<?= Html::Button('<i class="fa fa-thumbs-up"> I Agree</i>', ['id'=>'new-agree', 'class' => 'btn btn-primary', 'onclick' => 'AckAgree();']), PHP_EOL ?> 
</div>
<div class="container badgessm-form" id="div_FormBody" style="display:none;">
    <?php $form = ActiveForm::begin([
			'id'=>'NewMembers',
			'enableAjaxValidation' => true,
			'validateOnSubmit' => true,]); ?>
	<div class="row">

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
			<?= $form->field($model, 'mem_type')->dropDownList($model->getMemberShipList(true),[]).PHP_EOL; ?>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-4">
			<?= $form->field($model, 'club_id')->dropDownList((new clubs)->getClubList(false,false,2), ['prompt'=>'select']).PHP_EOL; ?>
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
			<?= $form->field($model, 'city')->textInput(['autocomplete' => 'off']) ?>
		</div>

		<div class="col-xs-6 col-sm-2">
			<?= $form->field($model, 'state')->dropDownList(yii::$app->controller->getStates(),['value'=>'MD']) ?>
		</div>
		<div class="col-xs-6 col-sm-2">
			<p><?=  $form->field($model, 'gender')->radioList([ '0'=>'Male', '1'=> 'Female'],['value'=>0]) ?></p>
		</div>
		<div class="col-xs-6 col-sm-2">
			<?= $form->field($model, 'yob')->dropDownList($YearList,['value'=>$MyYr-13 ]) ?>
		</div>
		<div class="col-xs-12 col-sm-6">
			<?= $form->field($model, 'email')->textInput(['autocomplete' => 'off','class'=>'form-control']) ?>
		</div>
		<div class="col-xs-12 col-sm-6">
			<?= $form->field($model, 'email_verify')->textInput(['autocomplete' => 'off','class'=>'form-control']) ?>
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
	</div>
	<div class="row">
		<div class="col-xs-12" id="HideMySubmit">
			<?= Html::submitButton('<i class="fa fa-save"> </i> Register', ['class' => 'btn btn-success','id'=>'self_save']).PHP_EOL; ?>
			<?= Html::a('<i class="fa fa-eraser"> </i> Clear',['/site/new-member'],['class' => 'btn btn-danger']).PHP_EOL; ?>
			<div id="run_animation" style="display: none">
				<img src="<?=yii::$app->params['rootUrl']?>/images/animation_processing.gif" style="width: 100px"> Please Wait.. </h4>
			</div>
		</div>
	</div>
<hr />
	<?= $form->field($model, 'badge_number')->hiddenInput(['readonly'=>true, 'value' =>str_pad($model->badge_number, 5, '0', STR_PAD_LEFT) ])->label(false) ?>
	<?= $form->field($model, 'incep')->hiddenInput(['readonly' => true,'value'=>date('M d, Y h:i A',strtotime(yii::$app->controller->getNowTime()))])->label(false) ?>
	<?= $form->field($model, 'qrcode')->hiddenInput(['readOnly'=>true])->label(false) ?>
	<?= $form->field($model, 'status')->hiddenInput(['value'=>"self"])->label(false) ?>

	<?php ActiveForm::end(); ?>
</div>
</div>
</div>

<script>
	document.getElementById("new-agree").disabled=true;

	$('#NewMembers').on('beforeSubmit', function (e) {
		document.getElementById("self_save").disabled=true;
		$("run_animation").show();
		console.log('submitting...');
	});

	function checkForm() {
		console.log("Check Form");
		var $form = $("#NewMembers"),data = $form.data("yiiActiveForm");
		$.each(data.attributes, function() {
		   this.status = 3;
		});
		$form.yiiActiveForm("validate");
		
		if ($("#NewMembers").find(".has-error").length) { 
			document.getElementById("self_save").disabled=true;
			return false; 
		} else { 
			document.getElementById("self_save").disabled=false;
			return true; 
		}
	}

	function AckAgree() {
		$("#div_Ack").hide();
		$("#div_FormBody").show();
	}
	
	function ChkAgreed() {
		b=0;
		checkboxes = document.getElementsByName('agreed');
		for(var i=0, n=checkboxes.length;i<n;i++) {
			if (checkboxes[i].checked == true) { b++;}
		}
		if(b==9) {document.getElementById("new-agree").disabled=false;} 
		else {document.getElementById("new-agree").disabled=true;}
	}
	
	function CheckOnline() {
		// Only For Renewals!console
	}

    function fillQR() {
        var ranText="";
        var possible = "ABCDEFGHJKMNPQRSTUVWXYZ23456789";
        for (var ri = 0; ri < 2; ri++)
           ranText += possible.charAt(Math.floor(Math.random() * possible.length));
        var barcodeData = ("00" + $('#badgessm-club_id').val()).slice(-2)+' '+("00" + $('#badgessm-mem_type').val()).slice(-2)+' '+$('#badgessm-badge_number').val()+' '+ranText;
        $('#badgessm-qrcode').val(barcodeData);
        barcodeGenerate(barcodeData);
        $(".barcode").show(300);
    };

	$("#badgessm-club_id").change(function() {
		fillQR();
	});

	$("#badgessm-mem_type").change(function() {
		run_waitMe('show');
		var memTypeId = $("#badgessm-mem_type").val();
		if(memTypeId=='51') {
			$("#HideMySubmit").hide(500);
			family_badge_view('show');
		} else {
			$("#HideMySubmit").show(500);
			family_badge_view('hide');
		}
		run_waitMe('hide');
		fillQR();
	});

    $("#badgessm-primary").change(function() {
        var primaryRequest = $("#badgessm-primary").val();
        if(primaryRequest!=null || primaryRequest !=0) {
            getPrimaryBadger(primaryRequest,'self');
        }
        else {
            //alert("error reporting");
        }
        if(primaryRequest==0 || primaryRequest ==null) {
 //           $("#primary-badge-summary").hide(500);
        }
    });

    $('#badgessm-zip').keyup(function(e) {
        zipcode = $("#badgessm-zip").val();
        if(zipcode.length==5) {
            console.log('Using '+zipcode);
            jQuery.ajax({
                method: 'GET',
                url: '<?=yii::$app->params['rootUrl']?>/badges/api-zip?zip='+zipcode,
                crossDomain: false,
                async: true,
                success: function(responseData, textStatus, jqXHR) {
                    responseData = JSON.parse(responseData);
                    if(responseData.City) {
					$mycity=responseData.City.toProperCase()
                    $("#badgessm-city").val($mycity);
                    $("#badgessm-state").val(responseData.State);
					}
                },
                error: function (responseData, textStatus, errorThrown) {
                    console.log(responseData);
                },
            });
        }
    });

    document.getElementById('badgessm-phone').addEventListener('keyup',function(evt){
        var phoneNumber = document.getElementById('badgessm-phone');
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        phoneNumber.value = phoneFormat(phoneNumber.value);
    });

    document.getElementById('badgessm-phone_op').addEventListener('keyup',function(evt){
        var phoneNumber = document.getElementById('badgessm-phone_op');
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        phoneNumber.value = phoneFormat(phoneNumber.value);
    });

    document.getElementById('badgessm-ice_phone').addEventListener('keyup',function(evt){
        var phoneNumber = document.getElementById('badgessm-ice_phone');
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        phoneNumber.value = phoneFormat(phoneNumber.value);
    });

  function subform() {
    //e.preventDefault();
    console.log(checkForm());
  };
  
function doCalcNew() {
//nope
};

function ProcessSwipe(cleanUPC) {
//nope
};

$( document ).ready(function() {
  family_badge_view('hide');
  fillQR();
});
</script>
