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

$this->title = 'Current RSO Report';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['rso-rpt/current']];
?>

<?=$this->render('_view-tab-menu').PHP_EOL ?>

<div class="params-form">
<?php $form = ActiveForm::begin(['id'=>'paramsform']); ?>
<div class="row">
	<div class="col-xs-12 col-sm-9">
		<?= $form->field($model, 'rso_email')->dropDownList(MakeDropdown($model->rso_email),['value'=>json_decode($model->rso_email),'prompt'=>'Select',  'multiple'=>true, 'size'=>false]).PHP_EOL; ?>
	</div>
	<div class="col-xs-12 col-sm-3">
		<?= $form->field($model, 'Addrso_email')->textInput(['maxlength' => true])->label('Add Email to RSO Report') ?>
	</div>
</div>

<div class="row">
	<div class="col-xs-12 form-group">
		<?= Html::submitButton('Update', ['class' => 'btn btn-primary pull-right']) ?>
	</div>
</div>
</div>
<?php ActiveForm::end(); ?>
</div>

<script>
$("#params-rso_email").select2({placeholder_text_multiple:'add Email',width: "100%"});

$("#params-addrso_email").change(function(e){
	var new_word = $("#params-addrso_email").val().toLowerCase();
	if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(new_word)) { //pass
	} else {
		return false;
	}
	console.log(new_word);
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
