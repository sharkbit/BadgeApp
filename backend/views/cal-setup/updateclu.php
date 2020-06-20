<?php

use backend\models\agcClubs;
use backend\models\agcFacility;
use backend\models\agcEventStatus;
use backend\models\agcRangeStatus;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\Params */
/* @var $form yii\widgets\ActiveForm */

$this->title = "$model->name";
$this->params['breadcrumbs'][] = ['label' => 'Calendar Setup'];
$this->params['breadcrumbs'][] = ['label' => 'Clubs', 'url' => ['/cal-setup/clubs'] ];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/cal-setup/updateclu?id='.$model->club_id] ];

?>
<div class="calsetup-fac-form">

<?php $form = ActiveForm::begin(); ?>
<div class="row">
	<div class="col-xs-4 col-sm-2">
		<?= $form->field($model, 'club_id')->textInput(['disabled'=>true,'maxlength'=>true]) ?>
	</div>
	<div class="col-xs-6 col-sm-4">
		<?= $form->field($model, 'name')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-6 col-sm-4">
	    <?= $form->field($model, 'nick_name')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-4 col-sm-2">
		<?= $form->field($model, 'ca')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-4 col-sm-2">
	    <?= $form->field($model, 'display_in_administration')->DropDownList(['1'=>'True','0'=>'False']) ?>
	</div>
	<div class="col-xs-4 col-sm-2">
	    <?= $form->field($model, 'display_in_badges_administration')->DropDownList(['1'=>'True','0'=>'False']) ?>
	</div>
	<div class="col-xs-4 col-sm-2">
	    <?= $form->field($model, 'is_cio')->DropDownList(['1'=>'True','0'=>'False']) ?>
	</div>
	<div class="col-xs-4 col-sm-2">
	    <?= $form->field($model, 'active')->DropDownList(['1'=>'True','0'=>'False']) ?>
	</div>
	<div class="col-xs-4 col-sm-2">
		<?= $form->field($model, 'display_order')->textInput(['disabled'=>true,'maxlength'=>true]) ?>
	</div>

	<div class="col-xs-12 form-group">
		<?= Html::submitButton('Update', ['class' => 'btn btn-primary pull-right']) ?>
	</div>

</div> 
<?php ActiveForm::end(); ?>
</div>

<script>
	$("#agccal-facility_id").change(function(e) {
		var reqLanes = $("#Req_Lanes").val();
		var facil_id = $("#agccal-facility_id").val();
		if (reqLanes.includes(facil_id)) {
			$("#Div_Lanes_Req").show();
		} else {
			$("#Div_Lanes_Req").hide();
			$("#agccal-lanes_requested").val(0);
		}
	});
</script>