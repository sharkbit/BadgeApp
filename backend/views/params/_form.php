<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Params */
/* @var $form yii\widgets\ActiveForm */

//$model->whitelist = ['ACTION','AGC','AND','ARCHERY','&','CLUB','GUN','MD','PISTOL','RIFLE','SHOOTING','STATE'];
$cnt=0;
$whitelist = json_decode($model->whitelist);
sort($whitelist);
foreach ($whitelist as $item) {
	if($cnt==0) { $wlist = [$item=>$item];
	} else { $wlist = array_merge($wlist,[$item=>$item]); }
	$cnt++;
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
		<?= $form->field($model, 'qb_env')->dropDownList(['dev'=>'Development','prod'=>'Production']) ?>
	</div>
	<div class="col-xs-12 col-sm-6">
		<?= $form->field($model, 'whitelist')->dropDownList($wlist,['value'=>json_decode($model->whitelist),'prompt'=>'Select', 'id'=>'params-whitelist', 'class'=>"chosen_select", 'multiple'=>true, 'size'=>false]).PHP_EOL; ?>
	</div>
	<div class="col-xs-12 col-sm-3">
		<?= $form->field($model, 'AddWhitelist')->textInput(['maxlength' => true])->label('Add To Whitelist') ?>
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
<!-- 
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
-->
<div class="row">    
	<div class="form-group">
		<?= Html::submitButton('Update', ['class' => 'btn btn-primary pull-right']) ?>
	</div>
</div>
</div>
<?php ActiveForm::end(); ?>

</div>
<script src="<?=yii::$app->params['rootUrl']?>/js/chosen.jquery.min.js"></script>
<style>
  .chosen-container-multi .chosen-choices {padding: 3px; }
</style>
<script>
$("#params-whitelist").chosen({placeholder_text_multiple:'Choose Clubs',width: "100%"});

$("#params-addwhitelist").change(function(e){ 
	var new_word = $("#params-addwhitelist").val().toUpperCase();
	console.log(new_word);
	
	//$("#params-whitelist").append('<option value="'+new_word+'" selected>'+new_word+'</option>').trigger("chosen:updated");
	  $("#params-whitelist").append($('<option></option>')
        .val(new_word)
        .attr('selected', '')
        .html(new_word));
		
	$("#params-whitelist").trigger('change');
	//$("#params-whitelist").trigger('chosen:update');
	//$("#params-whitelist").chosen("update"); //.chosen({placeholder_text_multiple:'Choose Clubs',width: "100%"});
	$(".chosen-select").trigger("chosen:updated")
	$("#params-addwhitelist").val('');
	document.getElementById("paramsform").submit();
	
});

</script>
