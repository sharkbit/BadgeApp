<?php

use yii\helpers\Url;
use yii\helpers\Html;
$urlStatus = yii::$app->controller->getCurrentUrl();
?>

<ul class="nav nav-tabs">
    <li class="<?php if($urlStatus['actionId']=='index')echo'active';?>"> <a href="<?=Url::to(['/guest/index'])?>"><span class="glyphicon glyphicon-eye-open"></span> Guest</a></li>
<?php if(yii::$app->controller->hasPermission('guest/create')) { ?>
    <li class="<?php if($urlStatus['actionId']=='create')echo'active';?>"><a href="<?=Url::to(['/guest/create'])?>"><span class="glyphicon glyphicon-plus"></span> Add Guest</a></li>
<?php } if(yii::$app->controller->hasPermission('guest/stats')) { ?>
	<li class="<?php if($urlStatus['actionId']=='stats')echo'active'; ?>"> <a href="<?=Url::to(['/guest/stats'])?>"> <span class="glyphicon glyphicon-stats"></span> Stats</a></li>
<?php } ?>
</ul>
