<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use backend\models\Badges;
use backend\models\Event_Att;
use backend\models\User;

$model_ea = new Event_Att();

/* @var $this yii\web\View */
/* @var $model backend\models\Events */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'View Event';
$this->params['breadcrumbs'][] = ['label' => 'Event List', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title. " - ".$model->event_date. " - " .$model->event_name, 'url' => ['view','id'=>$model->ea_calendar_id ]];
$div_closed=false;
?>
<div class="events-view">
    <h2><?= Html::encode($model->event_date. " - " .$model->event_name) ?></h2>
<div class="row">
	<div class="col-xs-6 col-sm-4">
		<b>POC:</b> <?php echo "($model->poc_badge) ".yii::$app->controller->decodeBadgeName((int)$model->poc_badge).PHP_EOL; ?>
	</div>
	<div class="col-xs-6 col-sm-4">
		<b>Sponsored by:</b>  <?=$model->club_name ?>
		<?php if ($model->is_volunteer) { echo " <b>Event:</b> Volunteer  ($model->ea_hours hours)"; } ?>
	</div>
	<div class="col-xs-6 col-sm-2"><?php
	/*	if ($model->track_wristbands) {
			if (!$model->e_rso) {
				if (yii::$app->controller->hasPermission('events/approve')) {
				echo Html::button('RSO Approve <i class="fa fa-check "> </i>', ['class' => 'btn btn-success','id'=>'event_approve']).PHP_EOL;
				}
			} else {
				if ($model->e_status==0) {
					$rso=explode('|',$model->e_rso);
				} else {
					$rso=explode('|',explode('+',$model->e_rso)[0]);
				}
				echo "Approved by ".yii::$app->controller->decodeBadgeName((int)$rso[0])." at ".date('Y-m-d H:i',strtotime($rso[1]));
			}
		}*/ ?></div>
	<div class="col-xs-6 col-sm-2" id='div_closed'>
	<?php /*if ($model->e_status==0) { ?>
	<?php 	if (yii::$app->controller->hasPermission('events/close')) {
			echo Html::button('Close <i class="fa fa-times-circle "> </i>', ['class' => 'btn btn-danger','id'=>'event_close']).PHP_EOL;
			} else { echo "<b>Status:</b> Open"; }
		} else {
			$rso=explode('|',explode('+',$model->e_rso)[1]);
			if($rso[0]=='0') { 
				yii::$app->controller->createLog(true, 'trex_rso', var_export($rso,true));
				echo " it's really closed "; 
			} else {
				
				echo "Closed by ".yii::$app->controller->decodeBadgeName((int)$rso[0])." at ".date('Y-m-d H:i',strtotime($rso[1]));
			//}
		}*/ ?>
	</div>
<?php if ($model->track_wristbands) { ?>
	<div class="col-xs-8 col-sm-8"><b>Instructors:</b> <?=$model->cal_inst?> </div>
<?php } ?>
</div>
<?php if (($model->event_date == date('Y-m-d',strtotime(yii::$app->controller->getNowTime()))) && (yii::$app->controller->hasPermission('events/add-att'))) { ?>
<hr /><details> <summary><b> - - Add Attendees - - </b></summary> <section>
<div class="row">
<div class="col-xs-12">
<div class="events-attendees-form">

<?php $form = ActiveForm::begin(['id'=>'event_att']); ?>
	<?= Html::input('hidden',Yii::$app->request->csrfParam,Yii::$app->request->csrfToken)?>
	<?= $form->field($model_ea, 'track_wristbands')->hiddenInput(['value'=>$model->track_wristbands])->label(false).PHP_EOL ?>
	<?= $form->field($model_ea, 'ea_calendar_id')->hiddenInput(['value'=>$model->ea_calendar_id])->label(false).PHP_EOL ?>
<div class="row" style="margin: auto;">
	<div class="col-xs-4 col-sm-2" ><div id="badge_name"> </div> <?= $form->field($model_ea, 'ea_badge')->textInput().PHP_EOL ?> </div>

<?php if($model->allow_guests) { ?>
	<div class="col-xs-12 col-sm-1"><h2>OR</h2></div>
	<div class="col-xs-6 col-sm-2"><?= $form->field($model_ea, 'ea_f_name')->textInput().PHP_EOL; ?></div>
	<div class="col-xs-6 col-sm-2"><?= $form->field($model_ea, 'ea_l_name')->textInput().PHP_EOL; ?></div>
	<div class="col-xs-12"> <?php yii::$app->controller->getWaver();  ?> </div>
<?php if($model->track_wristbands) { ?>
	<div class="col-xs-6 col-sm-2"><?= $form->field($model_ea, 'ea_wb_serial')->textInput().PHP_EOL; ?></div>
<?php } } ?>
<div class="col-xs-3 col-sm-2" ><div class="form-group" >
	<button type="submit" id="reg_button" class="btn btn-success" onclick="jsReg();" >Register <i class="fa fa-child"> </i></button><div class="help-block" ></div></div></div>
<div class="col-xs-3 col-sm-2" ><div class="form-group" >
<button class="btn btn-primary" onclick="window.location='/events'" >Done <i class="fa fa-arrow-up"> </i></button><div class="help-block" ></div></div></div>
<hr />
</div>
<?php ActiveForm::end(); ?>
</div></div></div>
</section></details>
<div id="reg_notes"> </div>

<?php } ?>

