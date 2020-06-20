<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Params */

$this->title = 'Add Rule';
$this->params['breadcrumbs'][] = ['label' => 'Admin Function', 'url' => ['badge/admin-function']];
$this->params['breadcrumbs'][] = ['label' => 'Rules List', 'url' => ['rules/index']];
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>
<div class="rules-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
