<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\sales */

$this->title = 'Update';
$this->params['breadcrumbs'][] = ['label' => 'Sales', 'url' => ['/sales']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/sales/update','id'=>$model->item_id]];

echo $this->render('_view-tab-menu').PHP_EOL ?>
<div class="sales-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
