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
$this->params['breadcrumbs'][] = ['label' => $this->title. " - ".$model->e_date. " - " .$model->e_name, 'url' => ['view','id'=>$model->e_id ]];
$div_closed=false;
?>
<div class="events-view">
    <h2><?= Html::encode($model->e_date. " - " .$model->e_name) ?></h2>
<div class="row">
	<div class="col-xs-6 col-sm-4">
		<b>POC:</b> <?php echo "($model->e_poc) ".yii::$app->controller->decodeBadgeName((int)$model->e_poc).PHP_EOL; ?>
	</div>
	<div class="col-xs-6 col-sm-4">

<?php	switch ($model->e_type) {
			case 'cio':
				$user = User::find()->where(['badge_number'=>$model->e_poc])->one();
				if(isset($user->company)) {$company = $user->company; } else { $company = 'Non CIO'; }
				echo "<b>Sponsored by:</b> ".$company; break;
				case 'club': echo "<b>Event:</b> Club Sponsored"; break;
				case 'vol':  echo "<b>Event:</b> Volunteer  ($model->e_hours hours)"; break;
} ?>
	</div>
	<div class="col-xs-6 col-sm-2"><?php
		if ($model->e_type=='cio') {
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
		} ?></div>
	<div class="col-xs-6 col-sm-2" id='div_closed'>
	<?php if ($model->e_status==0) { ?>
	<?php 	if (yii::$app->controller->hasPermission('events/close')) {
			echo Html::button('Close <i class="fa fa-times-circle "> </i>', ['class' => 'btn btn-danger','id'=>'event_close']).PHP_EOL;
			} else { echo "<b>Status:</b> Open"; }
		} else {
			$rso=explode('|',explode('+',$model->e_rso)[1]);
			echo "Closed by ".yii::$app->controller->decodeBadgeName((int)$rso[0])." at ".date('Y-m-d H:i',strtotime($rso[1]));
		} ?>
	</div>
<?php if ($model->e_type=='cio') { ?>
	<div class="col-xs-8 col-sm-8"><b>Instructors:</b> <?=$model->e_inst?> </div>
<?php } ?>
</div><hr />
<?php if (($model->e_date == date('Y-m-d',strtotime(yii::$app->controller->getNowTime()))) && ($model->e_status==0))  {
if ($model->e_type=='cio' && ($_SESSION['privilege']==3 || $_SESSION['privilege']==6 )) { } else { ?>
<div class="row">
<div class="col-xs-12">
<div class="events-attendees-form">

<?php $form = ActiveForm::begin(['id'=>'event_att']); ?>
	<?= $form->field($model_ea, 'ea_event_id')->hiddenInput(['value'=>$model->e_id])->label(false).PHP_EOL ?>
	<?= $form->field($model_ea, 'ea_type')->hiddenInput(['value'=>$model->e_type])->label(false).PHP_EOL ?>
<div class="row" style="margin: auto;">
	<div class="col-xs-4 col-sm-2" ><div id="badge_name"> </div> <?= $form->field($model_ea, 'ea_badge')->textInput().PHP_EOL ?> </div>

<?php if(($model->e_type=='club') || ($model->e_type=='cio')) { ?>
	<div class="col-xs-12 col-sm-1"><h2>OR</h2></div>
	<div class="col-xs-6 col-sm-2"><?= $form->field($model_ea, 'ea_f_name')->textInput().PHP_EOL; ?></div>
	<div class="col-xs-6 col-sm-2"><?= $form->field($model_ea, 'ea_l_name')->textInput().PHP_EOL; ?></div>
<?php if($model->e_type=='cio') { ?>
	<div class="col-xs-6 col-sm-2"><?= $form->field($model_ea, 'ea_wb_serial')->textInput().PHP_EOL; ?></div>
<?php } } ?>
<div class="col-xs-3 col-sm-2" ><div class="form-group" >
	<button type="submit" id="reg_button" class="btn btn-success" onclick="jsReg();" >Register <i class="fa fa-child"> </i></button><div class="help-block" ></div></div></div>
<div class="col-xs-3 col-sm-2" ><div class="form-group" >
<button class="btn btn-primary" onclick="window.location='/events'" >Done <i class="fa fa-arrow-up"> </i></button><div class="help-block" ></div></div></div>

</div>
<?php ActiveForm::end(); ?>
</div></div></div>
<div id="reg_notes"> </div>
<hr />
<?php } } ?>

<div class="row">
<div class='col-xs-12'>