<div class="row">
<div class='col-xs-12'>

<?php
$Attendees = Event_Att::find()->where(['ea_calendar_id'=>$model->ea_calendar_id])->orderby('ea_badge')->all();
$att_count = count($Attendees);
if($att_count>0) {
	echo "<p><b>Showing ".count($Attendees)." Attendees: </b></p>\n<div class='row'>\n";
	//array_multisort($Attendees['ea_badge']);
	foreach ($Attendees as $person) {
		echo "<div class='col-xs-6 col-sm-4'><p>";

		if($person->ea_badge>0) {
			$ba_name = yii::$app->controller->decodeBadgeName((int)$person->ea_badge);
			echo str_pad($person->ea_badge, 5, '0', STR_PAD_LEFT)." - ".$ba_name;

			if (yii::$app->controller->hasPermission('badges/barcode')) {
				if(file_exists("files/badge_photos/".str_pad($person->ea_badge, 5, '0', STR_PAD_LEFT).".jpg")) {
					//Photo Exists
					$badge = Badges::find()->where(['badge_number'=>$person->ea_badge])->one();
					if((substr($badge->qrcode, -2)==" 0") || (substr($badge->qrcode, -2)==" 1")) {
						echo " <b><a href='/badges/view?badge_number=".$person->ea_badge."'>[ <span class='glyphicon glyphicon-eye-open'></span> Update Badge ]</a></b>\n";
					}
				} else {
					if(yii::$app->controller->hasPermission('badges/photo-add')) {
						echo " <b><a href='/badges/photo-add?badge=".$person->ea_badge."'>[ <span class='glyphicon glyphicon-camera'></span> add Photo ]</a></b>\n";
					}
				}
			}
		} else {
			$ba_name = $person->ea_f_name." ".$person->ea_l_name;
			echo $ba_name;
			if ($person->ea_wb_serial) {
				echo " (WB# $person->ea_wb_serial";
				if($person->ea_wb_out) {
					if (yii::$app->controller->hasPermission('events/return')) {
						$div_closed=true;
						echo " <a href='/events/return?id=".$model->ea_calendar_id."&wb=".$person->ea_wb_serial."'>[Return]</a>)\n";
					} else { echo " Out)"; }
				} else { echo " Returned)"; }
			}
		}

		if ((yii::$app->controller->hasPermission('events/remove-att')) ) { //&& $model->e_status==0) {  //Event is Open

			echo " <a href onclick='jsRemoveAtt(".$model->ea_calendar_id.",".$person->ea_id.',"'.$ba_name."\");' class='del'>&times;</a></p></div>\n";
		} else { echo "</p></div>\n"; }
	}
	echo "</div>\n";
} else { echo "<p>No Attendees found</p>";}

?>

</div>
</div>
</div>

<style>
/* The Delete Button */
.del {
    color: red;
    //float: right;
    //font-size: 28px;
    font-weight: bold;
}

