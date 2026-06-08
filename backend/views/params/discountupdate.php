<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Discount */

$this->title = 'Update Discount : ' . $model->dis_name;
if(yii::$app->controller->hasPermission('site/admin-menu')) {
	$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']]; }
$this->params['breadcrumbs'][] = ['label' => 'Discount List', 'url' => ['/params/discount']];
$this->params['breadcrumbs'][] = ['label' => $model->dis_name.' ('.$model->dis_id.')', 'url' => ['discountview', 'id' => $model->dis_id]];
$this->params['breadcrumbs'][] = 'Update';

?>
<div class="discount-update">
	<h2><?= Html::encode($this->title) ?></h2>

	<?php $form = ActiveForm::begin(['id'=>'discountupdate']); ?>

	<div class="col-xs-12">

		<div class="row">
			<div class="col-xs-3 col-sm-3" <?php if($model->isNewRecord){echo 'style="display: none;"';} ?> >
				<?= $form->field($model, 'dis_id')->textInput(['maxlength' => true,'readonly'=>true]) ?>
			</div>
			<div class="col-xs-12 col-sm-3">
				<?= $form->field($model, 'dis_name')->textInput(['maxlength'=>true]) ?>
			</div>
			<div class="col-xs-6 col-sm-3">
				<?= $form->field($model, 'dis_short').PHP_EOL; ?>
			</div>
			<div class="col-xs-6 col-sm-3">
				<?= $form->field($model, 'dis_amount').PHP_EOL; ?>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-6 col-sm-3">
				<?= $form->field($model, 'dis_active')->DropDownList(['0'=>'No','1'=>'Yes']).PHP_EOL ?>
			</div>
			<div class="col-xs-6 col-sm-3">
				<?= $form->field($model, 'dis_def')->DropDownList(['0'=>'No','1'=>'Yes']).PHP_EOL ?>
			</div>
			<div class="col-xs-12 col-sm-6">
				<?= $form->field($model, 'dis_allowed')->DropDownList(['new'=>'New','renew'=>'Renew'],['multiple' => true]).PHP_EOL ?>
			</div>
		</div>
	</div>

	<div class="row">
	    <div class="form-group btn-group pull-right">
			<?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
			<?= Html::resetButton('Reset <i class="fa fa-eraser"> </i>', ['class' => 'btn btn-danger']) ?>
		</div>
	</div>

	<?php ActiveForm::end(); ?>
</div>
