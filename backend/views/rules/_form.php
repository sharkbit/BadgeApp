<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Params */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="params-form">
<div class="row">
	<div class="col-xs-6">
		<?php $form = ActiveForm::begin(); ?>
		
		<?= $form->field($model, 'rule_abrev')->textInput(['maxlength'=>true,'placeholder'=>'1A1']).PHP_EOL; ?>
		
		<?= $form->field($model, 'vi_type')->dropdownList(['1'=>'Class 1','2'=>'Class 2','3'=>'Class 3','4'=>'Class 4']).PHP_EOL; ?>
		
		<?= $form->field($model, 'rule_name')->textInput(['maxlength'=>true]).PHP_EOL; ?>
		
   		<?= $form->field($model, 'is_active')->DropDownList(['1'=>'Yes','0'=>'No']).PHP_EOL ?>

	    <div class="form-group">
	         <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    	</div>

	    <?php ActiveForm::end(); ?>
	</div>
</div>
</div>
