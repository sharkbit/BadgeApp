<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Clubs */

$this->title = 'Update Official : ' . $model->first_name.' '.$model->last_name;
$this->params['breadcrumbs'][] = ['label' => 'Legislative Emails', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->first_name.' '.$model->last_name, 'url' => ['update', 'id' => $model->contact_id]];
$this->params['breadcrumbs'][] = 'Update';

?>
<div class="clubs-update">
<h2><?= Html::encode($this->title) ?></h2>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>