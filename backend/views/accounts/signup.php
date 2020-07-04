<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use backend\models\clubs;
use backend\models\Privileges;
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
<?php if ($_SESSION['privilege']===1) {
	echo $form->field($model, 'privilege')->dropDownList((new Privileges)->getPrivList(),['prompt'=>'Select']).PHP_EOL;
} else {
	echo $form->field($model, 'privilege')->dropDownList((new Privileges)->getPrivList(true),['prompt'=>'Select']).PHP_EOL;
} ?>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-5">
			<?= $form->field($model, 'clubs')->dropDownList((new clubs)->getClubList(), ['prompt'=>'select','id'=>'club-id', 'class'=>"chosen_select", 'multiple'=>true, 'size'=>false]).PHP_EOL; ?>
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

<?php if($_SESSION['privilege']==1) { ?>
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
<p>
<ul><li>Calendar Access not needed for Root and Admin Users</li></ul>
</p>
<script src="<?=yii::$app->params['rootUrl']?>/js/chosen.jquery.min.js"></script>
<script>
  $(".chosen_select").chosen({placeholder_text_multiple:'Choose Clubs',width: "100%"}).change(function(){
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
		var priv = $("#signupform-privilege").val();
		$("#cio_hide").hide();
		switch(priv){
			case "1": priv = 'root';	break;
			case "2": priv = 'adm';	break;
			case "3": priv = 'rso';	break;
			case "4": priv = 'view';	break;
			case "5": alert('Do not use Member'); priv = 'Do not use Member';	break;
			case "6": priv = 'RSOL';	break;
			case "7": priv = 'wc';	break;
			case "8": priv = 'cio';	$("#cio_hide").show(); break;
			case "9": priv = 'cal'; break;
			case "10": priv = 'cash'; break;
		}

		$("#signupform-username").val(priv+'.'+fname+'.'+lname);
	}
</script>

