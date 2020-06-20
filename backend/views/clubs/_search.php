<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\search\ClubsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="clubs-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'club_id') ?>
    <?= $form->field($model, 'club_name') ?>
    <?= $form->field($model, 'short_name') ?>
    <?= $form->field($model, 'status') ?>
    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

	<?php ActiveForm::end(); ?>

</div>