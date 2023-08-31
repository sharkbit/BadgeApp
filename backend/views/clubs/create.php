<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Clubs */

$this->title = 'Create Club';
if(yii::$app->controller->hasPermission('site/admin-menu')) {
	$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']]; }
$this->params['breadcrumbs'][] = ['label' => 'Member Club List', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clubs-create">
	<div class="col-xs-12">
		<?= $this->render('_view-tab-menu') ?>
	</div>
    <div class="col-xs-12">
		<h2><?= Html::encode($this->title) ?></h2>
	</div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
