<?php

use yii\helpers\Url;
use yii\helpers\Html;
$urlStatus = yii::$app->controller->getCurrentUrl();

if($model->status=='pending')
	{Yii::$app->getSession()->setFlash('error', 'Badge is Pending!  Verify Badge Number and Bar Code!');}
else if ($model->status=='revoked')
	{Yii::$app->getSession()->setFlash('error', 'Badge is REVOKED!');}
else if ($model->status=='suspended')
	{Yii::$app->getSession()->setFlash('error', 'Badge is currently Suspended!');}
?>

<ul class="nav nav-tabs">
<?php
	if((yii::$app->controller->hasPermission('badges/modify')) || ($model->badge_number==$_SESSION['badge_number'])) {
		if(yii::$app->controller->hasPermission('badge/create')) { $mystr='Renew / Update'; } else { $mystr='Update'; }  ?>
	<li class="<?php if($urlStatus['actionId']=='update')echo'active';?>"> <a href="<?=Url::to(['/badges/update','badge_number'=>$model->badge_number])?>"> <span class="glyphicon glyphicon-pencil"></span> <?=$mystr?></a></li>
<?php }  ?>

    <li class="<?php if($urlStatus['actionId']=='view')echo'active';?>"> <a href="<?=Url::to(['/badges/view','badge_number'=>$model->badge_number])?>"><span class="glyphicon glyphicon-eye-open"></span> Details</a></li>
    <li class="<?php if($urlStatus['actionId']=='view-renewal-history')echo'active';?>"><a href="<?=Url::to(['/badges/view-renewal-history','badge_number'=>$model->badge_number])?>">  <span class="glyphicon glyphicon-book"></span> Renewal History</a></li>
    <li class="<?php if($urlStatus['actionId']=='view-certifications-list')echo'active';?>"><a href="<?=Url::to(['/badges/view-certifications-list','badge_number'=>$model->badge_number])?>"> <span class="glyphicon glyphicon-screenshot"></span> Certifications</a></li>
    <li class="<?php if($urlStatus['actionId']=='view-work-credits')echo'active';?>"><a href="<?=Url::to(['/badges/view-work-credits','badge_number'=>$model->badge_number])?>"><span class="glyphicon glyphicon-wrench"></span> Work Credits</a></li>
    <li class="<?php if($urlStatus['actionId']=='view-remarks-history')echo'active';?>"><a href="<?=Url::to(['/badges/view-remarks-history','badge_number'=>$model->badge_number])?>"> <span class="glyphicon glyphicon-comment"></span> Remarks History</a></li>
</ul>
