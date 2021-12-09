<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use backend\models\clubs;
use backend\models\User;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

$this->title = 'Create User';
$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']];
$this->params['breadcrumbs'][] = ['label' => 'Authorized Users', 'url' => ['/accounts/index']];
$this->params['breadcrumbs'][] = ['label' => 'Create User', 'url' => ['/accounts/create']];

function generateRandomString($length = 15) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
$randStr = generateRandomString();
?>
<div class="site-signup">

	 <h2><?= Html::encode($this->title) ?></h2><hr />
	<?php $form = ActiveForm::begin(['id' => 'form_signup','enableAjaxValidation' => true]); ?>
	<input hidden id='RandStr' value='<?=$randStr ?>' />
	<div class="row">
		<div class="col-xs-4 col-sm-3 col-md-2">
		<?= $form->field($model, 'badge_number').PHP_EOL ?>
		</div>
		<div class="col-xs-8 col-sm-9 col-md-5">
			<?php if (in_array(1, json_decode(yii::$app->user->identity->privilege))) { $limit=false; } else { $limit=true; }
			echo $form->field($model, 'privilege')->dropDownList((new User)->getPrivList($limit),['id'=>'privilege', 'multiple'=>true, 'size'=>false]).PHP_EOL; ?>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-2" id="remote_name" style="display: none;" >
			<?= $form->field($model, 'r_user')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-5" id="need_cal" style="display: none;" >
			<?= $form->field($model, 'clubs')->dropDownList((new clubs)->getClubList(), ['id'=>'club-id','multiple'=>true, 'size'=>false]).PHP_EOL; ?>
		</div>
		<div id='dont_need_cal' ><input type='hidden' id="club-id" value=''> </div>
		<div class="col-xs-12 col-sm-6 col-md-3">
			<?= $form->field($model, 'f_name')->textInput(['autofocus' => true]).PHP_EOL ?>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-3">
			<?= $form->field($model, 'l_name')->textInput(['autofocus' => true]).PHP_EOL ?>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-3">
			<?= $form->field($model, 'email').PHP_EOL ?>
		</div>

		<div id="pass_hide" style="display: none;" >
		<div class="col-xs-12 col-sm-6 col-md-3" >
			<?=$form->field($model, 'password')->passwordInput().PHP_EOL;?>
		</div><div class="col-xs-12 col-sm-6 col-md-3" >
			<?=$form->field($model, 'confirm_password')->passwordInput().PHP_EOL;?>
		</div></div>
		<div id="pass_hide_rev" >
			<input type="hidden" id="signupform-password"  name="SignupForm[password]" value='<?=$randStr ?>' />
			<input type="hidden" id="signupform-confirm_password"  name="SignupForm[confirm_password]" value='<?=$randStr ?>' />
		</div>
		<div class="col-xs-12 col-sm-6 col-md-3" id="cio_hide" style="display: none;" >
			<?= $form->field($model, 'auth_key')->textInput(['autofocus' => true])->label('Company').PHP_EOL ?>
		</div>

		<div class="col-xs-12 col-sm-6 col-md-3">
			<?= $form->field($model, 'username')->textInput(['autofocus' => true]).PHP_EOL ?>
		</div>
	</div>
	<div class="form-group btn-group pull-right">
		<?= Html::submitButton('Create', ['class' => 'btn btn-success', 'name' => 'signup-button']) ?>
		<?= Html::resetButton('Reset <i class="fa fa-eraser"> </i>', ['class' => 'btn btn-danger']) ?>
	</div>
	<?php ActiveForm::end(); ?>
</div>
<div id="my_error_msg"> </div>
<p>
<ul><li>Calendar Access not needed for Root and Admin Users</li></ul>
</p>

