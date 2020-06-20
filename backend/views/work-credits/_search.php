<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\search\WorkCreditsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="work-credits-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>
    <?= $form->field($model, 'badge_number') ?>
    <?= $form->field($model, 'work_date') ?>
    <?= $form->field($model, 'work_hours') ?>
    <?= $form->field($model, 'project_name') ?>
    <?php // echo $form->field($model, 'remarks') ?>
    <?php // echo $form->field($model, 'autherized_by') ?>
    <?php // echo $form->field($model, 'status') ?>
    <?php // echo $form->field($model, 'updated_at') ?>
    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>