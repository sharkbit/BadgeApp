<?php

/* @var $this yii\web\View */

$this->title = 'ADMIN';
$activeUser = yii::$app->controller->getActiveuser();
use backend\components\Menu;
 //Menu::widget(['type'=>'home'])
?>

<div class="row">
    <div class="col">
        <h1 class="text-center">AGC Admin Functions</h1>
        <div class="menu-box-parent">

			<?= Menu::widget(['type'=>'admin','privilege'=> $activeUser->privilege]) ?>

        </div>
    </div>
</div>