<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Storeitems */

$this->title = 'Update';
$this->params['breadcrumbs'][] = ['label' => 'Store', 'url' => ['/sales/']];
$this->params['breadcrumbs'][] = ['label' => 'Store Items', 'url' => ['sales/stock']];
$this->params['breadcrumbs'][] = ['label' => $this->title];

echo $this->render('_view-tab-menu').PHP_EOL ?>
<div class="Storeitems-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
