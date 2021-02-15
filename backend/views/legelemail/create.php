<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Params */

$this->title = 'Add Legislative Contact';
$this->params['breadcrumbs'][] = ['label' => 'Legislative Contacts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>
<div class="Legelemail-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
