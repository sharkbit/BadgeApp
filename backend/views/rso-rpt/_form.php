<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

if($model->isNewRecord) {
	$model->date_open = date("Y-m-d H:i:s",strtotime(yii::$app->controller->getNowTime()));
	$model->par_50 = $model->par_100 = $model->par_200 = $model->par_steel = $model->par_nm_hq = $model->par_m_hq = $model->par_trap = $model->par_arch = $model->par_pel = $model->par_spr = $model->par_cio_stu = $model->par_act =0;
}

$rpt_pre = backend\models\RsoReports::find()->where(['<','date_open',$model->date_open])->orderBy(['date_open'=>SORT_DESC])->one();
?>

<div class="rso_rpt-form" ng-controller="RsoReportFrom">
<?php $form = ActiveForm::begin(['action' => ['/rso-rpt/current'],'method' => 'post',
                'id'=>'rsoreportsformFilter','enableAjaxValidation' => true]); ?>

<?php if($rpt_pre) { ?>
<h3>Previous Report:</h3>
<div class="row">
	<div class="col-xs-12 col-sm-12">
		<div class="form-group ">
		 <?php  
		 echo Html::label('Closing Remarks','prev_close',[ 'class'=>"control-label"])."<br />\n";
		 echo Html::textArea('prev_close',$rpt_pre->closing,['readonly' => true,'rows'=>1,'id'=>'prev_close','class'=>"form-control"]).PHP_EOL;
		  ?>
		</div>
	</div>
</div>
<?php } ?>

<h2>Active Report:</h2>
<div class="row">
	<?= $form->field($model, 'id')->hiddenInput()->label(false).PHP_EOL ?>
	<div class="col-xs-6 col-sm-3 col-md-2">
		<?= $form->field($model, 'date_open')->textInput(['readonly' => true,'maxlength'=>true]).PHP_EOL ?>
	</div>
	<div class="col-xs-6 col-sm-2">
		<?= $form->field($model, 'shift')->dropDownList(['m'=>'Morning','e'=>'Evening']).PHP_EOL ?>
	</div>
	<div class="col-xs-12 col-sm-7 col-md-8">
		<?= $form->field($model, 'rso')->dropDownList($model->listRSOs(),['value'=>json_decode($model->rso),'multiple'=>true])->label("All RSO's on Shift").PHP_EOL; ?>
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
		<?= $form->field($model, 'mics')->dropDownList(['o'=>'Mics Set Out','s'=>'Mics stored in closet','t'=>'Mics in Trap 3'],['prompt'=>'select']) ?>
	</div>
	<div class="col-xs-12 col-sm-2">
		<?= $form->field($model, 'wb_trap_cases')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-12 col-sm-4">
		<?= $form->field($model, 'stickers')->textInput(['value'=>$model->getStickerCount('rso'),'readonly' => true,'maxlength'=>true]) ?>
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
	<?= $form->field($model, 'par_steel')->textInput(['title'=>'Steel Rental','maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3 col-sm-2 col-md-1">
	<?= $form->field($model, 'par_trap')->textInput(['title'=>'Trap Range','maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3 col-sm-2 col-md-1">
	<?= $form->field($model, 'par_arch')->textInput(['title'=>'Arcery Range','maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3 col-sm-2 col-md-1">
	<?= $form->field($model, 'par_pel')->textInput(['title'=>'Pellet Range','maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3 col-sm-2 col-md-1">
	<?= $form->field($model, 'par_act')->textInput(['title'=>'Action Range (Shooting Bays)','maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3 col-sm-2 col-md-1">
	<?= $form->field($model, 'par_spr')->textInput(['title'=>'Shotgun Pattern Range','maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3 col-sm-2 col-md-1">
	<?= $form->field($model, 'par_nm_hq')->textInput(['title'=>'Non Member Hunter Qualification','maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3 col-sm-2 col-md-1">
	<?= $form->field($model, 'par_m_hq')->textInput(['title'=>'Member Hunter Qualification','maxlength'=>true]) ?>
	</div>
	<div class="col-xs-3 col-sm-2 col-md-1">
	<?= $form->field($model, 'par_cio_stu')->textInput(['maxlength'=>true]) ?>
	</div>
</div>

<h3>Cash:</h3>
<div class="row" style="background-color:#ecf9f2;">
	<div class="col-xs-4 col-sm-3 col-md-2 col-lg-1">
	<?= $form->field($model, 'cash_bos')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-4 col-sm-3 col-md-2 col-lg-1">
	<?= $form->field($model, 'cash_drop')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-4 col-sm-3 col-md-2 col-lg-1">
	<?= $form->field($model, 'cash_eos')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
	<?= $form->field($model, 'cash')->textarea(['value'=>$model->getCash('cash',$model),'rows' => '3','readonly' => true,'maxlength'=>true]) ?>
	</div>
	<div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
	<?= $form->field($model, 'checks')->textarea(['value'=>$model->getCash('check',$model),'rows' => '3','readonly' => true,'maxlength'=>true]) ?>
	</div>
</div>
<p> </p>
<div class="row">
	<div class="col-xs-12 col-sm-12">
	<?= $form->field($model, 'closing')->textarea(['title'=>'Information Next Shift Should Know','rows' => '1']).PHP_EOL; ?>
	</div>
	<div class="col-xs-12 col-sm-12">
	<?= $form->field($model, 'violations')->textarea(['value'=>$model->getViolations($model),'rows' => '3','readonly' => true,'maxlength'=>true]) ?>
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
		<br />Save Report Often & <b>Prior to Finalizing</b><br />
			<?= Html::submitButton('<i class="fa fa-check" aria-hidden="true"></i> Save ', ['class' => 'btn btn-success','id'=>'save_btn']).PHP_EOL;  ?>
			<?= Html::a('<i class="fa fa-check-double" aria-hidden="true"></i> Finalize ', ['current?id='.$model->id.'&close=1'], ['class' => 'btn btn-warning ','onclick'=>'clkfin();']) ?>
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
	
    $("textarea").each(function () {
      this.setAttribute("style", "height:" + (this.scrollHeight) + "px;overflow-y:hidden;");
    }).on("input", function () {
      this.style.height = "auto";
      this.style.height = (this.scrollHeight) + "px";
    });
  });
  
  function clkfin() {
    $("#btn-group").html('Please Wait while report is being sent. . .');
  };
</script>

