<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use backend\models\clubs;

/* @var $this yii\web\View */
/* @var $model backend\models\Events */
/* @var $form yii\widgets\ActiveForm */

if((isset($model->e_poc)) & ($model->e_poc>0))  {
	$poc_name = yii::$app->controller->decodeBadgeName((int)$model->e_poc);
} elseif ($model->isNewRecord) {
	$poc_name = yii::$app->controller->decodeBadgeName((int)$_SESSION['badge_number']);
} else {$poc_name='';}

$only_CIO=false; $checked_cnt=0;
foreach ($_SESSION['privilege'] as $priv_chk){
	if (($priv_chk==8) && ($checked_cnt==0)) {$only_CIO=true; } else {$only_CIO=false; }
	$checked_cnt++;
}

if(!yii::$app->controller->hasPermission('events/approve')) {
	$clubList=(new clubs)->getClubList(false, json_encode((New clubs)->getMyClubs($_SESSION['badge_number'])),false,true);
} else {
	$clubList=(new clubs)->getClubList();
} ?>

<div class="events-form">
<?php $form = ActiveForm::begin(); ?>
	<div class="row" style="display: none;" >
		<?= $form->field($model, 'e_id')->hiddenInput([])->label(false).PHP_EOL; ?>
	</div>
	<div class="row" style="margin: auto;">

	<div class="col-xs-12 col-sm-6 col-md-6" >
		<?= $form->field($model, 'sponsor')->dropDownList($clubList,['prompt'=>'Select']).PHP_EOL; ?>
	</div>

	<div class="col-xs-12 col-sm-6 col-md-6">
		<?= $form->field($model, 'e_name')->textInput(['maxlength' => true]).PHP_EOL; ?>
	</div>
	<div class="col-sm-4">
	<?php	//if($model->isNewRecord) {
			echo $form->field($model, 'e_date')->widget(DatePicker::classname(), [
				'options' => ['placeholder' => 'Event Date',
				'value'=> $model->isNewRecord ?  date('M d, Y',strtotime(yii::$app->controller->getNowTime())) : date('M d, Y',strtotime($model->e_date))],
				'pluginOptions' => [
					'autoclose'=>true,
					'format' => 'M dd, yyyy',
					'todayHighlight' => true] ] );
		//} else {
		//	echo $form->field($model, 'e_date')->textInput(['readOnly'=>'true','value'=>date('M d, Y',strtotime($model->e_date))]);
		//} ?>
	</div>
	<div class="col-xs-2 col-sm-2">
	<?php if(yii::$app->controller->hasPermission('events/approve')) {
			echo $form->field($model, 'e_poc')->textInput(['maxlength' => true]);
		} elseif ($model->isNewRecord) {
			echo $form->field($model, 'e_poc')->textInput(['value'=>$_SESSION['badge_number'],'maxlength' => true,'readOnly'=>true]);
		} else {
			echo $form->field($model, 'e_poc')->textInput(['value'=>$model->badge_number,'maxlength' => true,'readOnly'=>true]);
		} ?>

	</div>
	<div class="col-xs-6 col-sm-6">

		<?= $form->field($model, 'poc_name')->textInput(['value' => $poc_name, 'maxlength' => true,'disabled'=>true]).PHP_EOL; ?>
	</div>

<?php if ($only_CIO) {
		echo $form->field($model, 'e_type')->hiddenInput(['value'=>'cio'])->label(false).PHP_EOL;
	} else {
		$list = [ 'cio'=>'CIO', 'club'=>'Club', 'vol'=>'Volunteer' ];
		echo '<div class="col-xs-2 col-sm-2">'."\n";
		echo $form->field($model, 'e_type')->dropDownList($list,['prompt'=>$model->isNewRecord ? 'Select Type' : null]).PHP_EOL;
		echo "\n</div>\n";
	} ?>
		<div class="col-xs-2 col-sm-2" id="disp_e_hours" <?php if($model->e_type<>'vol') {echo ' style="display: none"';} ?> >
		<?= $form->field($model, 'e_hours')->textInput(['value' => $model->e_hours>0 ? $model->e_hours : 0 , 'maxlength' => true]) ?>
		</div>
  <?php if($model->isNewRecord) {
			echo "<div style='display: none;'>";
			echo $form->field($model, 'e_status')->hiddenInput(['value'=>0])->label(false).PHP_EOL;
			echo "</div>\n";
		} else { ?>
		<div class="col-xs-2 col-sm-2">
		<?= $form->field($model, 'e_status')->dropDownList([ '0'=>'Open', '1'=>'Closed', '2'=>'Canceled' ]) ?>
		</div> <?php } ?>
		<div class="col-xs-2 col-sm-2" id="note_close" style="display: none;" >
		This close option will only Hide the event from the login page. To Close Event properly use Close on the
		<a href="<?=yii::$app->params['rootUrl']?>/events/view?id=<?=$model->e_id?>">View page</a>.  <br> Did you mean to Cancel the event?.
		</div>

		<div class="col-xs-8 col-sm-8" id="inst_div"<?php
	$Show_CIO_inst=true;
	if (($model->isNewRecord) && ($only_CIO)) {
		$Show_CIO_inst=true;
	} elseif ($model->e_type!='cio') {
		$Show_CIO_inst=false ;
	}
	if(!$Show_CIO_inst) {echo ' style="display: none;"';}
	?>>
		<?=$form->field($model, 'e_inst')->textInput(['value' => $model->e_inst, 'maxlength' => true]) ?>
		</div>

	<div class="form-group btn-group pull-right">
		<?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		<?= Html::resetButton('Reset <i class="fa fa-eraser"> </i>', ['class' => 'btn btn-danger']) ?>
	</div>
</div>
</div>
<?php ActiveForm::end(); ?>
</div>

<script>
	$("#events-e_status").change(function() {
		stat = $(this).val();
		if(stat=='1') { $("#note_close").show(); } else { $("#note_close").hide(); }
	});

	$("#events-e_type").change(function() {
		if ($(this).val() == 'cio') {
			$("#inst_div").show()
		} else { $("#inst_div").hide(); }
	});

	$('#events-e_poc').on('input', function() {
		var badgeNumber = $(this).val()
		if((badgeNumber!='') && (badgeNumber!=0)) {
			changeBadgeName(badgeNumber);
		} else {
		  $("#events-poc_name").val('');
		}
	});

	function changeBadgeName(badgeNumber) {
		jQuery.ajax({
			method: 'GET',
			url: '<?=yii::$app->params['rootUrl']?>/badges/get-badge-details?badge_number='+badgeNumber,
			crossDomain: false,
			success: function(responseData, textStatus, jqXHR) {
				responseData =  JSON.parse(responseData);
				var PrimeExpTimestamp = getTimestamp(responseData.expires);
				var resExpTimestamp = Math.floor(Date.now() / 1000);

				if(PrimeExpTimestamp < resExpTimestamp) {
					$("#events-poc_name").val('No Active Member Found');
				} else {
					$("#events-poc_name").val(responseData.first_name+' '+responseData.last_name);
				}
			},
			error: function (responseData, textStatus, errorThrown) {
				$("#events-poc_name").val('Valid Badge Holder not found');
				console.log("fail "+responseData);
			},
		});
	}

</script>
