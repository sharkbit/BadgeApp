<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Params */
/* @var $form yii\widgets\ActiveForm */


$this->title = "$model->name";
$this->params['breadcrumbs'][] = ['label' => 'Calendar Setup'];
$this->params['breadcrumbs'][] = ['label' => 'Facilities', 'url' => ['/cal-setup/facility'] ];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/cal-setup/updatefac?id='.$model->facility_id] ];

?>
<div class="calsetup-fac-form">

    <h3><?= Html::encode($this->title) ?></h3>
<hr />

<?php $form = ActiveForm::begin(); ?>
<div class="row">
	<div class="col-xs-2 col-sm-2">
		<?= $form->field($model, 'facility_id')->textInput(['disabled'=>true,'maxlength'=>true]) ?>
	</div>
	<div class="col-xs-6">
		<?= $form->field($model, 'name')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-2 col-sm-2">
	    <?= $form->field($model, 'available_lanes')->textInput(['maxlength'=>true]) ?>
	</div>

	<div class="col-xs-2 col-sm-2">
		<?= $form->field($model, 'active')->DropDownList(['1'=>'True','0'=>'False']) ?>
	</div>
	<div class="col-xs-2 col-sm-2">
		<?= $form->field($model, 'display_order')->textInput(['disabled'=>true,'maxlength'=>true]) ?>
	</div>



	<div class="col-xs-12 form-group">
		<?= Html::submitButton('Update', ['class' => 'btn btn-primary pull-right']) ?>
	</div>


</div> 
<?php ActiveForm::end(); ?>
</div>
