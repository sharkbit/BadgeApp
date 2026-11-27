<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Params;

/* @var $this yii\web\View */
/* @var $model backend\models\Privileges */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>
<div class="row" style="margin: auto;">

<div class="col-xs-12">

	<div class="row" >
<!--	<div class="col-xs-2 col-sm-2 col-md-1"<?php if($model->isNewRecord){echo 'style="display: none;"';} ?> >
		<?= $form->field($model, 'id')->textInput(['maxlength' => true,'readonly'=>true]) ?>
	</div> -->
	<div class="col-xs-6 col-sm-4 col-md-2">
		<?= $form->field($model, 'priv_sort')->textInput(['maxlength' => true]) ?>
	</div>
	<div class="col-xs-12 col-sm-6 col-md-4">
		<?= $form->field($model, 'privilege')->textInput(['maxlength' => true]) ?>
	</div>
	<div class="col-xs-6 col-sm-4 col-md-2">
		<?= $form->field($model, 'timeout')->textInput(['maxlength' => true]) ?>
	</div>
	<div class="col-xs-6 col-sm-4 col-md-2">
		<?= $form->field($model, 'restricted')->dropDownList(
			[1 => 'Yes', 0 => 'No'],
			['disabled' => !Yii::$app->controller->hasPermission('privileges/raw', 1)]
		) ?>
	</div>
    <div class="form-group btn-group pull-right">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    	<?= Html::resetButton('Reset <i class="fa fa-eraser"> </i>', ['class' => 'btn btn-danger']) ?>
    </div>

	</div>
</div>
</div>
<?php ActiveForm::end(); ?>

<?= $this->render('_show_priv', ['model' => $model]) ?>
