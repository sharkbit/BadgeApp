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

<div class="col-xs-12 col-sm-8">

	<div class="row" <?php if($model->isNewRecord){echo 'style="display: none;"';} ?> >
	<?= $form->field($model, 'club_id')->textInput(['maxlength' => true,'readonly'=>true]) ?>
	</div>
	<div class="row">
    <?= $form->field($model, 'club_name')->textInput(['maxlength' => true]) ?>
	</div>
	<div class="row">
    <?= $form->field($model, 'short_name')->textInput(['maxlength' => true]) ?>
	</div>
	<div class="row">
	<?= $form->field($model, 'poc_email')->textInput(['maxlength' => true]) ?>
	</div>
	<div class="row">
	<?= $form->field($model, 'is_club')->dropDownList([ '0'=>'CIO Or Other', '1'=>'Yes', ], ['prompt' => 'Choose']) ?>
	</div>
	<div class="row" <?php if($model->isNewRecord){echo 'style="display: none;"';} ?> >
	<?= $form->field($model, 'status')->dropDownList([ '0'=>'Active', '1'=>'Inactive', ], ['prompt' => 'Status']) ?>
	</div>

    <div class="form-group btn-group pull-right">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    	<?= Html::resetButton('Reset <i class="fa fa-eraser"> </i>', ['class' => 'btn btn-danger']) ?>
    </div>

</div>
</div>
<?php ActiveForm::end(); ?>
</div>

