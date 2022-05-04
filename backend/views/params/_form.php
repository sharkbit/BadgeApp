<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Params */
/* @var $form yii\widgets\ActiveForm */

function MakeDropdown($dlist) {
	$cnt=0;
	$rlist=[];
	$whitelist = json_decode($dlist);
	if($whitelist) {
		sort($whitelist);
		foreach ($whitelist as $item) {
			if($cnt==0) { $rlist = [$item=>$item];
			} else { $rlist = array_merge($rlist,[$item=>$item]); }
			$cnt++;
		}
		return $rlist;
	} else { return []; }
}
?>

<div class="params-form">
<?php $form = ActiveForm::begin(['id'=>'paramsform']); ?>
<div class="row">
	<div class="col-xs-12 col-sm-3">
		<?= $form->field($model, 'sell_date')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-6 col-sm-3">
		<?= $form->field($model, 'guest_sku').PHP_EOL; ?>
	</div>
	<div class="col-xs-6 col-sm-3">
		<?= $form->field($model, 'guest_total').PHP_EOL; ?>
	</div>
	<div class="col-xs-6 col-sm-3">
		<?= Html::label('Enviroment (moved to params file)')."<br />".Html::textInput('Env',Yii::$app->params['env'],['readOnly'=>true,'disabled'=>true,'class'=>'form-control']); ?>
	</div>
	<div class="col-xs-12 col-sm-9">
		<?= $form->field($model, 'whitelist')->dropDownList(MakeDropdown($model->whitelist),['value'=>json_decode($model->whitelist),'prompt'=>'Select',  'multiple'=>true, 'size'=>false]).PHP_EOL; ?>
	</div>
	<div class="col-xs-12 col-sm-3">
		<?= $form->field($model, 'AddWhitelist')->textInput(['maxlength' => true])->label('Add To Whitelist') ?>
	</div>
	<div class="col-xs-12 col-sm-12">
		<?= $form->field($model, 'remote_users').PHP_EOL; ?>
	</div>
	<div class="col-xs-12 col-sm-9">
		<?= $form->field($model, 'rso_email')->dropDownList(MakeDropdown($model->rso_email),['value'=>json_decode($model->rso_email),'prompt'=>'Select',  'multiple'=>true, 'size'=>false]).PHP_EOL; ?>
	</div>
	<div class="col-xs-12 col-sm-3">
		<?= $form->field($model, 'Addrso_email')->textInput(['maxlength' => true])->label('Add Email to RSO Report') ?>
	</div>
</div>

<h3>Discounts</h2>
<div class="row">
	<div class="col-xs-6 col-sm-6">
	<?= $form->field($model, 'sku_student')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-6 col-sm-6">
	<?= $form->field($model, 'sku_wc_discount')->textInput(['maxlength'=>true]) ?>
	</div>
</div>

<h3>Converge Settings</h2>
<div class="row">
	<div class="col-xs-6 col-sm-3">
	<?= $form->field($model, 'conv_p_merc_id')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-6 col-sm-3">
	<?= $form->field($model, 'conv_p_user_id')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-12 col-sm-6">
	<?= $form->field($model, 'conv_p_pin')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-6 col-sm-3">
	<?= $form->field($model, 'conv_d_merc_id')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-6 col-sm-3">
	<?= $form->field($model, 'conv_d_user_id')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-12 col-sm-6">
	<?= $form->field($model, 'conv_d_pin')->textInput(['maxlength'=>true]) ?>
	</div>
</div>

<h3> PayPal Settings:</h2>
<div class="row">
	<div class="col-xs-6 col-sm-3">
	<?= $form->field($model, 'pp_id')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-6 col-sm-3">
	<?= $form->field($model, 'pp_sec')->textInput(['maxlength'=>true]) ?>
	</div>
</div>

<h3>QuickBooks oAuth 2</h2>
<div class="row">
	<div class="col-xs-12 col-sm-6">
	<?= $form->field($model, 'qb_oauth_cust_key')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-12 col-sm-6">
	<?= $form->field($model, 'qb_oauth_cust_sec')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-12 col-sm-6">
	<?= $form->field($model, 'qb_oa2_id')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-12 col-sm-6">
	<?= $form->field($model, 'qb_oa2_sec')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-6 col-sm-3">
	<?= $form->field($model, 'qb_oa2_realmId')->textInput(['disabled'=>true,'maxlength'=>true]) ?>
	</div>
	<div class="col-xs-6 col-sm-3">
	<?= $form->field($model, 'qb_oa2_access_token')->textInput(['disabled'=>true,'maxlength'=>true]) ?>
	</div>
	<div class="col-xs-6 col-sm-3">
	<?= $form->field($model, 'qb_oa2_access_date')->textInput(['disabled'=>true,'maxlength'=>true]) ?>
	</div>
	<div class="col-xs-6 col-sm-6">
	<?= $form->field($model, 'qb_oa2_refresh_token')->textInput(['disabled'=>true,'maxlength'=>true]) ?>
	</div>
	<div class="col-xs-6 col-sm-3">
	<?= $form->field($model, 'qb_oa2_refresh_date')->textInput(['disabled'=>true,'maxlength'=>true]) ?>
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

<script>
$("#params-whitelist").select2({width: "100%"});

$("#params-addwhitelist").change(function(e){
	var new_word = $("#params-addwhitelist").val().toUpperCase();

	$("#params-whitelist").append($('<option></option>')
        .val(new_word)
        .attr('selected', '')
        .html(new_word));

	$("#params-whitelist").trigger('change');
	$("#params-whitelist").trigger("select2:updated")
	$("#params-addwhitelist").val('');
	document.getElementById("paramsform").submit();
});

$("#params-rso_email").select2({placeholder_text_multiple:'add Email',width: "100%"});

$("#params-addrso_email").change(function(e){
	var new_word = $("#params-addrso_email").val().toLowerCase();
	if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(new_word)) { //pass
	} else {
		return false;
	}

	$("#params-rso_email").append($('<option></option>')
        .val(new_word)
        .attr('selected', '')
        .html(new_word));

	$("#params-rso_email").trigger('change');
	$("#params-rso_email").trigger("select2:updated")
	$("#params-addrso_email").val('');
	document.getElementById("paramsform").submit();
});

</script>
