<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\date\DatePicker;
use backend\models\RuleList;

/* @var $this yii\web\View */
/* @var $model backend\models\Violations */
/* @var $form yii\widgets\ActiveForm */

$isviolations = false;
if($model->isNewRecord) {
	$model->vi_date = date("M d, Y H:i",strtotime(yii::$app->controller->getNowTime()));
}
echo $this->render('_view-tab-menu').PHP_EOL ?>

<div class="violations-form" ng-controller="ViolationsRecFrom">
  <!--  <h1><?= Html::encode($this->title) ?></h1> <hr />-->
<br />
    <?php $form = ActiveForm::begin(['id'=>'ViolationsForm',]); ?>
<div class="row">
    <div class="col-xs-12 col-sm-4 pull-right" >

<?php if(isset($model->was_guest) && ($model->was_guest=='1')) {
	  echo "<img src='/files/badge_photos/guest.png' id='warm'>";
  }
  elseif(file_exists("files/badge_photos/".str_pad($model->badge_involved, 5, '0', STR_PAD_LEFT).".jpg")) {
	echo "<img src='/files/badge_photos/".str_pad($model->badge_involved, 5, '0', STR_PAD_LEFT).".jpg?dummy=".rand(10000,99999).
				"' alt='".$model->involved_name."' width='260' height='340' id='warm'><br><br>";
  } else echo "<img src='/files/badge_photos/warm.gif' id='warm'>";
	
	?>
	</div>
	<div class="col-xs-12 col-sm-8" >
		<div class="row">
		<div class="col-xs-12 col-sm-3">

        <?php if($model->isNewRecord) {
			echo $form->field($model, 'badge_reporter')->textInput(['value'=>$model->badge_reporter,'readonly'=> $isviolations]).PHP_EOL;
			echo '</div><div class="col-xs-12 col-sm-5">';
			echo $form->field($model, 'reporter_name')->textInput(['readOnly'=>'true']).PHP_EOL;
			echo '</div><div class="col-xs-12 col-sm-2">';
			echo $form->field($model, 'vi_type')->textInput(['readOnly'=>'true']).PHP_EOL;
			echo '</div><div class="container">';
			echo '<div class="row"><div class="col-xs-6 col-sm-3">';
			echo '<div class="row"><div class="col-xs-12 col-sm-12">';
			echo $form->field($model, 'vi_override')->checkbox().PHP_EOL.'<p> </p>';
			echo '</div></div><div class="row"><div class="col-xs-12 col-sm-12">';
			echo $form->field($model, 'was_guest')->checkbox().PHP_EOL;
			echo '</div></div>';
			echo '</div><div class="col-xs-12 col-sm-3">';
			echo $form->field($model, 'vi_date')->textInput(['readonly' => true,'value'=>(yii::$app->controller->getNowTime())]);
			echo '</div></div>' .PHP_EOL;
        } else {
			echo $form->field($model, 'badge_reporter')->textInput(['value'=>$model->badge_reporter,
				'readOnly'=>yii::$app->controller->hasPermission('violations/delete') ? false: true]).PHP_EOL;
			echo '</div><div class="col-xs-12 col-sm-5">';
			echo $form->field($model, 'reporter_name')->textInput(['value'=>$model->reporter_name,'readOnly'=>'true']).PHP_EOL;
			echo '</div><div class="col-xs-12 col-sm-2">';
			echo $form->field($model, 'vi_type')->textInput(['readOnly'=>'true']).PHP_EOL;
			echo '</div><div class="container">';
			echo '<div class="row"><div class="col-xs-6 col-sm-3">';
			echo '<div class="row"><div class="col-xs-12 col-sm-12">';
			echo $form->field($model, 'vi_override')->checkbox().PHP_EOL.'<p> </p>';
			echo '</div></div><div class="row"><div class="col-xs-12 col-sm-12">';
			echo $form->field($model, 'was_guest')->checkbox().PHP_EOL;
			echo '</div></div>';
			echo '</div><div class="col-xs-12 col-sm-3">';
			echo $form->field($model, 'vi_date')->textInput(['readOnly'=>'true','value'=>yii::$app->controller->pretydtg($model->vi_date)]);
			if(yii::$app->controller->hasPermission('violations/board')) {
				echo '</div><div class="col-xs-12 col-sm-3">';
			    echo $form->field($model, 'hear_date')->widget(DatePicker::classname(), [
					'options' => ['placeholder' => 'Hearing Date'],
					'type' => DatePicker::TYPE_INPUT,
					'pluginOptions' => [
                        'format' => 'M dd, yyyy',
                        'autoclose'=>true,
                        'convertFormat'=>true
					]
                ]).PHP_EOL;
			} else {
				echo '</div></div>';
			}
		}
		?>
        </div>
		</div><div class="row">
		<div class="col-xs-12 col-sm-3">
			<?= $form->field($model, 'badge_involved')->textInput(['value'=>$model->badge_involved,'readonly'=> $isviolations]).PHP_EOL; ?>
		</div>
		<div class="col-xs-12 col-sm-5">
			<?= $form->field($model, 'involved_name')->textInput(['readOnly'=>'true']).PHP_EOL; ?>
		</div>
		</div><div class="row">
		<div class="col-xs-12 col-sm-3">
			<?= $form->field($model, 'badge_witness')->textInput(['value'=>$model->badge_witness,'readonly'=> $isviolations]).PHP_EOL; ?>
		</div>
		<div class="col-xs-12 col-sm-5">
			<?= $form->field($model, 'witness_name')->textInput(['readOnly'=>'true']).PHP_EOL; ?>
		</div>
		</div>
		<div class="row">
		<div class="col-xs-12 col-sm-3">
			<?= $form->field($model, 'vi_loc')->dropdownList($model->getLocations(),['maxlength'=>true]).PHP_EOL; ?>
		</div>
		</div>
	</div>
		<div class="col-xs-12">
			<?= $form->field($model, 'vi_rules')->dropDownList((new RuleList)->getRules($optionDataAttributes),
			[
			'options' => $optionDataAttributes,
			'value'=>explode(', ',$model->vi_rules),
			'multiple'=>true]).PHP_EOL; ?>
		</div>

		<div class="col-xs-12">
			<?= $form->field($model, 'vi_sum')->textInput().PHP_EOL; ?>
		</div>
		<div class="col-xs-12">
			<?= $form->field($model, 'vi_report')->textarea(['rows' => '2']).PHP_EOL; ?>
		</div>
		<div class="col-xs-12">
			<?= $form->field($model, 'vi_action')->textarea(['rows' => '2']).PHP_EOL; ?>
		</div>

