<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\clubs;
use backend\models\Roles;

/* @var $this yii\web\View */
/* @var $model backend\models\Badge_to_Role */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="officers-form" >

    <?php $form = ActiveForm::begin(['id'=>'OfficersForm',]); ?>
	<div class="row">
    <div class="col-xs-12 col-sm-4 pull-right" >

<?php 
	if ($model->badge_number) { $bg_num = $model->badge_number; } else { $bg_num = ''; }
	$file_name = "files/badge_photos/".str_pad($bg_num, 5, '0', STR_PAD_LEFT).".jpg";
	if(file_exists($file_name)) {
		echo "<img src='/".$file_name."?dummy=".rand(10000,99999).
				"' alt='".$model->full_name."' width='260' height='340' id='photo'><br><br>";
	} else echo "<img src='/files/badge_photos/guest.jpg'  width='260' height='340' id='photo'>";
	?>
	</div>
	<div class="col-xs-12 col-sm-8" >
		<div class="row">
			<div class="col-xs-12 col-sm-2">
				<?= $form->field($model, 'badge_number')->textInput(['value'=>$model->badge_number]).PHP_EOL; ?>
			</div>
			<div class="col-xs-12 col-sm-5">
			<?= $form->field($model, 'full_name')->textInput(['readOnly'=>'true']).PHP_EOL; ?>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-7">
				<?= $form->field($model, 'role')->dropDownList((new Roles)->getRoles(), ['prompt'=>'select']).PHP_EOL; ?>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-7">
				<?= $form->field($model, 'club')->dropDownList((new clubs)->getClubList(false,false,true), ['prompt'=>'select']).PHP_EOL; ?>
			</div>
		
		</div>
		
	</div>
	<div class="col-xs-12 col-sm-8" >
		<div class="btn-group pull-right">
		<?= Html::submitButton($model->isNewRecord ? 'Save' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success done-OfficersForm' : 'btn btn-primary done-OfficersForm']) ?>
		</div>
	</div>
	</div>
	<?php ActiveForm::end(); ?>
</div>


<script>
  function getOfficerName(o_badge) {
	var csrf = $('meta[name="csrf-token"]').attr('content');
	jQuery.ajax({
		method: 'POST',
		url: '<?=yii::$app->params['rootUrl']?>/badges/get-badge-details?badge_number='+o_badge+'&rtn=0',
		data: {'_csrf-backend':csrf},
		crossDomain: false,
		success: function(responseData, textStatus, jqXHR) {
		  responseData = JSON.parse(responseData);
		  if(responseData.badge_number) {
				$("#badgetoroles-full_name").val(responseData.first_name+' '+responseData.last_name);
				document.getElementById("photo").src = "/files/badge_photos/"+("0000"+o_badge).slice(-5)+".jpg"; //?dummy="+Math.random();
				document.getElementById("photo").alt = responseData.first_name+' '+responseData.last_name;
				
			} else { 
				$("#badgetoroles-full_name").val('Not Found'); 
				document.getElementById("photo").src = "/files/badge_photos/guest.jpg";
			}
		},
		error: function (responseData, textStatus, errorThrown) {
			console.log(responseData);
		},
	});
  };
 
	$(document).ready(function (e) {
		var o_badge=$("#badgetoroles-badge_number").val();
		if (o_badge) { getOfficerName(o_badge); }
	});

    $("#badgetoroles-badge_number").change(function() {
		var o_badge=$("#badgetoroles-badge_number").val();
        if (o_badge) { getOfficerName(o_badge); }
    });
</script>