<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Badges */

$this->title = 'Range Receipt';
$this->params['breadcrumbs'][] = ['label' => 'Range Badges', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $MyRcpt->badge_number, 'url' => ['/badges/view-renewal-history','badge_number'=>$MyRcpt->badge_number]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div style="width:300px" class="badges-print-rcpt"> <!-- ng-controller="WallaWalla"> -->

<h2><?= Html::encode($this->title) ?></h2>
 
 <?= $this->render('_print-rcpt', [
        'MyRcpt' => $MyRcpt
    ]) ?>

</div>