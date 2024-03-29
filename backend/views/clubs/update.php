<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Clubs */

$this->title = 'Update Club : ' . $model->club_name;
if(yii::$app->controller->hasPermission('site/admin-menu')) {
	$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']]; }
$this->params['breadcrumbs'][] = ['label' => 'Member Club List', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->club_name.' ('.$model->club_id.')', 'url' => ['view', 'id' => $model->club_id]];
$this->params['breadcrumbs'][] = 'Update';

?>
<div class="row">
	<div class="col-xs-12">
		<?= $this->render('_view-tab-menu') ?>
	</div>
	<div class="col-xs-12" class="clubs-update">
		<h2><?= Html::encode($this->title) ?></h2>
	</div>
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>