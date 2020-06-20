<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\WorkCredits */

$this->title = 'Update Work Credits: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Work Credits', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="work-credits-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
