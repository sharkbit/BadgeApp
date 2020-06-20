<?php

use yii\helpers\Url;
$urlStatus = yii::$app->controller->getCurrentUrl();
?>

<ul class="nav nav-tabs">
<?php /*
	if((yii::$app->controller->hasPermission('badges/modify')) || ($model->badge_number==$_SESSION['badge_number'])) {
		if(yii::$app->controller->hasPermission('badge/create')) { $mystr='Renew / Update'; } else { $mystr='Update'; }  ?>
	<li class="<?php if($urlStatus['actionId']=='update')echo'active';?>"> <a href="<?=Url::to(['/badges/update'])?>"> <span class="glyphicon glyphicon-pencil"></span> <?=$mystr?></a></li>
<?php } */ ?>

    <li class="<?php if($urlStatus['actionId']=='facility')echo'active';?>"> <a href="<?=Url::to(['/cal-setup/facility'])?>"><span class="glyphicon glyphicon-home"></span> Facilities</a></li>
    <li class="<?php if($urlStatus['actionId']=='rangestatus')echo'active';?>"><a href="<?=Url::to(['/cal-setup/rangestatus'])?>">  <span class="glyphicon glyphicon-road"></span> Range Status</a></li>
    <li class="<?php if($urlStatus['actionId']=='eventstatus')echo'active';?>"><a href="<?=Url::to(['/cal-setup/eventstatus'])?>"> <span class="glyphicon glyphicon-flag"></span> Event Status</a></li>
    <li class="<?php if($urlStatus['actionId']=='clubs')echo'active';?>"><a href="<?=Url::to(['/cal-setup/clubs'])?>"><span class="glyphicon glyphicon-thumbs-down"></span> Calendar Clubs</a></li>

    <li class="<?php if($urlStatus['actionId']=='Calendar')echo'active';?>"><a href="<?=Url::to(['/calendar/index'])?>"> - <span class="glyphicon glyphicon-th"></span> Calendar - </a></li>
</ul>
