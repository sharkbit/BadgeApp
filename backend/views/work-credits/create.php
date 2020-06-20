<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\WorkCredits */

$this->title = 'Create Work Credit';
$this->params['breadcrumbs'][] = ['label' => 'Work Credits', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/work-credits/create']];

?>
<div class="work-credits-create">

    <?= $this->render('_form', [
        'model' => $model,
        'badgeArray'=>$badgeArray,
    ]) ?>

</div>
