<?php

use backend\controllers\AdminController;
use backend\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

if(strpos(" ".$model->mass_to,'@')) {
	$model->to_single=true;
	$model->to_email=$model->mass_to;
} else {
	if (strpos(" ".$model->mass_to, '*A')) { $model->to_active=true; }
	if (strpos(" ".$model->mass_to, '*E')) { $model->to_expired=true; }
}
if($model->mass_to_users<>'') {
	$model->to_users=true;
	$model->mass_to_users = json_decode($model->mass_to_users);
}

$form = ActiveForm::begin(['id'=>'email']); ?>

<div class="row">
     <div class="col-xs-12">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
</div>

<div class="row" style="background-color: AliceBlue;  border: 3px solid black;  border-spacing: 5px;">
	<div class="col-xs-12 col-sm-2" >
		<?=Html::label("Email To:")?>
	</div>
	<div class="col-xs-6 col-sm-2" >
		<?= $form->field($model, 'to_active')->checkbox() ?>
	</div>
	<div class="col-xs-6 col-sm-2" style="background-color: WhiteSmoke;">
		<?= $form->field($model, 'to_expired')->checkbox() ?>
	</div>
	<div class="col-xs-6 col-sm-2" >
		<?= $form->field($model, 'to_users')->checkbox() ?>
	</div>
	<div class="col-xs-6 col-sm-2" >
		<?= $form->field($model, 'to_single')->checkbox() ?>
	</div>
	<div class="col-xs-12 col-sm-6" id="to_usr" >
		<?=$form->field($model, 'mass_to_users')->dropDownList((new User)->getPrivList(), ['value'=>$model->mass_to_users,'prompt'=>'select','id'=>'mass_to_users', 'class'=>"chosen_select", 'multiple'=>true, 'size'=>false]).PHP_EOL; ?>
	</div>
	<div class="col-xs-12 col-sm-6" id="to_email_addr" >
		<?= $form->field($model, 'to_email')->textInput(['placeholder'=>'one@email.com; two@emails.com'])->label('To Email(s):  seperated by ;') ?>
	</div>
</div>
<p> </p>
<div class="row">
	<div class="col-xs-12" id="email_err"> </div>
	<div class="col-xs-12" >
		<?= $form->field($model, 'mass_reply_to')->textInput(['placeholder'=>'president@AGCrange.org']) ?>
	</div>
	<div class="col-xs-12" >
	 <?= $form->field($model, 'mass_subject')->textInput(['placeholder'=>'Required']) ?>
	</div>
</div>

<div class="row">
	<div class="col-xs-12 col-sm-6" > <!-- style="background-color:grey; padding:10px;" > -->
