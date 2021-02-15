<?php
//use yii;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use backend\models\Events;
use backend\models\Event_Att;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;

$agc_event = Events::find()->where(['e_date' => date('Y-m-d',strtotime(yii::$app->controller->getNowTime())),'e_status'=>'0'])->andWhere(['!=', 'e_type', 'cio'])->all();
//$agc_event = Events::find()->where(['e_date' => '2020-01-02'])->all();

$urlStatus = yii::$app->controller->getCurrentUrl();
?>
<div class="site-login">
    <div class="row ">
		<div class="col-xs-12 col-md-4" >
<?php if($agc_event) { ?>
			<div class="events-box box" style="box-shadow: 3px 20px 79px #a2a2a2; padding: 15px 15px;" >
				<h3>Todays Events:</h3><hr /><ul>
<?php
foreach($agc_event as $an_event){
 //yii::$app->controller->createLog(false, 'trex_B_V_Si_Login_mem', var_export($an_event,true));
	switch ($an_event->e_type) {
		case 'cio':  $e_type="CIO Sponsored"; break;
		case 'club': $e_type="Club Sponsored"; break;
		case 'vol':  $e_type="Volunteer"; break;
	}
	echo "<li style='margin: 20px 0;'><p>$an_event->e_name ($e_type) ";
	echo "<a onclick='jsRegister(".$an_event->e_id.',"'.htmlentities($an_event->e_name, ENT_QUOTES).'","'.$an_event->e_type.'")'."' href='#'>[Register]</a></p></li>\n";
} ?>
			</div>
		<p> </p> <br />
<?php } ?>
		</div>
        <div class="col-xs-12 col-md-4" >
            <div class="login-box">
			<?= $this->render('_login-tab-menu',['model'=>$model]).PHP_EOL; ?>
				<p class="help-block help-block-error"></p>
                <?php $form = ActiveForm::begin(['id' => 'login-member-form']); ?>
				<?= Html::label("Bar Code"); ?> <span style="font-size: .85em"> (## ## ##### XX) </span>
				<div class="row " style="margin:0">

					<div class="col-xs-2" >
					<?= $form->field($model, 'barcode_c')->textInput(['autofocus' => true,'style'=>'width:34px;padding:6px 6px;','maxlength'=>'2','placeholder' => '##'])->label('').PHP_EOL; ?>
					</div><div class="col-xs-2" >
					<?= $form->field($model, 'barcode_t')->textInput(['style'=>'width:34px;padding:6px 6px;','maxlength'=>'2','placeholder' => '##'])->label('').PHP_EOL; ?>
					</div><div class="col-xs-3" >
					<?= $form->field($model, 'barcode_b')->textInput(['style'=>'width:58px;padding:6px 6px;','maxlength'=>'5','placeholder' => '#####'])->label('').PHP_EOL; ?>
					</div><div class="col-xs-2" >
					<?= $form->field($model, 'barcode_pw')->textInput(['style'=>'width:34px;padding:6px 6px;','maxlength'=>'2','placeholder' => 'XX'])->label('').PHP_EOL; ?>
					</div>
				</div>
				<?= $form->field($model, 'badge')->textInput().PHP_EOL; //passwordInput() ?>
                <div class="form-group">
                    <?= Html::submitButton('Login', ['class' => 'btn btn-primary pull-right', 'name' => 'MemberLogin-button']).PHP_EOL; ?>
                </div>
                <div class="clearfix"></div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <div class="col-xs-12 col-md-4" >
			<div style=" padding: 20px;"><p> <br /> </p>
				<div class="events-box box" style="box-shadow: 3px 20px 79px #a2a2a2; padding: 15px 15px;" >
				<p> </p> <center><h3><a href="/site/new-member">New Member Signup</a></h3></center>
				</div>
			</div>
		</div>
    </div>
</div>


<?php if($agc_event) {
	$event_model = New Event_Att;
?>
<!-- The Modal -->
<div id="myModal" class="modal">

  <!-- Modal content -->
  <div class="modal-content">
   <?php $formR = ActiveForm::begin(['id' => 'Event-Reg-form']); ?>
    <span class="close">&times;</span>
    <div class="row">
		<input type="hidden" id="event_id" />
		<p id='event_name'>Regester for: </p>

		<div id='reg_form' ><p id="event_notes"> </p>
			<div class="col-xs-6 col-sm-2"><?= $formR->field($event_model, 'ea_badge')->textInput().PHP_EOL; ?></div>
		</div>

		<div class="col-xs-6 col-sm-2"><p id="badge_name"> </p><br />
		</div>
		<div id="by_name" style="display:none;">
		<div class="col-xs-12 col-sm-1"><h2>OR</h2></div>
		<div class="col-xs-6 col-sm-2"><?= $formR->field($event_model, 'ea_f_name')->textInput().PHP_EOL; ?></div>
		<div class="col-xs-6 col-sm-2"><?= $formR->field($event_model, 'ea_l_name')->textInput().PHP_EOL; ?></div>
		<div class="col-xs-6 col-sm-2" id="e_serial" style="display:none;"><?= $formR->field($event_model, 'ea_wb_serial')->textInput().PHP_EOL; ?></div>
		</div>
	</div>
	<div class="row"><div id='reg_notes'> </div>
		<div class="col-xs-2">
		<button id="reg_button" type="submit" class="btn btn-success" onclick="jsReg();" >Register <i class="fa fa-child"> </i></button>
		</div>

		<?php ActiveForm::end(); ?>
	</div>
  </div>

</div>

<style>
/* The Modal (background) */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    padding-top: 100px; /* Location of the box */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
}

/* The Close Button */
.close {
    color: #aaaaaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}
</style>
<?php } ?>
<script>
	function ProcessSwipe(cleanUPC) {
		if (cleanUPC.indexOf(' ') > 0) {
			var scanBadge = cleanUPC.split(" ");
			document.getElementById("loginmemberform-barcode_c").value = scanBadge[0];
			document.getElementById("loginmemberform-barcode_t").value = scanBadge[1];
			document.getElementById("loginmemberform-barcode_b").value = scanBadge[2];
			document.getElementById("loginmemberform-barcode_pw").value = scanBadge[3];
			document.getElementById("loginmemberform-badge").value = scanBadge[2];
			document.getElementById("login-member-form").submit();
		}
		newUPC = '';
	};

	document.getElementById("loginmemberform-barcode_c").addEventListener("keyup",function(e){
		if($(this).val().length==2) { $("#loginmemberform-barcode_t").focus(); }
	});

	document.getElementById("loginmemberform-barcode_t").addEventListener("keyup",function(e){
		if($(this).val().length==2) { $("#loginmemberform-barcode_b").focus(); }
		if($(this).val().length == 0 && e.which == 8) { document.getElementById("loginmemberform-barcode_c").focus(); }
	});

	document.getElementById("loginmemberform-barcode_b").addEventListener("keyup",function(e){
		if($(this).val().length==5) { $("#loginmemberform-barcode_pw").focus(); }
		if($(this).val().length == 0 && e.which == 8) { document.getElementById("loginmemberform-barcode_t").focus(); }
	});

	document.getElementById("loginmemberform-barcode_pw").addEventListener("keyup",function(e){
		if($(this).val().length==2) { $("#loginmemberform-badge").focus(); }
		if($(this).val().length == 0 && e.which == 8) { document.getElementById("loginmemberform-barcode_b").focus(); }
	});

	document.getElementById("loginmemberform-badge").addEventListener("keyup",function(e){
		if($(this).val().length == 0 && e.which == 8) { document.getElementById("loginmemberform-barcode_pw").focus(); }
	});

<?php if($agc_event) { ?>
	$('#event_att-ea_badge').on('input', function() {
		document.getElementById("event_att-ea_f_name").value='';
		document.getElementById("event_att-ea_l_name").value='';
		document.getElementById("event_att-ea_wb_serial").value='';
		var badgeNumber = $(this).val();
		if((badgeNumber!='') && (badgeNumber!=0)) {
			changeBadgeNam(badgeNumber);
		} else {
		 $("#badge_name").html('');
		}
	});

	$('#event_att-ea_f_name').on('input', function() {
		$("#badge_name").html(''); 
		document.getElementById("event_att-ea_badge").value='';
	});

	$('#event_att-ea_l_name').on('input', function() {
		$("#badge_name").html(''); 
		document.getElementById("event_att-ea_badge").value='';
	});
	$('#event_att-ea_wb_serial').on('input', function() {
		$("#badge_name").html(''); 
		document.getElementById("event_att-ea_badge").value='';
	});

	var modal = document.getElementById('myModal');
	var span = document.getElementsByClassName("close")[0];
	var reg_sub = document.getElementsByClassName("btn-success")[0];

	function jsRegister(r_id,r_name,r_type) {

		modal.style.display = "block";
		document.getElementById("event_att-ea_badge").value='';
		document.getElementById("event_att-ea_f_name").value='';
		document.getElementById("event_att-ea_l_name").value='';
		document.getElementById("event_att-ea_wb_serial").value='';
		$("div#reg_notes").html('');
		$("#badge_name").html(' ');

		document.getElementById("event_id").value = r_id;

		switch(r_type) {
		case 'cio':
			var event_type="(CIO Event)";
			var reg_html="<ul><li>Enter Badger Number <b>Or</b> First and Last Name.</li><ul>";
			$("#by_name").show();
			$("#e_serial").show();
			break;
		case 'club':
			var event_type="(Club Sponsored Event)";
			var reg_html="<ul><li>Enter Badger Number <b>Or</b> First and Last Name.</li><ul>";
			$("#by_name").show();
			$("#e_serial").hide();
			break;
		case 'vol':
			var event_type="(AGC Volunteer Event)";
			var reg_html="<ul><li>AGC Volunteer Events are Range Members only.</li><ul>";
			$("#by_name").hide();
			$("#e_serial").hide();
			break;
		}
		$("p#event_name").html("Regester for: <b>"+r_name+"</b> "+event_type);
		$("p#event_notes").html(reg_html);
	}

	// When the user clicks on <span> (x), close the modal
	span.onclick = function() {
		modal.style.display = "none";
	}

	$("#reg_button").click(function(e) {
		e.preventDefault();
	});

	function jsReg() {
		console.log('here 192');
		var reg_id = document.getElementById("event_id").value;
		if(document.getElementById("event_att-ea_badge")) { var reg_badge = document.getElementById("event_att-ea_badge").value; }

		if(reg_badge > 1 ) {
			jQuery.ajax({
				method: 'POST',
				url: '<?=yii::$app->params['rootUrl']?>/events/reg?id='+reg_id+'&badge='+reg_badge,
				crossDomain: false,
				success: function(responseData, textStatus, jqXHR) {
					responseData =  JSON.parse(responseData);
					console.log(responseData);
					$("div#reg_notes").html("<p>"+responseData.msg+"</p>");
				},
				error: function (responseData, textStatus, errorThrown) {
					console.log('login_member:207'); console.log(textStatus);
					$("div#reg_notes").html("<p>"+textStatus+"</p>");
				},
			});
		} else {
			var f_name = document.getElementById("event_att-ea_f_name").value;
			var l_name = document.getElementById("event_att-ea_l_name").value;
			var ea_serial='';
			if((f_name) && (l_name)) {
				var ea_wb_serial = document.getElementById("event_att-ea_wb_serial").value;
				if(ea_wb_serial) {ea_serial='&e_wb='+ea_wb_serial;}
				console.log('/events/reg?id='+reg_id+'&f_name='+f_name+'&l_name='+l_name+ea_serial);
				jQuery.ajax({
					method: 'POST',
					url: '<?=yii::$app->params['rootUrl']?>/events/reg?id='+reg_id+'&f_name='+f_name+'&l_name='+l_name+ea_serial,
					crossDomain: false,
					success: function(responseData, textStatus, jqXHR) {
						responseData =  JSON.parse(responseData);
						console.log(responseData);
						$("div#reg_notes").html("<p>"+responseData.msg+"</p>");
					},
					error: function (responseData, textStatus, errorThrown) {
						console.log('login_member:226'); console.log(textStatus);
						$("div#reg_notes").html("<p>"+textStatus+"</p>");
					},
				});

			} else { alert("Please check that first and last name is specified"); }

		}
	}

	// When the user clicks anywhere outside of the modal, close it
	window.onclick = function(event) {
		if (event.target == modal) {
			modal.style.display = "none";
		}
	}

	$('#event_att-ea_badge').on('input', function() {
		var badgeNumber = $(this).val();
		if((badgeNumber!='') && (badgeNumber!=0)) {
            changeBadgeNam(badgeNumber);
        } else {
          $("#badge_name").html(' ');
        }
	});

    function changeBadgeNam(badgeNumber) {
		$("#badge_name").html('Searching');
		jQuery.ajax({
			method: 'GET',
			url: '<?=yii::$app->params['rootUrl']?>/badges/get-badge-name?badge_number='+badgeNumber,
			crossDomain: false,
			success: function(responseData, textStatus, jqXHR) {
				responseData =  JSON.parse(responseData);
				if(responseData.success==true) {
					var PrimeExpTimestamp = getTimestamp(responseData.expires);
					var resExpTimestamp = Math.floor(Date.now() / 1000);

					if(PrimeExpTimestamp < resExpTimestamp) {
						$("#badge_name").html('No Active Member Found');
					} else {
						$("#badge_name").html(responseData.first_name+' '+responseData.last_name);
					}
				} else {$("#badge_name").html('Valid Badge holder not found');}
			},
			error: function (responseData, textStatus, errorThrown) {
				$("#badge_name").html('Valid Badge holder not found');
				console.log("fail "+responseData);
			},
		});
    }

<?php } ?>
</script>