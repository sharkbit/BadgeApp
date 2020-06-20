<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\search\BadgesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="row">
	<div class="col-sm-7">
		<h1><?= Html::encode($this->title) ?></h1>
	</div>
    <div class="guest-search">
		<?php $form = ActiveForm::begin([
			'action' => ['index'],
			'method' => 'get',
			'id'=>'viewPrintGuestFilter',
		]); ?>
		<div class="col-sm-2">

			<?= $form->field($model, 'atRange_condition')->dropDownlist(['all'=>'All','atRange'=>'At Range','gone'=>'Past Visitors'],['value'=>$model->atRange_condition !=null ? $model->atRange_condition : 'atRange'])->label('Fliter by') ?>
		</div>
		<div class="col-sm-2">
			<div class=" form-group btn-group ">
			<br>
				<?= Html::submitButton('<i class="fa fa-search" aria-hidden="true"></i> Search', ['class' => 'btn btn-primary']) ?>
				<?php //= Html::a('<i class="fa fa-eraser" aria-hidden="true"></i> Reset', ['/guest/index'],['class' => 'btn btn-danger']) ?>
			</div>
		</div>

		<?php ActiveForm::end(); ?>

	</div>
</div>