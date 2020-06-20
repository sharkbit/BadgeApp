<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Badges */

$this->title = 'Issue New Range Badge';
$this->params['breadcrumbs'][] = ['label' => 'Range Badges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="badges-create" ng-controller="CreateBadgeController">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
		'confParams' => $confParams
		
    ]) ?>

</div>