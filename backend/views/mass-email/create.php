<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Guest */

$this->title = 'Create Mass Email';
$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']];
$this->params['breadcrumbs'][] = ['label' => 'Mass Emails', 'url' => ['mass-email/index']];
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<div class="guest-create" >

    <?= $this->render('_form', [
        'model' => $model,
        //'badgeArray'=>$badgeArray,
    ]) ?>

</div>