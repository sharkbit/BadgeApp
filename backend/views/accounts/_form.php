<?php

use backend\models\clubs;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\User */
/* @var $form yii\widgets\ActiveForm */

if(array_intersect([8,9],json_decode($model->privilege))) { $need_cal=true; } else { $need_cal=false;}
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true,'readOnly'=>true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'full_name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'badge_number')->textInput(['maxlength' => true]) ?>

<?php if(array_intersect([1,2],$_SESSION['privilege'])) {
		echo $form->field($model, 'privilege')->dropDownList($model->getPrivList(json_decode($model->privilege)), ['value'=>json_decode($model->privilege),'id'=>'privilege', 'class'=>"chosen_select", 'multiple'=>true, 'size'=>false]).PHP_EOL;
	} else {
		echo "<b>Privilege: </b> ".$model->getPrivilege_Names($model->privilege)."\n<br/><br/>";
	} ?>

	<div id="remote_name" <?php if(($model->r_user=='') && (!in_array(13,json_decode($model->privilege)))) {?> style="display: none;" <?php } ?>>
	<?= $form->field($model, 'r_user')->textInput(['maxlength' => true]) ?>
	</div>

	<div id="need_cal" <?php if(!$need_cal) {?> style="display: none;" <?php } ?>>
	<?= $form->field($model, 'clubs')->dropDownList((new clubs)->getClubList(), ['id'=>'club-id', 'class'=>"chosen_select", 'multiple'=>true, 'size'=>false]).PHP_EOL; ?>
	</div>
	<div id='dont_need_cal'<?php if($need_cal) {?> style="display: none;" <?php } ?>><input type='hidden' id="club-id" value=''> </div>
<?php if (($model->privilege<>'') && (in_array(8,json_decode($model->privilege)))) { echo $form->field($model, 'company')->textInput(['autofocus' => true]).PHP_EOL; } ?>

    <div class="form-group pull-right">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success pull-right' : 'btn btn-success pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<div id="error_msg"> </div>

<script src="<?=yii::$app->params['rootUrl']?>/js/chosen.jquery.min.js"></script>
<script>
  $("#privilege").chosen({placeholder_text_multiple:'Select Privilege',width: "100%"}).change(function(){
	var selectedText = " "+$(this).find("option:selected").text();
	console.log(selectedText.length);
	if ((selectedText.indexOf("Root")>0) && (selectedText.length > 5)) {
	  console.log('only root');
      $("#error_msg").html('<center><p style="color:red;"><b>Root should not have any other privilages!.</b></p></center>');
    } else if (selectedText==" ") {
	  $("#error_msg").html('<p style="color:red"><b>User Will be Deleted!</b></p>');
	} else if (selectedText.indexOf("Chairmen")>0) {
		if (selectedText.indexOf("Calendar")>0) {} else {
			$("#error_msg").html('<p style="color:red"><b>Must also be Calendar Cordinator</b></p>');
		}
	} else {
	  $("#error_msg").html('');
	}
	
	if ((selectedText.indexOf("CIO")>0) || (selectedText.indexOf("Calendar")>0)) {
		if (selectedText.indexOf("CIO")>0) { $("#cio_hide").show(); }
		$("#need_cal").show(); $("#dont_need_cal").hide();
	} else {
		$("#cio_hide").hide();
		$("#need_cal").hide();$ ("#dont_need_cal").show();
	}

	if (selectedText.indexOf("Remote Access")>0) {
		$("#remote_name").show();
		var rem_usr = document.getElementById("user-r_user").value;
		console.log(rem_usr);
		if ((!rem_usr) || (rem_usr=='')) {
			document.getElementById("user-r_user").value = document.getElementById("user-badge_number").value
		}
	} else {
		$("#remote_name").hide();
	}
  });

  $("#club-id").chosen({placeholder_text_multiple:'Choose Clubs',width: "100%"}).change(function(){
    var myCom = document.getElementById("user-company");
    if((myCom) && (!myCom.value)) {
      var selectedText = $(this).find("option:selected").text();
      myCom.value=selectedText;
    }
  });
</script>