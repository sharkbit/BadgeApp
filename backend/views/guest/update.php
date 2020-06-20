<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\WorkCredits */

$this->title = 'Update Guest: ';// . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Visitor Log', 'url' => ['guest/index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="guest-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