<?php if(($model->isNewRecord==false) && yii::$app->controller->hasPermission('violations/board')) { ?>
		<div class="col-xs-12">
			<?= $form->field($model, 'hear_sum')->textarea(['rows' => '2']).PHP_EOL; ?>
		</div>
<?php } ?>
	</div>
	<div class="btn-group pull-right">

	<?= Html::submitButton($model->isNewRecord ? 'Save' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success done-Violations' : 'btn btn-primary done-Violations']) ?>
    </div>
<?php ActiveForm::end(); ?>
</div>


<script>
    $("#violations-was_guest").change(function() {
		if(document.getElementById("violations-was_guest").checked==true) {
			document.getElementById("warm").src = "/files/badge_photos/guest.png";
			document.getElementById("warm").alt = "Guest";
		} else {
			var rep_badge=$("#violations-badge_involved").val();
			if (rep_badge) { document.getElementById("warm").src = "/files/badge_photos/"+("0000"+rep_badge).slice(-5)+".jpg"; }
			else { document.getElementById("warm").src = "/files/badge_photos/warm.gif"; }
		}
	});

    $("#violations-vi_override").change(function() {
        if (document.getElementById("violations-vi_override").checked == true){
            $("#violations-vi_type").val('4');
        } else  {
			var cur_rules = $("#violations-vi_rules").val().toString();

			if(cur_rules) {
				if(cur_rules.indexOf(",") > 0) {
					spl_rules = cur_rules.split(",");

					var arrayLength = spl_rules.length;
					var lvl=0;
					for (var i = 0; i < arrayLength; i++) {
						if (spl_rules[i].slice(-1) > lvl) {lvl = spl_rules[i].slice(-1);};
					}
					$("#violations-vi_type").val(lvl);

				} else {
					$("#violations-vi_type").val(cur_rules.slice(-1));
				}
			} else {
				$("#violations-vi_type").val('1'); }
		}
    });



</script>