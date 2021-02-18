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
		
		<?php if ($model->isNewRecord) {
			echo $form->field($model, 'type')->dropDownList($model->getTypes(),['prompt'=>$model->isNewRecord ? 'Select': '']).PHP_EOL;
		} else {
			if ($model->type=='Category'){
				echo $form->field($model, 'type')->textInput(['readonly'=>true,'maxlength'=>true]);
			} else {
				echo $form->field($model, 'type')->dropDownList($model->getTypes(true)).PHP_EOL;
			}
		} ?>

		<?php if($model->type!='Category'){echo $form->field($model, 'paren')->dropDownList($model->getGroups()).PHP_EOL; } ?>	
		
		<?= $form->field($model, 'item')->textInput(['maxlength'=>true])->label("Item Name") ?>
		
	    <?= $form->field($model, 'sku')->textInput(['readonly'=>($model->type=='Category')? true:false,'maxlength'=>true]) ?>
	    
		<?= $form->field($model, 'price')->textInput(['readonly'=>($model->type=='Category')? true:false,'maxlength'=>true]) ?>
	    
		<?= $form->field($model, 'stock')->textInput(['placeholder'=>'Optional','readonly'=>($model->type=='Category')? true:false,'maxlength'=>true]) ?>
	    
		<?= $form->field($model, 'active')->dropDownList(['1'=>'Yes','0'=>'No'],['readonly'=>($model->type=='Category')? true:false,'value'=>$model->active]).PHP_EOL; ?>	
		
	    <?= $form->field($model, 'new_badge')->dropDownList(['1'=>'Yes','0'=>'No'],['readonly'=>($model->type=='Category')? true:false,'value'=>$model->new_badge]).PHP_EOL; ?>	
	
	    <div class="form-group">
	        <?= Html::submitButton('Update', ['class' => 'btn btn-primary pull-right']) ?>
	    </div>

	    <?php ActiveForm::end(); ?>
	</div>
</div>
</div>

<script>
 $("#storeitems-type").change(function() {
	 console.log('h1');
        var myType = document.getElementById("storeitems-type").value;
        if(myType=="Category") {
			console.log('h2');
            document.getElementsByClassName("field-storeitems-paren")[0].style.display = 'none';
			document.getElementById("storeitems-sku").value = null;
			document.getElementById("storeitems-sku").readOnly = true;
			document.getElementById("storeitems-price").value = null;
			document.getElementById("storeitems-price").readOnly = true;
			document.getElementById("storeitems-stock").value = null;
			document.getElementById("storeitems-stock").readOnly = true;
			document.getElementById("storeitems-active").readOnly = true;
			document.getElementById("storeitems-new_badge").readOnly = true;
		} else {
			document.getElementsByClassName("field-storeitems-paren")[0].style.display = '';
			document.getElementById("storeitems-sku").readOnly = false;
			document.getElementById("storeitems-price").readOnly = false;
			document.getElementById("storeitems-stock").readOnly = false;
			document.getElementById("storeitems-active").readOnly = false;
			document.getElementById("storeitems-new_badge").readOnly = false;
		}
 });
</script>
