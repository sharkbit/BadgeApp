<?php

use yii\helpers\Url;
use yii\helpers\Html;
use backend\models\Params;

$urlStatus = yii::$app->controller->getCurrentUrl();
$confParams  = Params::findOne('1');
?>

<ul class="nav nav-tabs">
    <li class="<?php if($urlStatus['actionId']=='index')echo'active';?>"> <a href="<?=Url::to(['/sales/index'])?>"><span class="glyphicon glyphicon-tag"></span> Store</a></li>
    <li class="<?php if($urlStatus['actionId']=='purchases')echo'active';?>"><a href="<?=Url::to(['/sales/purchases'])?>"> <span class="glyphicon glyphicon-list-alt"></span> Purchases</a></li>
<?php if(yii::$app->controller->hasPermission('sales/stock')) { ?>
	<li class="<?php if($urlStatus['actionId']=='stock')echo'active';?>"><a href="<?=Url::to(['/sales/stock'])?>">  <span class="glyphicon glyphicon-book"></span> Stock</a></li>
<?php } ?>
<?php if(yii::$app->controller->hasPermission('sales/report')) { ?>
	<li class="<?php if($urlStatus['actionId']=='report')echo'active';?>"><a href="<?=Url::to(['/sales/report'])?>">  <span class="glyphicon glyphicon-stats"></span> Report</a></li>
<?php } if(yii::$app->controller->hasPermission('sales/summary')) { ?>
	<li class="<?php if($urlStatus['actionId']=='summary')echo'active';?>"><a href="<?=Url::to(['/sales/summary'])?>">  <span class="glyphicon glyphicon-stats"></span> Sales Summary</a></li>
<?php } ?>
	<li class="<?php if($urlStatus['actionId']=='help') echo 'active'; ?>"><a href="<?=Url::to(['/sales/help'])?>"> <span class="glyphicon glyphicon-info-sign"></span> Help </a></li>
</ul>
