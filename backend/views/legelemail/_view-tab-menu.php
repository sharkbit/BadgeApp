<?php

use yii\helpers\Url;
use yii\helpers\Html;
$urlStatus = yii::$app->controller->getCurrentUrl();
?>

<ul class="nav nav-tabs">
    <li class="<?php if($urlStatus['actionId']=='index')echo'active';?>"> <a href="<?=Url::to(['/legelemail/index'])?>"><span class="glyphicon glyphicon-th-list"></span> Contacts</a></li>
    <li class="<?php if($urlStatus['actionId']=='groups')echo'active';?>"><a href="<?=Url::to(['/legelemail/groups'])?>"><span class="glyphicon glyphicon-plus"></span> Groups</a></li>

</ul>
