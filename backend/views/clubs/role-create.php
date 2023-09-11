<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Clubs */

$this->title = 'Create Club Role';
if(yii::$app->controller->hasPermission('site/admin-menu')) {
	$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']]; }
$this->params['breadcrumbs'][] = ['label' => 'Member Club List', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Club Roles', 'url' => ['roles']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="clubsroles-create">
    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form_role', [
        'model' => $model,
    ]) ?>

</div>
