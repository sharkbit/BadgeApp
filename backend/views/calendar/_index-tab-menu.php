<?php

use yii\helpers\Url;
use yii\helpers\Html;
$urlStatus = yii::$app->controller->getCurrentUrl();


use backend\models\AgcCal;

$testConflict=0;
if(yii::$app->controller->hasPermission('calendar/conflict')) {
	if(yii::$app->controller->hasPermission('calendar/all')) {
		$testConflict = (new AgcCal)::find()->where(['conflict' => 1])->andWhere(['deleted'=>0])
		->andWhere(['>=','event_date' , date("Y-m-d 00:00",strtotime(yii::$app->controller->getNowTime())) ])
		->count();
	} else {
		$testConflict = (new AgcCal)::find()->where(['conflict' => 1])->andWhere(['deleted'=>0])
		->andWhere(['>=','event_date' , date("Y-m-d 00:00",strtotime(yii::$app->controller->getNowTime())) ])
		->andWhere(['in','agc_calendar.club_id',json_decode(Yii::$app->user->identity->clubs)])
		->count();
	}
} ?>

<style>
.blinking{
	animation:blinkingText 1.2s infinite;
}
@Keyframes blinkingText{
	  0%{ color: red; }
	 49%{ color: red; }
	 60%{ color: transparent; }
	 99%{ color: transparent; }
	100%{ color: red; }
}
</style>

<ul class="nav nav-tabs">
<!-- <?php
	//if((yii::$app->controller->hasPermission('calendar/modify')) || ($model->badge_number==$_SESSION['badge_number'])) {
		//if(yii::$app->controller->hasPermission('badge/create')) { $mystr='Renew / Update'; } else { $mystr='Update'; }
		?>
	<li class="<?php if($urlStatus['actionId']=='update')echo'active';?>"> <a href="<?=Url::to(['/calendar/update'])?>"> <span class="glyphicon glyphicon-pencil"></span> </a></li>
<?php //}  ?> -->

    <li class="<?php if($urlStatus['actionId']=='index')echo'active';?>"><a href="<?=Url::to(['/calendar/index'])?>">  <span class="glyphicon glyphicon-th"></span> Active Events</a></li>
<?php if(yii::$app->controller->hasPermission('calendar/recur')) { ?>
	<li class="<?php if($urlStatus['actionId']=='recur')echo'active';?>"><a href="<?=Url::to(['/calendar/recur'])?>">  <span class="glyphicon glyphicon-th"></span> Recurring Master Events</a></li> <?php } ?>
<?php if($testConflict > 0) { ?>
	<li class="<?php if($urlStatus['actionId']=='conflict')echo'active';?>"><a href="<?=Url::to(['/calendar/conflict'])?>"><span class="glyphicon glyphicon-warning-sign blinking"> </span> <span style="color:red"> Conflicted Events</span></a></li> <?php } ?>
<?php if(yii::$app->controller->hasPermission('calendar/inactive')) { ?>
	<li class="<?php if($urlStatus['actionId']=='inactive')echo'active';?>"><a href="<?=Url::to(['/calendar/inactive'])?>"> <span class="glyphicon glyphicon-trash"></span> Inactive Events</a></li> <?php } ?>
<?php if(yii::$app->controller->hasPermission('cal-setup/index')) { ?>
	<li class="<?php if($urlStatus['actionId']=='cal-setup')echo'active';?>"><a href="<?=Url::to(['/cal-setup/'])?>"> - <span class="glyphicon glyphicon-cog"></span> Calendar Setup -</a></li> <?php } ?>

</ul>
