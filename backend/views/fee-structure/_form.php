<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\money\MaskMoney;
/* @var $this yii\web\View */
/* @var $model backend\models\FeesStructure */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="fees-structure-form" ng-controller="FeesStructureForm">

    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => false,
        'enableClientValidation' => true,
        'id' => 'ajax'
        ]); ?>

    <?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?>
    <?php if($model->isNewRecord) { ?>
    <?=  $form->field($model, 'type')->radioList(['badge_fee'=>'Badge Fee','certification'=>'Certification Fee'],['value'=>'badge_fee']) ?>
    <?php } else { ?>
    <?=  $form->field($model, 'type')->radioList(['badge_fee'=>'Badge Fee','certification'=>'Certification Fee'],[]) ?>
    <?php } ?>
    <?= $form->field($model, 'membership_id')->dropDownlist($model->getMembershipList(),['prompt'=>'membership type'])?>

    <?= $form->field($model, 'fee')->widget(MaskMoney::classname(), [
    	'pluginOptions' => [
        'allowNegative' => false
    	]
	]); ?>
	<?= $form->field($model, 'sku_full')->textInput(['maxlength' => true]) ?>
	<?= $form->field($model, 'sku_half')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'status')->dropDownList([ '0'=>'active', '1'=>'inactive', ], ['prompt' => 'status']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset <i class="fa fa-eraser"> </i>', ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
