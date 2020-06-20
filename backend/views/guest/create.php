<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Guest */

$this->title = 'Add Visitor';
$this->params['breadcrumbs'][] = ['label' => 'Visitor Log', 'url' => ['guest/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="guest-create" >

    <?= $this->render('_form', [
        'model' => $model,
        //'badgeArray'=>$badgeArray,
    ]) ?>

</div>