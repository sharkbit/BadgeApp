<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

if($model->isNewRecord) {
	$model->date = date("Y-m-d H:i:s",strtotime(yii::$app->controller->getNowTime()));
}
?>

<div class="rso_rpt-form" ng-controller="RsoReportFrom">
<?php $form = ActiveForm::begin([
                'action' => ['/rso-rpt/current'],
                'method' => 'post',
                'id'=>'rsoreportsformFilter'
            ]); ?>

<h2>Active Report:</h2>
<div class="row">
	<?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
	<div class="col-xs-12 col-sm-2">
		<?= $form->field($model, 'date')->textInput(['readonly' => true,'maxlength'=>true]) ?>
	</div>
	<div class="col-xs-6 col-sm-2">
		<?= $form->field($model, 'shift')->dropDownList(['m'=>'Morning','e'=>'Evening']) ?>
	</div>
	<div class="col-xs-6 col-sm-8">
		<?= $form->field($model, 'rso')->dropDownList($model->listRSOs(),['value'=>json_decode($model->rso),'multiple'=>true]).PHP_EOL; ?>
	</div>
	<div class="col-xs-12 col-sm-12">
		<?= $form->field($model, 'shift_anom')->textarea(['rows' => '1']).PHP_EOL; ?>
	</div>
	<div class="col-xs-12 col-sm-12">
		<?= $form->field($model, 'notes')->textarea(['rows' => '1']).PHP_EOL; ?>
	</div>

	<div class="col-xs-6 col-sm-3">
		<?= $form->field($model, 'wb_color')->dropDownList(['g'=>'Green','b'=>'Blue','r'=>'Red','l'=>'Lavender','k'=>'Black'],['prompt'=>'select']) ?>
	</div>
	<div class="col-xs-6 col-sm-3">
		<?= $form->field($model, 'mics')->dropDownList(['o'=>'Mics Set Out','s'=>'Mics Stores in closet','t'=>'Mics in Trap 3'],['prompt'=>'select']) ?>
	</div>
	<div class="col-xs-12 col-sm-2">
		<?= $form->field($model, 'wb_trap_cases')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-12 col-sm-2">
		<B> Badge Sticker Numbers comming to a tab near you one day.
	</div>
</div>

<h3>Participation:</h3>
<div class="row" style="background-color:WhiteSmoke;">
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
	<?= $form->field($model, 'par_trap')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3 col-sm-2 col-md-1">
	<?= $form->field($model, 'par_arch')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3 col-sm-2 col-md-1">
	<?= $form->field($model, 'par_pel')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3 col-sm-2 col-md-1">
	<?= $form->field($model, 'par_act')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3 col-sm-2 col-md-1">
	<?= $form->field($model, 'par_spr')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3 col-sm-2 col-md-1">
	<?= $form->field($model, 'par_nm_hq')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3 col-sm-2 col-md-1">
	<?= $form->field($model, 'par_m_hq')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3 col-sm-2 col-md-1">
	<?= $form->field($model, 'par_cio_stu')->textInput(['maxlength'=>true]) ?>
	</div>
</div>

<h3>Cash:</h3>
<div class="row" style="background-color:#ecf9f2;">
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

<?php if(yii::$app->controller->hasPermission('rso-rpt/close_mod')) { ?>
<div class="col-xs-6 col-sm-6">
	<?= $form->field($model, 'closed')->checkbox() ?>
</div>
<?php } ?>

<div class="row">
	<div class="col-xs-12 col-sm-12">
		<div class="btn-group pull-right">
			<?= Html::submitButton('<i class="fa fa-check" aria-hidden="true"></i> Save ', ['class' => 'btn btn-success','id'=>'save_btn']).PHP_EOL;  ?>
			<?= Html::a('<i class="fa fa-check-double" aria-hidden="true"></i> Finalize ', ['current?id='.$model->id.'&close=1'], ['class' => 'btn btn-warning ']) ?>
		</div>
	</div>
</div>
<br />

<?php ActiveForm::end(); ?>

<?php if(yii::$app->controller->hasPermission('rso-rpt/remarks')) { ?>
<div class="row">
	<div class="col-xs-12">
		<?php 
		$remakrs_logs = json_decode($model->remarks,true);
		if(!empty($remakrs_logs)) {
			rsort($remakrs_logs);
		}
		else {
			$remakrs_logs = null;
		}  
	?>
	</div>
	<div class="col-xs-12">
         <div class="row">
            <div class="col-xs-12">
                <h3> Remarks history </h3>
            </div>
            <div class="col-xs-12">
                <?=$this->render('/badges/_remarks',['remakrs_logs'=>$remakrs_logs])?>
            </div>
        </div>
    </div>
</div>
<?php } ?>
</div>

<script>
  $(document).ready(function (e) {
    $("#rsoreports-rso").select2({placeholder_text_multiple:'Select RSOs',width: "100%",})
  });
</script>

