<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Violations */

$this->title = 'Record Violation';
$this->params['breadcrumbs'][] = ['label' => 'Violations', 'url' => ['violations/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="violations-create" >

    <?= $this->render('_form', [
        'model' => $model,
        //'badgeArray'=>$badgeArray,
    ]) ?>

</div>