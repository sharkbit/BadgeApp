<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\roles */
/* @var $form yii\widgets\ActiveForm */

$numList = '';
for ($x = 1; $x <= 99; $x++) {
	$numList .=json_encode([$x=>$x]);
}
$numList = json_decode(str_replace('}{',',',$numList));
?>

<div class="roles-form">
<?php $form = ActiveForm::begin(); ?>
<div class="row" style="margin: auto;">
	<div class="col-xs-6 col-sm-2" <?php if($model->isNewRecord){echo 'style="display: none;"';} ?> >
	<?= $form->field($model, 'role_id')->textInput(['maxlength' => true,'readonly'=>true]) ?>
	</div>
	<div class="col-xs-12 col-sm-4">
	<?= $form->field($model, 'role_name')->textInput(['maxlength' => true]) ?>
	</div>
	<div class="col-xs-12 col-sm-4">
	<?= $form->field($model, 'disp_order')->dropDownList($numList,['value'=>$model->isNewRecord ? mt_rand(1, 100) : $model->disp_order]) ?>
	</div>
	<?php if(yii::$app->controller->hasPermission('clubs/role-create')) { ?>
    <div class="form-group btn-group pull-right">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    	<?= Html::resetButton('Reset <i class="fa fa-eraser"> </i>', ['class' => 'btn btn-danger']) ?>
    </div>
<?php } ?>
</div>
<?php ActiveForm::end(); ?>
</div>


