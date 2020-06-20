<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\WorkCredits */

$this->title = $model->mass_subject;
$this->params['breadcrumbs'][] = ['label' => 'Admin Function', 'url' => '/badge/admin-function'];
$this->params['breadcrumbs'][] = ['label' => 'Mass Emails', 'url' => ['mass-email/index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['mass-email/update?id='.$model->id]];

?>
<div class="guest-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
