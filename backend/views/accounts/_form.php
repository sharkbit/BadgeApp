<?php

use backend\models\clubs;
use backend\models\Privileges;
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

<?php if($_SESSION['privilege']==1) {
		echo $form->field($model, 'privilege')->dropDownList((new Privileges)->getPrivList()).PHP_EOL;
	} else {
		echo "<b>Privilege: </b> ".(new Privileges)->getPriv($model->privilege)."\n";
	} ?>
	
	<?= $form->field($model, 'clubs')->dropDownList((new clubs)->getClubList(), ['prompt'=>'select','id'=>'club-id', 'class'=>"chosen_select", 'multiple'=>true, 'size'=>false]).PHP_EOL; ?>
	
<?php if ($model->privilege==8) { echo $form->field($model, 'company')->textInput(['autofocus' => true]).PHP_EOL; } ?>
	</div>

    <div class="form-group pull-right">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success pull-right' : 'btn btn-success pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script src="<?=yii::$app->params['rootUrl']?>/js/chosen.jquery.min.js"></script>

<script>
  $(".chosen_select").chosen({placeholder_text_multiple:'Choose Clubs',width: "100%"}).change(function(){
    var myCom = document.getElementById("user-company");
    if(!myCom.value) {
      var selectedText = $(this).find("option:selected").text();
      myCom.value=selectedText;
    }
  });
</script>