<?php
$Attendees = Event_Att::find()->where(['ea_event_id'=>$model->e_id])->orderby('ea_badge')->all();
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
						echo " <a href='/events/return?id=".$model->e_id."&wb=".$person->ea_wb_serial."'>[Return]</a>)\n";
					} else { echo " Out)"; }
				} else { echo " Returned)"; }
			}
		}

		if ((yii::$app->controller->hasPermission('events/remove-att')) && $model->e_status==0) {  //Event is Open

			echo " <a href onclick='jsRemoveAtt(".$model->e_id.",".$person->ea_id.',"'.$ba_name."\");' class='del'>&times;</a></p></div>\n";
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
<?php if($div_closed) { echo "document.getElementById('div_closed').style.visibility='hidden';"; } ?>

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

	$("#event_approve").click(function() {
		if (confirm('Are you sure you want to Approve Event?')) {
		jQuery.ajax({
			method: 'POST',
			url: '<?=yii::$app->params['rootUrl']?>/events/approve?id='+<?=$model->e_id?>,
			crossDomain: false,
			success: function(responseData, textStatus, jqXHR) {
				responseData =  JSON.parse(responseData);
				console.log(responseData);
				window.location.href = "<?=yii::$app->params['rootUrl']?>/events/view?id=<?=$model->e_id?>";
			},
			error: function (responseData, textStatus, errorThrown) {
				console.log('e_view:171'); console.log(textStatus);
			},
		});}
	});

	$("#event_close").click(function() {
		if (confirm('Are you sure you want to Perminatly Close this Event?')) {
		jQuery.ajax({
			method: 'POST',
			url: '<?=yii::$app->params['rootUrl']?>/events/close?id='+<?=$model->e_id?>,
			crossDomain: false,
			success: function(responseData, textStatus, jqXHR) {
				responseData =  JSON.parse(responseData);
				console.log(responseData);
				window.location.href = "<?=yii::$app->params['rootUrl']?>/events/view?id=<?=$model->e_id?>";
			},
			error: function (responseData, textStatus, errorThrown) {
				console.log('e_view:188'); console.log(textStatus);
			},
		});}
	});

	function changeBadgeNam(badgeNumber) {
		$("#badge_name").html('Searching');
		jQuery.ajax({
			method: 'GET',
			url: '<?=yii::$app->params['rootUrl']?>/badges/get-badge-details?badge_number='+badgeNumber,
			crossDomain: false,
			success: function(responseData, textStatus, jqXHR) {
				responseData =  JSON.parse(responseData);
				var PrimeExpTimestamp = getTimestamp(responseData.expires);
				var resExpTimestamp = Math.floor(Date.now() / 1000);

				if(PrimeExpTimestamp < resExpTimestamp) {
					$("#badge_name").html('No Active Member Found');
				} else {
					$("#badge_name").html(responseData.first_name+' '+responseData.last_name);
				}
			},
			error: function (responseData, textStatus, errorThrown) {
				$("#badge_name").html('Valid Badge holde not found');
				console.log("e_view:212"+responseData);
			},
		});
	}

	function jsRemoveAtt(e_id,ea_id,name) {
		if (confirm('Are you sure you want to remove '+name+' from the event?')) {
			jQuery.ajax({
				method: 'POST',
				url: '<?=yii::$app->params['rootUrl']?>/events/remove-att?id='+e_id+'&ea_id='+ea_id,
				crossDomain: false,
				success: function(responseData, textStatus, jqXHR) {
					responseData =  JSON.parse(responseData);
					console.log(responseData);
					window.location.href = "<?=yii::$app->params['rootUrl']?>/events/view?id=<?=$model->e_id?>";
				},
				error: function (responseData, textStatus, errorThrown) {
					console.log('e_view:229'); console.log(textStatus);
				},
			});
		}
	}

	function jsReg() {
		console.log('e_view:236');
		var reg_id = document.getElementById("event_att-ea_event_id").value;
		if(document.getElementById("event_att-ea_badge")) { var reg_badge = document.getElementById("event_att-ea_badge").value; }

		if (reg_badge >= 1 ) {
			console.log('e_view:241');
			jQuery.ajax({
				method: 'POST',
				url: '<?=yii::$app->params['rootUrl']?>/events/reg?id='+reg_id+'&badge='+reg_badge,
				crossDomain: false,
				success: function(responseData, textStatus, jqXHR) {
					responseData =  JSON.parse(responseData);
					console.log(responseData);
					window.location.href = "<?=yii::$app->params['rootUrl']?>/events/view?id=<?=$model->e_id?>";
				},
				error: function (responseData, textStatus, errorThrown) {
					console.log('e_view:252'); console.log(textStatus);
					$("#reg_notes").html("<p>"+textStatus+"</p>");
				},
			});
		} else {
			console.log('e_view:257');
			var f_name = document.getElementById("event_att-ea_f_name").value;
			var l_name = document.getElementById("event_att-ea_l_name").value;
			var ea_serial='';
			if((f_name) && (l_name)) {
				var ea_wb_serial = document.getElementById("event_att-ea_wb_serial").value;

				if(document.getElementById("event_att-ea_type").value=='cio' && (!ea_wb_serial)) {
					console.log('yes yes:300');
					alert("A Wrist Band is required for this individule.");
					return;
				}
				if(ea_wb_serial) {ea_serial='&e_wb='+ea_wb_serial;}
				console.log('/events/reg?id='+reg_id+'&f_name='+f_name+'&l_name='+l_name+ea_serial);
				jQuery.ajax({
					method: 'POST',
					url: '<?=yii::$app->params['rootUrl']?>/events/reg?id='+reg_id+'&f_name='+f_name+'&l_name='+l_name+ea_serial,
					crossDomain: false,
					success: function(responseData, textStatus, jqXHR) {
						responseData =  JSON.parse(responseData);
						console.log(responseData);
						//window.location.href = "<?=yii::$app->params['rootUrl']?>/events/view?id=<?=$model->e_id?>";
					},
					error: function (responseData, textStatus, errorThrown) {
						console.log('e_view:275'); console.log(textStatus);
						$("#reg_notes").html("<p>"+textStatus+"</p>");
					},
				});

			} else { alert("Please check that first and last name is specified"); }

		}
	}

</script>
