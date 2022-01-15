<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Update Sticker:'.$model->sticker;
$this->params['breadcrumbs'][] = ['label' => 'RSO Reports - Stickers', 'url' => ['rso-rpt/sticker']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['rso-rpt/sticker-update?id='.$model->s_id]];
?>

<?=$this->render('_view-tab-menu').PHP_EOL; ?>

<?php $form = ActiveForm::begin([
	   // 'action' => [$url],
		'method' => 'post',
		'id'=>'rsoreportsformFilter'
	]); ?>

<div class="row">
	<?= $form->field($model, 's_id')->hiddenInput()->label(false) ?>
	<div class="col-xs-12 col-sm-2">
		<?= $form->field($model, 'sticker')->textInput(['readonly' => true,'maxlength'=>true]) ?>
	</div>
	<div class="col-xs-6 col-sm-2">
		<?= $form->field($model, 'status')->dropDownList($model->listStickerStatus()) ?>
	</div>
	<div class="col-xs-6 col-sm-4">
		<?= $form->field($model, 'holder')->textInput(['maxlength'=>true]) ?>
	</div>
	<div class="col-xs-6 col-sm-4">
		<?= $form->field($model, 'updated')->textInput(['readonly' => true,'maxlength'=>true]) ?>
	</div>
</div>

<div class="row">
	<div class="col-xs-12 col-sm-12">
		<div class="btn-group pull-right">
			<?= Html::submitButton('<i class="fa fa-check" aria-hidden="true"></i> Save ', ['class' => 'btn btn-success','id'=>'save_btn']).PHP_EOL;  ?>
		</div>
	</div>
</div>

<?php ActiveForm::end(); ?>
