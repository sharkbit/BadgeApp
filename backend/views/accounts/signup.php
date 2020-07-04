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
$this->params['breadcrumbs'][] = ['label' => 'Admin Function', 'url' => ['/badge/admin-function']];
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
	<?php $form = ActiveForm::begin(['id' => 'form_signup']); ?>
	<input hidden id='RandStr' value='<?=$randStr ?>' />
	<div class="row">
		<div class="col-xs-4 col-sm-3 col-md-2">
		<?= $form->field($model, 'badge_number').PHP_EOL ?>
		</div>
		<div class="col-xs-8 col-sm-9 col-md-5">
<?php if (yii::$app->controller->hasPermission('is_root')) {
	echo $form->field($model, 'privilege')->dropDownList((new User)->getPrivList(),['prompt'=>'Select', 'id'=>'privilege', 'class'=>"chosen_select", 'multiple'=>true, 'size'=>false]).PHP_EOL;
} else {
	echo $form->field($model, 'privilege')->dropDownList((new User)->getPrivList(true),['prompt'=>'Select', 'id'=>'privilege', 'class'=>"chosen_select", 'multiple'=>true, 'size'=>false]).PHP_EOL;
} ?>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-5">
			<?= $form->field($model, 'clubs')->dropDownList((new clubs)->getClubList(), ['id'=>'club-id', 'class'=>"chosen_select", 'multiple'=>true, 'size'=>false]).PHP_EOL; ?>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-6 col-md-3">
			<?= $form->field($model, 'f_name')->textInput(['autofocus' => true]).PHP_EOL ?>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-3">
			<?= $form->field($model, 'l_name')->textInput(['autofocus' => true]).PHP_EOL ?>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-3">
			<?= $form->field($model, 'email').PHP_EOL ?>
		</div>

<?php if(yii::$app->controller->hasPermission('is_root')) { ?>
		<div class="col-xs-12 col-sm-6 col-md-3"  id="pass_hide" style="display: none;" >
		<?=$form->field($model, 'password')->passwordInput().PHP_EOL;?>
		<?=$form->field($model, 'confirm_password')->passwordInput().PHP_EOL;?>
		</div>
<?php } else { ?>
	<input type="hidden" id="signupform-password"  name="SignupForm[password]" value='<?=$randStr ?>' />
	<input type="hidden" id="signupform-confirm_password"  name="SignupForm[confirm_password]" value='<?=$randStr ?>' />
<?php } ?>
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
<script src="<?=yii::$app->params['rootUrl']?>/js/chosen.jquery.min.js"></script>
<script>


 /*  $("#privilege").chosen({placeholder_text_multiple:'Select Privilege',width: "100%"})
   .change(function(){
	var selectedText = " "+$(this).find("option:selected").text();
	if ((selectedText.indexOf("Root")>0) && (selectedText.length > 5)) {
	  console.log('only root');
      $("#my_error_msg").html('<center><p style="color:red;"><b>Root should not have any other privilages!.</b></p></center>');
    } else {$("#my_error_msg").html('');}
	buildUsername();
  }); 

  $("#club-id").chosen({placeholder_text_multiple:'Choose Clubs',width: "100%"}).change(function(){
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
				console.log(responseData);
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
			},
			error: function (responseData, textStatus, errorThrown) {
				$("#events-poc_name").val('Valid Badge holde not found');
				console.log("fail "+responseData);
			},
		});
	}

	$("#signupform-privilege").change(function() {
		if($(this).val()==1) {
			$("#pass_hide").show();
			$("#signupform-password").val('');
			$("#signupform-confirm_password").val('');
		} else {
			$("#pass_hide").hide();
			$("#signupform-password").val('<?=$randStr ?>');
			$("#signupform-confirm_password").val('<?=$randStr ?>');
		}
		buildUsername();
	});

    $("#signupform-f_name").change(function() { buildUsername(); });

    $("#signupform-l_name").change(function() { buildUsername(); });


	function buildUsername() {
		var fname = $("#signupform-f_name").val().replace(/ |\./g,"");
		var lname = $("#signupform-l_name").val().replace(/ |\./g,"");
		var priv = '';
		$("#cio_hide").hide();
		var sel_opt = document.getElementById("privilege").options;
		//console.log(sel_opt);
	
		if (sel_opt[1].selected === true) {priv = 'root'; }
		else if (sel_opt[2].selected === true) { priv = 'adm'; }
		else if (sel_opt[3].selected === true) { priv = 'rsol'; }
		else if (sel_opt[4].selected === true) { priv = 'rso';}
		else if (sel_opt[5].selected === true) { priv = 'cash'; }
		else if (sel_opt[6].selected === true) { priv = 'cal'; }
		else if (sel_opt[7].selected === true) { priv = 'wc';}
		else if (sel_opt[8].selected === true) { priv = 'cio';	$("#cio_hide").show(); }
		else if (sel_opt[9].selected === true) { priv = 'view'; }
		else if (sel_opt[10].selected === true) { alert('Do not use Member'); priv = 'Do not use Member'; }
		//else {priv_name
		//console.log(sel_opt);

		$("#signupform-username").val(priv+'.'+fname+'.'+lname);
	}
	*/
</script>

