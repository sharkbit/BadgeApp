<?php

use backend\models\Badges;
use backend\models\Params;
use backend\models\StoreItems;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\money\MaskMoney;
use kartik\widgets\DatePicker;
use kartik\widgets\DepDrop;
use kartik\widgets\DateTimePicker;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model backend\models\Legelemail */
/* @var $form yii\widgets\ActiveForm */

if(!$model->isNewRecord) {
	$model->groups = $model->getMyGroups($model->contact_id);
	//echo json_encode($model->getMyGroups($model->contact_id));
}
?>

<div class="Legelemail-form">
<?php $form = ActiveForm::begin(); ?>

<div class="row" style="margin: auto;">
	
	<div class="col-xs-5 col-sm-3">
    <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
	</div>
	<div class="col-xs-5 col-sm-2">
	<?= $form->field($model, 'middle_name')->textInput(['maxlength' => true]) ?>
	</div>
	<div class="col-xs-6 col-sm-3">
    <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
	</div>
	<div class="col-xs-9 col-sm-3">
    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
	</div>
	<div class="col-xs-3 col-sm-3">
    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
	</div>
	<div class="col-xs-6 col-sm-3">
    <?= $form->field($model, 'office')->textInput(['maxlength' => true]) ?>
	</div>
	<div class="col-xs-6 col-sm-2">
    <?= $form->field($model, 'committee')->textInput(['maxlength' => true]) ?>
	</div>
	<div class="col-xs-6 col-sm-2">
    <?= $form->field($model, 'district')->textInput(['maxlength' => true]) ?>
	</div>
	<div class="col-xs-12 col-sm-12" >
		<?php echo $form->field($model, 'groups')->dropDownList($model->getGroupList(),['multiple'=>true, 'size'=>false]). PHP_EOL; ?>
	</div>
			
	<div class="col-xs-4 col-sm-2" <?php if($model->isNewRecord){echo 'style="display: none;"';} ?> >
	<?= $form->field($model, 'is_active')->dropDownList([ '1'=>'Active', '0'=>'Inactive', ]) ?>
	</div>
	<div class="col-xs-4 col-sm-2" <?php if($model->isNewRecord){echo 'style="display: none;"';} ?> >
    <?= $form->field($model, 'display_order')->textInput(['maxlength' => true]) ?>
	</div>
	<div class="col-xs-4 col-sm-2" <?php if($model->isNewRecord){echo 'style="display: none;"';} ?> >
    <?= $form->field($model, 'date_created')->textInput(['readonly' => true]) ?>
	</div>
	<div class="col-xs-4 col-sm-2" <?php if($model->isNewRecord){echo 'style="display: none;"';} ?> >
    <?= $form->field($model, 'date_modified')->textInput(['readonly' => true]) ?>
	</div>

    <div class="form-group btn-group pull-right">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

</div>
</div>
<?php ActiveForm::end(); ?>
</div>

<script>
$(document).ready(function (e) {
  $("#legelemail-groups").select2({placeholder_text_multiple:'Choose Groups',width: "100%"});
});
 </script>
