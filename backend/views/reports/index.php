<?php

use backend\components\Menu;

/* @var $this yii\web\View */

$this->title = 'Reports';
$this->params['breadcrumbs'][] = ['label'=>'Reports'];
$activeUser = Yii::$app->controller->getActiveUser();
?>

<div class="row">
	<div class="col">
		<h1 class="text-center"><?= \yii\helpers\Html::encode($this->title) ?></h1>
		<div class="menu-box-parent">
			<?= Menu::widget(['type' => 'reports', 'privilege' => $activeUser->privilege]) ?>
		</div>
	</div>
</div>
