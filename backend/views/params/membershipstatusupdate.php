<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Discount */

$this->title = 'Membership Status : ' . $model->act_name;
if(yii::$app->controller->hasPermission('site/admin-menu')) {
	$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']]; }
$this->params['breadcrumbs'][] = ['label' => 'Membership Stauts', 'url' => ['/params/membershipstatus']];
$this->params['breadcrumbs'][] = ['label' => $model->act_name.' ('.$model->act_id.')', 'url' => ['membershipstatusview', 'id' => $model->act_id]];
$this->params['breadcrumbs'][] = 'Update';

?>
<div class="discount-update">
	<h2><?= Html::encode($this->title) ?></h2>

	<?php $form = ActiveForm::begin(['id'=>'discountupdate']); ?>

	<div class="col-xs-12">

		<div class="row">
			<div class="col-xs-3 col-sm-3" <?php if($model->isNewRecord){echo 'style="display: none;"';} ?> >
				<?= $form->field($model, 'act_id')->textInput(['maxlength' => true,'readonly'=>true]) ?>
			</div>
			<div class="col-xs-12 col-sm-3">
				<?= $form->field($model, 'act_name')->textInput(['maxlength'=>true]) ?>
			</div>
			<div class="col-xs-6 col-sm-3">
				<?= $form->field($model, 'act_short').PHP_EOL; ?>
			</div>
			<div class="col-xs-6 col-sm-3">
				<?= $form->field($model, 'act_active')->DropDownList(['0'=>'No','1'=>'Yes']).PHP_EOL ?>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-6 col-sm-2">
				<?= $form->field($model, 'act_login')->DropDownList(['0'=>'No','1'=>'Yes']).PHP_EOL ?>
			</div>
			<div class="col-xs-6 col-sm-2">
				<?= $form->field($model, 'act_prefill')->DropDownList(['0'=>'No','1'=>'Yes']).PHP_EOL ?>
			</div>
			<div class="col-xs-6 col-sm-2">
				<?= $form->field($model, 'act_new')->DropDownList(['0'=>'No','1'=>'Yes']).PHP_EOL ?>
			</div>
			<div class="col-xs-6 col-sm-2">
				<?= $form->field($model, 'act_renew')->DropDownList(['0'=>'No','1'=>'Yes']).PHP_EOL ?>
			</div>
			<div class="col-xs-6 col-sm-2">
				<?= $form->field($model, 'act_signup')->DropDownList(['0'=>'No','1'=>'Yes']).PHP_EOL ?>
			</div>
			<div class="col-xs-6 col-sm-2">
				<?= $form->field($model, 'act_color')->DropDownList(['green'=>'Green','yellow'=>'Yellow','orange'=>'Orange','red'=>'Red','black'=>'Black']).PHP_EOL ?>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<?= $form->field($model, 'act_desc')->textarea(['rows' => 3,'cols' => 50,'class' => 'form-control']) ?>
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
