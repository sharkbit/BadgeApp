<?php

use yii\helpers\Url;
use yii\helpers\Html;
$urlStatus = yii::$app->controller->getCurrentUrl();

?>

<ul class="nav nav-tabs">
<!-- <?php
	//if((yii::$app->controller->hasPermission('calendar/modify')) || ($model->badge_number==$_SESSION['badge_number'])) {
		//if(yii::$app->controller->hasPermission('badge/create')) { $mystr='Renew / Update'; } else { $mystr='Update'; } 
		?>
	<li class="<?php if($urlStatus['actionId']=='update')echo'active';?>"> <a href="<?=Url::to(['/calendar/update'])?>"> <span class="glyphicon glyphicon-pencil"></span> </a></li>
<?php //}  ?> -->

    <li class="<?php if($urlStatus['actionId']=='index')echo'active';?>"><a href="<?=Url::to(['/calendar/index'])?>">  <span class="glyphicon glyphicon-th"></span> Active Events</a></li>
    <li class="<?php if($urlStatus['actionId']=='recur')echo'active';?>"><a href="<?=Url::to(['/calendar/recur'])?>">  <span class="glyphicon glyphicon-th"></span> Recurring Master Events</a></li>
	<li class="<?php if($urlStatus['actionId']=='inactive')echo'active';?>"><a href="<?=Url::to(['/calendar/inactive'])?>"> <span class="glyphicon glyphicon-trash"></span> Inactive Events</a></li>
	<li class="<?php if($urlStatus['actionId']=='conflict')echo'active';?>"><a href="<?=Url::to(['/calendar/conflict'])?>"> <span class="glyphicon glyphicon-cloud"></span> Conflicted Events</a></li>
<?php if(yii::$app->controller->hasPermission('cal-setup/index')) { ?>
<li class="<?php if($urlStatus['actionId']=='cal-setup')echo'active';?>"><a href="<?=Url::to(['/cal-setup/'])?>"> - <span class="glyphicon glyphicon-cog"></span> Calendar Setup -</a></li>
<?php } ?>

</ul>
