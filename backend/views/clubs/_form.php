<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Clubs */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="clubs-form">
<?php $form = ActiveForm::begin(); ?>
<div class="row" style="margin: auto;">
	<div class="col-xs-6 col-sm-2" <?php if($model->isNewRecord){echo 'style="display: none;"';} ?> >
	<?= $form->field($model, 'club_id')->textInput(['maxlength' => true,'readonly'=>true]) ?>
	</div>
	<div class="col-xs-12 col-sm-4">
	<?= $form->field($model, 'club_name')->textInput(['maxlength' => true]) ?>
	</div>
	<div class="col-xs-12 col-sm-4">
	<?= $form->field($model, 'short_name')->textInput(['maxlength' => true]) ?>
	</div>
	<div class="col-xs-12 col-sm-6">
	<?= $form->field($model, 'avoid')->textInput(['maxlength' => true]) ?>
	</div>
	<div class="col-xs-12 col-sm-6">
	<?= $form->field($model, 'poc_email')->textInput(['maxlength' => true]) ?>
	</div>
	<div class="col-xs-12 col-sm-4">
	<?= $form->field($model, 'is_club')->dropDownList([ '0'=>'CIO', '1'=>'Club','2'=>"AGC Sponsored" ], ['prompt' => 'Choose']) ?>
	</div>
	<div class="col-xs-6 col-sm-4">
	<?= $form->field($model, 'allow_members')->dropDownList([ '0'=>'no', '1'=>'yes', ], ['value' => $model->isNewRecord ? 1 : $model->allow_members]) ?>
	</div>
	<div class="col-xs-6 col-sm-4" <?php if($model->isNewRecord){echo 'style="display: none;"';} ?> >
	<?= $form->field($model, 'status')->dropDownList([ '0'=>'Active', '1'=>'Inactive', ], ['prompt' => 'Status']) ?>
	</div>
	<?php if(yii::$app->controller->hasPermission('clubs/update')) { ?>
    <div class="form-group btn-group pull-right">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    	<?= Html::resetButton('Reset <i class="fa fa-eraser"> </i>', ['class' => 'btn btn-danger']) ?>
    </div>
<?php } ?>
</div>
<?php ActiveForm::end(); ?>
</div>
