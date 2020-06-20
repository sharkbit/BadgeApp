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
		
		<?= $form->field($model, 'item')->textInput(['maxlength'=>true]) ?>
		
	    <?= $form->field($model, 'sku')->textInput(['maxlength'=>true]) ?>
	    
		<?= $form->field($model, 'price')->textInput(['maxlength'=>true]) ?>
	    
		<?= $form->field($model, 'stock')->textInput(['maxlength'=>true]) ?>
	    
		<?= $form->field($model, 'active')->dropDownList(['1'=>'Yes','0'=>'No'],['value'=>$model->active]).PHP_EOL; ?>	
		
	    <?= $form->field($model, 'new_badge')->dropDownList(['1'=>'Yes','0'=>'No'],['value'=>$model->new_badge]).PHP_EOL; ?>	
	
	    <div class="form-group">
	        <?= Html::submitButton('Update', ['class' => 'btn btn-primary pull-right']) ?>
	    </div>

	    <?php ActiveForm::end(); ?>
	</div>
</div>
    

</div>
