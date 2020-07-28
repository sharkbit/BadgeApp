<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\BadgesDatabase */

$this->title = 'Update Badges Database: ' . $model->badge_number;
$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']];
$this->params['breadcrumbs'][] = ['label' => 'Badge Databases', 'url' => ['range-badge-database/index']];
$this->params['breadcrumbs'][] = ['label' => $model->badge_number, 'url' => ['view', 'badge_number' => $model->badge_number]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="badges-database-update" ng-controller="BadgesDatabaseController">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
