<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Badges */

$this->title = 'Range Receipt';
$this->params['breadcrumbs'][] = ['label'=>'Sales', 'url'=>['/sales']];
$this->params['breadcrumbs'][] = ['label'=>'Purchases', 'url'=>['sales/purchases']];
$this->params['breadcrumbs'][] = $this->title;

 ?>

<div style="width:300px" class="badges-print-rcpt"> <!-- ng-controller="WallaWalla"> -->

<h2><?= Html::encode($this->title) ?></h2>
 
 <?= $this->render('/badges/_print-rcpt', [
        'MyRcpt' => $MyRcpt
    ]) ?>

</div>