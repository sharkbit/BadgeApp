<?php

use backend\models\clubs;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true,'readOnly'=>true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'full_name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'badge_number')->textInput(['maxlength' => true]) ?>

<?php if(array_intersect([1,2],$_SESSION['privilege'])) {
		echo $form->field($model, 'privilege')->dropDownList($model->getPrivList(), ['value'=>json_decode($model->privilege),'prompt'=>'select','id'=>'privilege', 'class'=>"chosen_select", 'multiple'=>true, 'size'=>false]).PHP_EOL;
	} else {
		echo "<b>Privilege: </b> ".$model->getPrivilege_Names($model->privilege)."\n<br/><br/>";
	} ?>
	
	<?= $form->field($model, 'clubs')->dropDownList((new clubs)->getClubList(), ['prompt'=>'select','id'=>'club-id', 'class'=>"chosen_select", 'multiple'=>true, 'size'=>false]).PHP_EOL; ?>
	
<?php if (in_array(8,json_decode($model->privilege))) { echo $form->field($model, 'company')->textInput(['autofocus' => true]).PHP_EOL; } ?>

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
    } else {$("#error_msg").html('');}
  });

  $("#club-id").chosen({placeholder_text_multiple:'Choose Clubs',width: "100%"}).change(function(){
    var myCom = document.getElementById("user-company");
    if((myCom) && (!myCom.value)) {
      var selectedText = $(this).find("option:selected").text();
      myCom.value=selectedText;
    }
  });
</script>