<script>
  $("#need_cal").hide();$("#dont_need_cal").show();

  $("#privilege")
    .select2({placeholder_text_multiple:'Select Privilege',width: "100%"})
	.change(function(){
    var selectedText = " "+$(this).find("option:selected").text();
    if (selectedText.indexOf("Root")>0) {
      if (selectedText.length > 5) {
        $("#my_error_msg").html('<center><p style="color:red;"><b>Root should not have any other privilages!.</b></p></center>');
      } else {$("#my_error_msg").html('');}
	  $("#pass_hide").show(); $("#pass_hide_rev").hide();
      $("#signupform-password").val('');
      $("#signupform-confirm_password").val('')
	} else if (selectedText.indexOf("Chairmen")>0) {
		if (selectedText.indexOf("Calendar")>0) {} else {
			$("#my_error_msg").html('<p style="color:red"><b>Must also be Calendar Cordinator</b></p>');
		}
	} else {
      $("#pass_hide").hide(); $("#pass_hide_rev").show();
      $("#signupform-password").val('<?=$randStr ?>');
      $("#signupform-confirm_password").val('<?=$randStr ?>');
	}

	if ((selectedText.indexOf("CIO")>0) || (selectedText.indexOf("Calendar")>0)) {
		if (selectedText.indexOf("CIO")>0) { $("#cio_hide").show(); console.log('hii');}
		$("#need_cal").show(); $("#dont_need_cal").hide();
	} else {
		$("#cio_hide").hide();
		$("#need_cal").hide();$ ("#dont_need_cal").show();
	}

	if (selectedText.indexOf("Remote Access")>0) {
		$("#remote_name").show();
		var rem_usr = document.getElementById("signupform-r_user").value;
		console.log(rem_usr);
		if ((!rem_usr) || (rem_usr=='')) {
			document.getElementById("signupform-r_user").value = document.getElementById("signupform-badge_number").value
		}
	} else {
		$("#remote_name").hide();
	}

    if(selectedText != ' ') { buildUsername();}
  });

  $("#club-id")
    .select2({placeholder_text_multiple:'Choose Clubs',width: "100%"})
	.change(function(){
    var myCom = document.getElementById("signupform-auth_key");
    if(!myCom.value) {
      var selectedText = $(this).find("option:selected").text();
      myCom.value=selectedText;
    }
  });

    $("#signupform-badge_number").change(function() {
    var badgeNumber = $(this).val();
    if((badgeNumber!='') && (badgeNumber!=0)) {
      changeBadgeName(badgeNumber);
    } else {
      $("#signupform-f_name").val('');
      $("#signupform-l_name").val('');
      $("#signupform-email").val('');

    }
  });

  function changeBadgeName(badgeNumber) {
    jQuery.ajax({
      method: 'GET',
      url: '<?=yii::$app->params['rootUrl']?>/badges/get-badge-details?badge_number='+badgeNumber,
      crossDomain: false,
      success: function(responseData, textStatus, jqXHR) {
        responseData =  JSON.parse(responseData);
        //console.log(responseData);
        var PrimeExpTimestamp = getTimestamp(responseData.expires);
        var resExpTimestamp = Math.floor(Date.now() / 1000);

        if(PrimeExpTimestamp < resExpTimestamp) {
          $("#signupform-f_name").val('No Active Member Found');
          $("#signupform-l_name").val('');
          $("#signupform-email").val('');
        } else {
          $("#signupform-f_name").val(responseData.first_name);
          $("#signupform-l_name").val(responseData.last_name);
          $("#signupform-email").val(responseData.email);
          buildUsername();
        }

		var x = document.getElementById("need_cal");
		if (window.getComputedStyle(x).display === "none") {
			var need_cal=true; 
			$("#need_cal").show();
		} else  { var need_cal=false; }
		
		$('#club-id option').attr('selected', false);
		var club = document.getElementById('club-id').options;
	
		var options='';
		for (var i = 0; i < club.length; i++) {
			if (responseData.clubs.includes(club[i].value)) {
				options += '<option value="'+club[i].value+'" selected >'+club[i].text+'</option>';
			} else {
				options += '<option value="'+club[i].value+'">'+club[i].text+'</option>';
			}
		}
		document.getElementById("club-id").innerHTML = options;
		$('club-id').trigger('change');
		$('club-id').trigger("select2:updated");

		if (need_cal) {$("#need_cal").hide();}
      },
      error: function (responseData, textStatus, errorThrown) {
        $("#events-poc_name").val('Valid Badge holder not found');
        console.log("fail "+responseData);
      },
    });
  }

  $("#signupform-f_name").change(function() { buildUsername(); });

  $("#signupform-l_name").change(function() { buildUsername(); });

  function buildUsername() {
    var fname = $("#signupform-f_name").val().replace(/ |\./g,"");
    var lname = $("#signupform-l_name").val().replace(/ |\./g,"");
    var priv = '';

	var sel_opt =document.getElementById("privilege").value;
	 if (sel_opt==1) {priv = 'root'; }
    else if (sel_opt==2) { priv = 'adm'; }
    else if (sel_opt==6) { priv = 'rsol'; }
    else if (sel_opt==3) { priv = 'rso';}
    else if (sel_opt==10) { priv = 'cash'; }
    else if (sel_opt==9) { priv = 'cal'; }
    else if (sel_opt==7) { priv = 'wc';}
    else if (sel_opt==8) { priv = 'cio';  }
    else if (sel_opt==4) { priv = 'view'; }
	else if (sel_opt==12) { priv = 'Arso'; }
	else if (sel_opt==13) { priv = 'adv'; }
    else if (sel_opt==5) { alert('Do not use Member'); priv = 'Do not use Member'; }

    $("#signupform-username").val(priv+'.'+fname+'.'+lname);
  }
</script>
