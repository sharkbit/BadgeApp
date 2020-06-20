<?php 

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$urlStatus = yii::$app->controller->getCurrentUrl();

if (isset($_SESSION['jump'])) { $jump="?url=".$_SESSION['jump']; }
else { $jump=""; }
?>

<ul class="nav nav-tabs">
    <li class="<?php if($urlStatus['actionId']=='login-member')echo'active';?>"><a href="/site/login-member<?=$jump?>"> Badge Holder </a></li>
    <li class="<?php if($urlStatus['actionId']=='login')echo'active';?>"><a href="/site/login<?=$jump?>"> User Login </a></li>
</ul>