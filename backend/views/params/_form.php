<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Params */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="params-form">
<?php $form = ActiveForm::begin(); ?>
<div class="row">
	<div class="col-xs-3">
		<?= $form->field($model, 'sell_date')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3">
		<?= $form->field($model, 'guest_sku').PHP_EOL; ?>
	</div>
	<div class="col-xs-3">
		<?= $form->field($model, 'guest_total').PHP_EOL; ?>
	</div>
	<div class="col-xs-3">
		<?= $form->field($model, 'qb_env')->dropDownList(['dev'=>'Development','prod'=>'Production']) ?>
	</div>
</div>

<h3>Converge Settings</h2>
<div class="row">
	<div class="col-xs-4">
	<?= $form->field($model, 'conv_p_merc_id')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-4">
	<?= $form->field($model, 'conv_p_user_id')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-4">
	<?= $form->field($model, 'conv_p_pin')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-4">
	<?= $form->field($model, 'conv_d_merc_id')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-4">
	<?= $form->field($model, 'conv_d_user_id')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-4">
	<?= $form->field($model, 'conv_d_pin')->textInput(['maxlength'=>true]) ?>
	</div>
</div>

<h3> PayPal Settings:</h2>
<div class="row">
	<div class="col-xs-3">
	<?= $form->field($model, 'pp_id')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3">
	<?= $form->field($model, 'pp_sec')->textInput(['maxlength'=>true]) ?>
	</div>
</div>

<h3>QuickBooks oAuth 2</h2>
<div class="row">
	<div class="col-xs-6">
	<?= $form->field($model, 'qb_oauth_cust_key')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-6">
	<?= $form->field($model, 'qb_oauth_cust_sec')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-6">
	<?= $form->field($model, 'qb_oa2_id')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-6">
	<?= $form->field($model, 'qb_oa2_sec')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3">
	<?= $form->field($model, 'qb_oa2_realmId')->textInput(['disabled'=>true,'maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3">
	<?= $form->field($model, 'qb_oa2_access_token')->textInput(['disabled'=>true,'maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3">
	<?= $form->field($model, 'qb_oa2_access_date')->textInput(['disabled'=>true,'maxlength'=>true]) ?>
	</div>
	<div class="col-xs-6">
	<?= $form->field($model, 'qb_oa2_refresh_token')->textInput(['disabled'=>true,'maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3">
	<?= $form->field($model, 'qb_oa2_refresh_date')->textInput(['disabled'=>true,'maxlength'=>true]) ?>
	</div>
</div>

<h3> Old QB AUth v1</h3>
<div class="row">
	<div class="col-xs-3">
	<?= $form->field($model, 'qb_realmId')->textInput(['disabled'=>true,'maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3">
	<?= $form->field($model, 'qb_token')->textInput(['disabled'=>true,'maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3">
	<?= $form->field($model, 'qb_token_date')->textInput(['disabled'=>true,'maxlength'=>true]) ?>
	</div>
</div>

<div class="row">    
	<div class="form-group">
		<?= Html::submitButton('Update', ['class' => 'btn btn-primary pull-right']) ?>
	</div>
</div>
</div>
<?php ActiveForm::end(); ?>

</div>
