<?php

use yii\helpers\Url;
use yii\helpers\Html;
$urlStatus = yii::$app->controller->getCurrentUrl();
?>

<ul class="nav nav-tabs">
<?php if(yii::$app->controller->hasPermission('rso-rpt/current')) { ?>
    <li class="<?php if($urlStatus['actionId']=='current') echo 'active';?>"> 
		<a href="<?=Url::to(['/rso-rpt/current'])?>"><span class="glyphicon glyphicon-briefcase"></span> Current</a></li>
<?php } if(yii::$app->controller->hasPermission('rso-rpt/index')) { ?>
    <li class="<?php if($urlStatus['actionId']=='index') echo 'active';?>">
		<a href="<?=Url::to(['/rso-rpt/index'])?>"><span class="glyphicon glyphicon-th-list"></span> Reports</a></li>
<?php } if(yii::$app->controller->hasPermission('rso-rpt/sticker')) { ?>
	<li class="<?php if($urlStatus['actionId']=='sticker') echo 'active'; ?>"> 
		<a href="<?=Url::to(['/rso-rpt/sticker'])?>"> <span class="glyphicon glyphicon-sound-dolby"></span> Stickers</a></li>
<?php } ?>
	<li class="<?php if($urlStatus['actionId']=='help') echo 'active'; ?>"> 
		<a href="<?=Url::to(['/rso-rpt/help'])?>"> <span class="glyphicon glyphicon-info-sign"></span> Help </a></li>
<?php if(yii::$app->controller->hasPermission('rso-rpt/settings')) { ?>
	<li class="<?php if($urlStatus['actionId']=='settings') echo 'active'; ?>"> 
		<a href="<?=Url::to(['/rso-rpt/settings'])?>"> <span class="glyphicon glyphicon-cog"></span> Settings</a></li>
<?php } ?>		
		
</ul>
<br/>