.del:hover,
.del:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}
</style>
<script>
<?php //if($div_closed) { echo "document.getElementById('div_closed').style.visibility='hidden';"; } ?>

	$('#event_att-ea_badge').on('input', function() {
		document.getElementById("event_att-ea_f_name").value='';
		document.getElementById("event_att-ea_l_name").value='';
		if (document.getElementById("event_att-ea_wb_serial") != null) {
		    document.getElementById("event_att-ea_wb_serial").value=''; }
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

	$("#event_approve").click(function() {
		if (confirm('Are you sure you want to Approve Event?')) {
		jQuery.ajax({
			method: 'POST',
			url: '<?=yii::$app->params['rootUrl']?>/events/approve?id='+<?=$model->ea_calendar_id?>,
			crossDomain: false,
			success: function(responseData, textStatus, jqXHR) {
				responseData =  JSON.parse(responseData);
				console.log(responseData);
				window.location.href = "<?=yii::$app->params['rootUrl']?>/events/view?id=<?=$model->ea_calendar_id?>";
			},
			error: function (responseData, textStatus, errorThrown) {
				console.log('e_view:213'); console.log(textStatus);
			},
		});}
	});

	$("#event_close").click(function() {
		if (confirm('Are you sure you want to Permanently Close this Event?')) {
		jQuery.ajax({
			method: 'POST',
			url: '<?=yii::$app->params['rootUrl']?>/events/close?id='+<?=$model->ea_calendar_id?>,
			crossDomain: false,
			success: function(responseData, textStatus, jqXHR) {
				responseData =  JSON.parse(responseData);
				console.log(responseData);
				window.location.href = "<?=yii::$app->params['rootUrl']?>/events/view?id=<?=$model->ea_calendar_id?>";
			},
			error: function (responseData, textStatus, errorThrown) {
				console.log('e_view:230'); console.log(textStatus);
			},
		});}
	});

	function changeBadgeNam(badgeNumber) {
		var formData = $("#event_att").serializeArray();
		$("#badge_name").html('Searching');
		jQuery.ajax({
			method: 'POST',
			url: '<?=yii::$app->params['rootUrl']?>/badges/get-badge-details?badge_number='+badgeNumber,
			data: formData,
			crossDomain: false,
			success: function(responseData, textStatus, jqXHR) {
				responseData =  JSON.parse(responseData);
				var resExpTimestamp = Math.floor(Date.now() / 1000);

				if(responseData.isExpired) {
					$("#badge_name").html('No Active Member Found');
				} else {
					$("#badge_name").html(responseData.first_name+' '+responseData.last_name);
				}
			},
			error: function (responseData, textStatus, errorThrown) {
				$("#badge_name").html('Valid Badge Holder not found');
				console.log("e_view:255"+responseData);
			},
		});
	}

	function jsRemoveAtt(ea_calendar_id,ea_id,name) {
		if (confirm('Are you sure you want to remove '+name+' from the event?')) {
			jQuery.ajax({
				method: 'POST',
				url: '<?=yii::$app->params['rootUrl']?>/events/remove-att?id='+ea_calendar_id+'&ea_id='+ea_id,
				crossDomain: false,
				success: function(responseData, textStatus, jqXHR) {
					responseData =  JSON.parse(responseData);
					console.log(responseData);
					window.location.href = "<?=yii::$app->params['rootUrl']?>/events/view?id=<?=$model->ea_calendar_id?>";
				},
				error: function (responseData, textStatus, errorThrown) {
					console.log('e_view:272'); console.log(textStatus);
				},
			});
		}
	}

	function jsReg() {
		console.log('e_view:278');
		var reg_id = document.getElementById("event_att-ea_calendar_id").value;
		if(document.getElementById("event_att-ea_badge")) { var reg_badge = document.getElementById("event_att-ea_badge").value; }

		if (reg_badge >= 1 ) {
			console.log('e_view:283');
			jQuery.ajax({
				method: 'POST',
				url: '<?=yii::$app->params['rootUrl']?>/events/reg?id='+reg_id+'&badge='+reg_badge,
				crossDomain: false,
				success: function(responseData, textStatus, jqXHR) {
					responseData =  JSON.parse(responseData);
					console.log(responseData);
					window.location.href = "<?=yii::$app->params['rootUrl']?>/events/view?id=<?=$model->ea_calendar_id?>";
				},
				error: function (responseData, textStatus, errorThrown) {
					console.log('e_view:294'); console.log(textStatus);
					$("#reg_notes").html("<p>"+textStatus+"</p>");
				},
			});
		} else {
			console.log('e_view:300');
			var f_name = document.getElementById("event_att-ea_f_name").value;
			var l_name = document.getElementById("event_att-ea_l_name").value;
			var ea_serial='';
			if((f_name) && (l_name)) {
				if(document.getElementById("event_att-track_wristbands").value==true) {
					var ea_wb_serial = document.getElementById("event_att-ea_wb_serial").value;
					if (!ea_wb_serial) {
					console.log('yes yes:308');
					alert("A Wrist Band is required for this individual.");
					return;
				}}
				if(ea_wb_serial) {ea_serial='&e_wb='+ea_wb_serial;}
				console.log('/events/reg?id='+reg_id+'&f_name='+f_name+'&l_name='+l_name+ea_serial);
				jQuery.ajax({
					method: 'POST',
					url: '<?=yii::$app->params['rootUrl']?>/events/reg?id='+reg_id+'&f_name='+f_name+'&l_name='+l_name+ea_serial,
					crossDomain: false,
					success: function(responseData, textStatus, jqXHR) {
						responseData =  JSON.parse(responseData);
						console.log(responseData);
						//window.location.href = "<?=yii::$app->params['rootUrl']?>/events/view?id=<?=$model->ea_calendar_id?>";
					},
					error: function (responseData, textStatus, errorThrown) {
						console.log('e_view:324'); console.log(textStatus);
						$("#reg_notes").html("<p>"+textStatus+"</p>");
					},
				});

			} else { alert("Please check that first and last name is specified"); }

		}
	}

</script>
