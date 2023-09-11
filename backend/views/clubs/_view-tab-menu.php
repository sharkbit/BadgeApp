<?php

use yii\helpers\Url;
use yii\helpers\Html;
$urlStatus = yii::$app->controller->getCurrentUrl();

?>

<ul class="nav nav-tabs">

    <li class="<?php if(($urlStatus['actionId']=='index')||($urlStatus['actionId']=='create')||($urlStatus['actionId']=='view')||($urlStatus['actionId']=='update'))echo'active';?>">
		<a href="<?=Url::to(['/clubs/index'])?>"><span class="glyphicon glyphicon-eye-open"></span> Club List</a></li>
    <?php if(yii::$app->controller->hasPermission('clubs/officers')) { ?>
	<li class="<?php if(($urlStatus['actionId']=='officers')||($urlStatus['actionId']=='officers-create')||($urlStatus['actionId']=='officers-update'))echo'active';?>">
		<a href="<?=Url::to(['/clubs/officers'])?>"> <span class="glyphicon glyphicon-screenshot"></span> Officer List</a></li>
    <?php }
	if(yii::$app->controller->hasPermission('clubs/badge-rosters')) { ?>
	<li class="<?php if($urlStatus['actionId']=='badge-rosters')echo'active';?>">
		<a href="<?=Url::to(['/clubs/badge-rosters'])?>">  <span class="glyphicon glyphicon-book"></span> Club Roster Report</a></li>
	<?php }
	if(yii::$app->controller->hasPermission('clubs/roles')) { ?>
	<li class="<?php if($urlStatus['actionId']=='roles')echo'active';?>">
		<a href="<?=Url::to(['/clubs/roles'])?>">  <span class="glyphicon glyphicon-book"></span> Club Roles</a></li>
	<?php } ?>
</ul>
