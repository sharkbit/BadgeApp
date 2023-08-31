<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\BadgeToRole */

$this->title = 'Update Officer';
if(yii::$app->controller->hasPermission('site/admin-menu')) {
	$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']]; }
$this->params['breadcrumbs'][] = ['label' => 'Member Club List', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Club Roles', 'url' => ['roles']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
	<div class="col-xs-12">
		<?= $this->render('_view-tab-menu') ?>
	</div>

	<div class="col-xs-12">
		<h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form_officer', [
        'model' => $model,
    ]) ?>

	</div>
</div>
