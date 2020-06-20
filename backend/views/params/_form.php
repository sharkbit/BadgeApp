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
		
		<?= $form->field($model, 'sell_date')->textInput(['maxlength'=>true]) ?>
		<?= $form->field($model, 'guest_sku').PHP_EOL; ?>
		<?= $form->field($model, 'guest_total').PHP_EOL; ?>

	<h2> PayPal Settings:</h2>
	    <?= $form->field($model, 'pp_id')->textInput(['maxlength'=>true]) ?>
		<?= $form->field($model, 'pp_sec')->textInput(['maxlength'=>true]) ?>

	<h2> QuickBooks Settings:</h2>
		<?= $form->field($model, 'qb_env')->dropDownList(['dev'=>'Development','prod'=>'Production']) ?>
	
	<h3>QuickBooks oAuth 2</h3>
	    <?= $form->field($model, 'qb_oauth_cust_key')->textInput(['maxlength'=>true]) ?>
		<?= $form->field($model, 'qb_oauth_cust_sec')->textInput(['maxlength'=>true]) ?>
	    <?= $form->field($model, 'qb_oa2_id')->textInput(['maxlength'=>true]) ?>
		<?= $form->field($model, 'qb_oa2_sec')->textInput(['maxlength'=>true]) ?>
		<?= $form->field($model, 'qb_oa2_realmId')->textInput(['disabled'=>true,'maxlength'=>true]) ?>
		<?= $form->field($model, 'qb_oa2_access_token')->textInput(['disabled'=>true,'maxlength'=>true]) ?>
		<?= $form->field($model, 'qb_oa2_access_date')->textInput(['disabled'=>true,'maxlength'=>true]) ?>
		<?= $form->field($model, 'qb_oa2_refresh_token')->textInput(['disabled'=>true,'maxlength'=>true]) ?>
		<?= $form->field($model, 'qb_oa2_refresh_date')->textInput(['disabled'=>true,'maxlength'=>true]) ?>
	
	<h3> Old QB AUth v1</h3>
		<?= $form->field($model, 'qb_realmId')->textInput(['disabled'=>true,'maxlength'=>true]) ?>
		<?= $form->field($model, 'qb_token')->textInput(['disabled'=>true,'maxlength'=>true]) ?>
	    <?= $form->field($model, 'qb_token_date')->textInput(['disabled'=>true,'maxlength'=>true]) ?>
		
    
		<div class="form-group">
	        <?= Html::submitButton('Update', ['class' => 'btn btn-primary pull-right']) ?>
	    </div>

	    <?php ActiveForm::end(); ?>
	</div>
</div>
    

</div>
