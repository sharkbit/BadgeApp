<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

if($model->isNewRecord) {
	$model->date = date("Y-m-d",strtotime(yii::$app->controller->getNowTime()));
}
?>

<div class="rso_rpt-form">
<?php $form = ActiveForm::begin(['id'=>'rsoreportsform']); ?>

<h2>Active Report</h2>
<div class="row">
	<?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
	<div class="col-xs-12 col-sm-2">
		<?= $form->field($model, 'date')->textInput(['readonly' => true,'maxlength'=>true]) ?>
	</div>
	<div class="col-xs-6 col-sm-2">
		<?= $form->field($model, 'shift')->dropDownList(['a'=>'aaa','b'=>'bbb']) ?>
	</div>
	<div class="col-xs-6 col-sm-3">
		<?= $form->field($model, 'rso').PHP_EOL; ?>
	</div>
	<div class="col-xs-6 col-sm-5">
		<?= $form->field($model, 'shift_anom')->textarea(['rows' => '1']).PHP_EOL; ?>
	</div>
	<div class="col-xs-12 col-sm-12">
		<?= $form->field($model, 'notes')->textarea(['rows' => '1']).PHP_EOL; ?>
	</div>
	<div class="col-xs-12 col-sm-3">
		<?php // $form->field($model, 'notes')->textInput(['maxlength' => true]) ?>
	</div>
	<div class="col-xs-12 col-sm-12">
		<?php //  $form->field($model, 'notes').PHP_EOL; ?>
	</div>
</div>

<h3>Participation</h3>
<div class="row">
	<div class="col-xs-3 col-sm-2 col-md-1">
	<?= $form->field($model, 'par_50')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3 col-sm-2 col-md-1">
	<?= $form->field($model, 'par_100')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3 col-sm-2 col-md-1">
	<?= $form->field($model, 'par_200')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3 col-sm-2 col-md-1">
	<?= $form->field($model, 'par_steel')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3 col-sm-2 col-md-1">
	<?= $form->field($model, 'par_nm_hq')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3 col-sm-2 col-md-1">
	<?= $form->field($model, 'par_m_hq')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3 col-sm-2 col-md-1">
	<?= $form->field($model, 'par_trap')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3 col-sm-2 col-md-1">
	<?= $form->field($model, 'par_arch')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3 col-sm-2 col-md-1">
	<?= $form->field($model, 'par_pel')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3 col-sm-2 col-md-1">
	<?= $form->field($model, 'par_spr')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3 col-sm-2 col-md-1">
	<?= $form->field($model, 'par_cio_stu')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3 col-sm-2 col-md-1">
	<?= $form->field($model, 'par_act')->textInput(['maxlength'=>true]) ?>
	</div>
</div>

<h3>Cash:</h3>
<div class="row">
	<div class="col-xs-6 col-sm-2">
	<?= $form->field($model, 'cash_bos')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-6 col-sm-2">
	<?= $form->field($model, 'cash_eos')->textInput(['maxlength'=>true]) ?>
	</div>
</div>

<h3>More Notes:</h3>
<div class="row">
	<div class="col-xs-12 col-sm-12">
	<?= $form->field($model, 'closing')->textarea(['rows' => '1']).PHP_EOL; ?>
	</div>
</div>

<?php if(yii::$app->controller->hasPermission('params/update')) { ?>
<div class="col-xs-6 col-sm-6">
	<?= $form->field($model, 'closed')->textInput(['maxlength'=>true]) ?>
</div>
<div class="col-xs-12 col-sm-6">
	<?= $form->field($model, 'remarks')->textarea(['rows' => '1']).PHP_EOL; ?>
</div>
<?php } ?>

<div class="row">
	<div class="col-xs-12 col-sm-12">
		<div class="btn-group pull-right">
			<?= Html::submitButton('<i class="fa fa-check" aria-hidden="true"></i> Save ', ['class' => 'btn btn-success','id'=>'save_btn']).PHP_EOL;  ?>

			<?php // = Html::a('<i class="fa fa-check" aria-hidden="true"></i> Save ', ['current'], ['class' => 'btn btn-success ']) ?>
			<?= Html::a('<i class="fa fa-check-double" aria-hidden="true"></i> Finalize ', ['curent'], ['class' => 'btn btn-warning ']) ?>
		</div>
	</div>
</div>
<br />

<?php ActiveForm::end(); ?>
</div>
