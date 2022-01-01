<?php

use yii\helpers\Url;
use yii\helpers\Html;
$urlStatus = yii::$app->controller->getCurrentUrl();
?>

<ul class="nav nav-tabs">
<?php if(yii::$app->controller->hasPermission('rso-rpt/current')) { ?>
    <li class="<?php if($urlStatus['requestUrl']=='/rso-rpt/current')echo'active';?>"> 
		<a href="<?=Url::to(['/rso-rpt/current'])?>"><span class="glyphicon glyphicon-briefcase"></span> Current</a></li>
<?php } if(yii::$app->controller->hasPermission('rso-rpt/index')) { ?>
    <li class="<?php if($urlStatus['actionId']=='/rso-rpt/index')echo'active';?>">
		<a href="<?=Url::to(['/rso-rpt/index'])?>"><span class="glyphicon glyphicon-th-list"></span> Reports</a></li>
<?php } if(yii::$app->controller->hasPermission('rso-rpt/stats')) { ?>
	<li class="<?php if($urlStatus['actionId']=='stats')echo'active'; ?>"> 
		<a href="<?=Url::to(['/rso-rpt/stats'])?>"> <span class="glyphicon glyphicon-stats"></span> Stats</a></li>
<?php } ?>
</ul>
<br/>