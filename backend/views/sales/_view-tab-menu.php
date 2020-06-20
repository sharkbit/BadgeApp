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
<?php }
	if(yii::$app->controller->hasPermission('payment/inventory') && (strlen($confParams->qb_token)>2 || strlen($confParams->qb_oa2_refresh_token)>2))  { ?>
    <li class="<?php if($urlStatus['actionId']=='view')echo'active';?>"><a href="<?=Url::to(['inventory'])?>"><span class="glyphicon glyphicon-save"></span> (QB) Pull Inventory</a></li>
    <li class="<?php if($urlStatus['actionId']=='view')echo'active';?>"><a href="<?=Url::to(['/sales'])?>"> <span class="glyphicon glyphicon-screenshot"></span> stuff</a></li>
<?php } ?>
</ul>
