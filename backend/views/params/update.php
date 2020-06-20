<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Params */

$this->title = 'Configuration';
$this->params['breadcrumbs'][] = ['label' => 'Admin Function', 'url' => ['badge/admin-function']];
$this->params['breadcrumbs'][] = ['label' => 'Settings', 'url' => ['params/update']];
?>
<div class="params-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
