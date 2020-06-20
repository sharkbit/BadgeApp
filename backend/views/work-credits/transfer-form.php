<?php 

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\date\DatePicker;

$this->title = 'Credit Transfer form';
$this->params['breadcrumbs'][] = ['label' => 'Work Credit', 'url' => ['/work-credits/index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?= $this->render('_view-tab-menu') ?>

<div class="work-transfer-form" ng-controller="WorkTransferForm">
	<?php $form = ActiveForm::begin(); ?>
	<div class="row">
		<div class="col-xs-12">
		<h3> Transfer From </h3>
		</div>
		<div class="col-xs-2">
	<?php if(yii::$app->controller->hasPermission('work-credits/approve')) {
			echo $form->field($model, 'badge_number')->textInput([]);
		} else {
			echo $form->field($model, 'badge_number')->textInput(['readOnly'=>'true','value'=>$_SESSION['badge_number']]);
			} ?>
		</div>
		<div class="col-xs-4">
			<?php echo Html::label("Badge Holder",'',['id'=>'badge_name_label']), PHP_EOL; ?>
			<?php echo Html::textinput("badge_name",'',['readonly'=>true,'id'=>'cred_xfer-badge_name','class'=>"form-control"]), PHP_EOL; ?>
		</div>

		<div class="col-xs-3">
			<div id="credit-block_a">
				<?php echo Html::radio('wc-Radio' ,false,['id'=>'wc_Radio_this','value'=>'this', 'name'=>'btnname', 'uncheckValue'=>null]), PHP_EOL; ?> 
				<?php echo Html::label("Current Years Credits",'',['id'=>'cur_year_label']), PHP_EOL; ?>
				<?php echo Html::textinput("total_credit_this",'',['id'=>'cred_xfer-total_this','class'=>"form-control"]), PHP_EOL; ?>
			</div>
		</div>
		<div class="col-xs-3">
			<div id="credit-block_b">
				<?php echo Html::radio('wc-Radio' ,false, ['id'=>'wc_Radio_Last','value'=>'last','name'=>'btnname', 'uncheckValue'=>null]), PHP_EOL; ?>
				<?php echo Html::label("Last Years Credits",'',['id'=>'las_year_label']), PHP_EOL; ?>
				<?php echo Html::textinput("total_credit_last",'',['id'=>'cred_xfer-total_last','class'=>"form-control"]), PHP_EOL; ?>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
		<h3> Transfer To </h3>
		</div>
		<div class="col-xs-2">
			<?php echo Html::label("Badge Number",'',['id'=>'to_badge_number_label']), PHP_EOL; ?>
			<?php echo Html::textinput("to_badge_number",'',['id'=>'cred_xfer-to_badge_number','class'=>"form-control"]), PHP_EOL; ?>
		</div>
		<div class="col-xs-4">
			<?php echo Html::label("Badge Holder",'',['id'=>'to_badge_name_label']), PHP_EOL; ?>
			<?php echo Html::textinput("to_badge_name",'',['readonly'=>true,'id'=>'cred_xfer-to_badge_name','class'=>"form-control"]), PHP_EOL; ?>
		</div>

		<div class="col-xs-3">
			<?php echo Html::label("Credits to Transfer",'',['id'=>'my_label']), PHP_EOL; ?>
			<?php echo Html::textinput("to_credits",'',['id'=>'cred_xfer-to_credits','class'=>"form-control"]), PHP_EOL; ?>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 col-sm-8">
			<?= $form->field($model, 'remarks')->textArea(['rows'=>1]) ?>
		</div>
		<div class="col-xs-4">
			<div class="form-group btn-group pull-right">
       			<?= Html::submitButton($model->isNewRecord ? 'Transfer' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    			<?= Html::resetButton('Reset <i class="fa fa-eraser"> </i>', ['class' => 'btn btn-danger']) ?>
    		</div>
		</div>
		
	</div>

	<?php ActiveForm::end(); ?>  
</div>