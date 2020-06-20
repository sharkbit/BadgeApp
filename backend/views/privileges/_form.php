<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Privileges */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>
<div class="row" style="margin: auto;">

<div class="col-xs-12 col-sm-8">

	<div class="row" <?php if($model->isNewRecord){echo 'style="display: none;"';} ?> >
	<?= $form->field($model, 'id')->textInput(['maxlength' => true,'readonly'=>true]) ?>

	</div><div class="row">
    <?= $form->field($model, 'privilege')->textInput(['maxlength' => true]) ?>

	</div><div class="row">
    <?= $form->field($model, 'priv_sort')->textInput(['maxlength' => true]) ?>

	</div><div class="row">
	<?= $form->field($model, 'timeout')->textInput(['maxlength' => true]) ?>
	
    <div class="form-group btn-group pull-right">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    	<?= Html::resetButton('Reset <i class="fa fa-eraser"> </i>', ['class' => 'btn btn-danger']) ?>
    </div>

</div>
</div>
<?php ActiveForm::end(); ?>
</div>
