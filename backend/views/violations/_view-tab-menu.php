<?php

use yii\helpers\Url;
use yii\helpers\Html;
$urlStatus = yii::$app->controller->getCurrentUrl();
?>

<ul class="nav nav-tabs">
    <li class="<?php if($urlStatus['requestUrl']=='/violations/index')echo'active';?>"> <a href="<?=Url::to(['/violations/index'])?>"><span class="glyphicon glyphicon-eye-open"></span> Violations</a></li>
<?php if(yii::$app->controller->hasPermission('violations/create')) { ?>
    <li class="<?php if($urlStatus['actionId']=='create')echo'active';?>"><a href="<?=Url::to(['/violations/create'])?>"><span class="glyphicon glyphicon-plus"></span> Add Citation</a></li>
<?php } if(yii::$app->controller->hasPermission('violations/report')) { ?>
	<li class="<?php if($urlStatus['actionId']=='report')echo'active'; ?>"> <a href="<?=Url::to(['/violations/report'])?>"> <span class="glyphicon glyphicon-book"></span> Report</a></li>
<?php } if(yii::$app->controller->hasPermission('violations/stats')) { ?>
	<li class="<?php if($urlStatus['actionId']=='stats')echo'active'; ?>"> <a href="<?=Url::to(['/violations/stats'])?>"> <span class="glyphicon glyphicon-stats"></span> Stats</a></li>
<?php } if(yii::$app->controller->hasPermission('rules/index')) { ?>
    <li class="<?php if($urlStatus['requestUrl']=='/rules/index')echo'active';?>"><a href="<?=Url::to(['/rules/index'])?>"> <span class="glyphicon glyphicon-wrench"></span> Rules</a></li>
<?php } ?>
</ul>
