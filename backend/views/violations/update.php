<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\WorkCredits */

$this->title = 'Update Violation: ';// . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Violations', 'url' => ['violations/index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="violations-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