<?php if(!$model->mass_body) { $model->mass_body="<p>put email here</p>
thanks<br />
Marc";}
	echo $form->field($model, 'mass_body')->textarea(['rows' => '9']).PHP_EOL; ?>

	</div>
	<div class="col-xs-12 col-sm-6" style="width=100%; height=100%; padding:10px; background-color: lightgreen;" >
		<p><b>Preview:</b></p><p>Hello {Member Name},</p>
		<div id='email_prev' style="margin:8px; background-color:white">
<?=$model->mass_body?>
		</div>
	</div>
</div>
<p> </p>
<div class="row">

	<div class="col-xs-12 col-sm-6">
<?php if ($model->mass_running==0) {
		echo Html::submitButton('<i class="fa fa-envelope "> Save </i>', ['id'=>'email_save','class' => 'btn btn-primary']), PHP_EOL;
		echo "&nbsp;  &nbsp; ";
		if (!$model->isNewRecord) { echo Html::Button('<i class="fa fa-envelope "> Send Emails</i>', ['id'=>'email_send','class' => 'btn btn-danger']), PHP_EOL; }
	} else {

		echo "<div class='col-xs-6'> badge: $model->mass_lastbadge @ $model->mass_runtime<div class='help-block' ></div></div>".PHP_EOL;
		if ($model->mass_running==1) {

			$date_start = new DateTime($model->mass_start);
			$since_start = $date_start->diff(new DateTime(yii::$app->controller->getNowTime()));

			$date_running = new DateTime($model->mass_runtime);
			$since_running = $date_running->diff(new DateTime(yii::$app->controller->getNowTime()));

			if ($since_running->i > 15) {
				echo Html::Button('<i class="fa fa-envelope "> Restart Emails</i>', ['id'=>'email_send','class' => 'btn btn-danger']), PHP_EOL;
				echo " Restart Needed! ".$since_running->h.' hours '.$since_running->i.' minutes ';
			}

			echo "<div class='col-xs-6 pull-right'><b>Processing...</b> ". $since_start->h.' hours '.$since_start->i.' minutes'."<div class='help-block' ></div></div>".PHP_EOL;
		} else {
			echo "<div class='col-xs-6 pull-right'><b>Message Was Sent</b>: ".$model->mass_finished." <div class='help-block' ></div></div>".PHP_EOL;
			echo Html::submitButton('<i class="fa fa-envelope "> Save </i>', ['id'=>'email_save','class' => 'btn btn-primary']), PHP_EOL;
			echo Html::Button('<i class="fa fa-envelope "> Re-Send Emails</i>', ['id'=>'email_resend','class' => 'btn btn-danger']), PHP_EOL;
		}
	} ?>

<?php // = Html::submitButton( '<i class="fa fa-refresh" aria-hidden="true"></i> RENEW BADGE', ['class' => 'btn btn-primary pull-right', 'id' => 'renew_btn']).PHP_EOL; ?>
	</div>
	<div class="col-xs-12 col-sm-6">
		<p id='email_info'> </p>
	</div>
</div>
<?php ActiveForm::end(); ?>

<script src="<?=yii::$app->params['rootUrl']?>/js/chosen.jquery.min.js"></script>
<script>

function validateEmail(email) {
  const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(email);
}

$("#mass_to_users").chosen({placeholder_text_multiple:'Select User Roll',width: "100%"})

	if (!document.getElementById("massemail-to_single").checked) {$("#to_email_addr").hide(); }
	if (!document.getElementById("massemail-to_users").checked) {$("#to_usr").hide(); }

	var area = document.getElementById('massemail-mass_body');
	if (area.addEventListener) {
	  area.addEventListener('input', function() {
		var ex=document.getElementById('email_prev');
		ex.innerHTML = $("#massemail-mass_body").val();
	  }, false);
	}

$("#massemail-to_single").change(function(event) {
	console.log('to_single');
    var checkbox = event.target;
    if (checkbox.checked) {
		$("#to_email_addr").show();
		document.getElementById("massemail-to_active").checked=false;
		document.getElementById("massemail-to_expired").checked=false;
    } else {$("#to_email_addr").hide();}
});

$("#massemail-to_active").change(function(event) {
	console.log('to_active');
	var checkbox = event.target;
    if (checkbox.checked) {
		$("#to_email_addr").hide();
		$("#to_usr").hide();
		document.getElementById("massemail-to_single").checked=false;
		document.getElementById("massemail-to_users").checked=false;
		document.getElementById("massemail-to_email").value = '';
    }
});

$("#massemail-to_expired").change(function(event) {
	console.log('to_expired');
	var checkbox = event.target;
    if (checkbox.checked) {
		$("#to_email_addr").hide();
		$("#to_usr").hide();
		document.getElementById("massemail-to_single").checked=false;
		document.getElementById("massemail-to_users").checked=false;
		document.getElementById("massemail-to_email").value = '';
    }
});

$("#massemail-to_users").change(function(event) {
	console.log('to_users');
    var checkbox = event.target;
    if (checkbox.checked) {
		$("#to_usr").show();
		document.getElementById("massemail-to_active").checked=false;
		document.getElementById("massemail-to_expired").checked=false;
    } else {$("#to_usr").hide();}
});

$("#massemail-to_email").change(function(e) {
	var to_email = $("#massemail-to_email")
	emails= to_email.val().replace(/(;$)/g, "").split(/,|;/);
	
	var good_emails = ''; var bad_emails='';
	emails.forEach(function (addr) {
		console.log(addr);
		if (validateEmail(addr.trim())) {
			good_emails += addr.trim()+';';
		} else {
			bad_emails += addr.trim()+';';
		}
	});
	to_email.val(good_emails.slice(0,-1));
	if (bad_emails != '') {$("#email_err").html('<b  style="color:red;">Email not valid: '+bad_emails.slice(0,-1)+'</b><br><br>');} else {$("#email_err").html('');}
	console.log("good:"+good_emails+", Bad emailz: "+bad_emails);
	
});

$("#email_send").click(function() {

	if($("#massemail-mass_subject").val().length < 2) {
		$("p#email_info").html("<b>Check Message Subject.</b>"); return; }
	if(document.getElementById("massemail-to_active").checked==false && 
		document.getElementById("massemail-to_expired").checked==false && 
		document.getElementById("massemail-to_users").checked==false && 
		document.getElementById("massemail-to_single").checked==false) {
			$("p#email_info").html("<b>No Email Groupd Selected.</b>"); return; }

    document.getElementById("email_send").disabled=true;
	$("p#email_info").html("Processing...");

	var formData = $("#email").serializeArray();
	jQuery.ajax({
		method: 'POST',
		crossDomain: false,
		data: formData,
		dataType: 'json',
		url: '<?=yii::$app->params['rootUrl']?>/mass-email/send?id=<?=$model->id ?>',
		success: function(responseData, textStatus, jqXHR) {
			console.log(responseData.msg);
			$("p#email_info").html(responseData.msg);
		},
        error: function (responseData, textStatus, errorThrown) {
			console.log(responseData.responseText);
			$("p#email_info").html(responseData.responseText);
			document.getElementById("email_send").disabled=false;
		}
	});
});

$("#email_resend").click(function() {

	if($("#massemail-mass_subject").val().length < 2) {
		$("p#email_info").html("<b>Check Message Subject.</b>"); return; }
    document.getElementById("email_resend").disabled=true;
	$("p#email_info").html("Processing...");

	var formData = $("#email").serializeArray();
	jQuery.ajax({
		method: 'POST',
		crossDomain: false,
		data: formData,
		dataType: 'json',
		url: '<?=yii::$app->params['rootUrl']?>/mass-email/send?id=<?=$model->id?>&resend=1',
		success: function(responseData, textStatus, jqXHR) {
			console.log(responseData.msg);
			$("p#email_info").html(responseData.msg);
		},
        error: function (responseData, textStatus, errorThrown) {
			console.log(responseData.responseText);
			$("p#email_info").html(responseData.responseText);
		}
	});
	document.getElementById("email_resend").disabled=false;
});

</script>