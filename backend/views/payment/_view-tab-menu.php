<?php

use yii\helpers\Url;
use yii\helpers\Html;
$urlStatus = yii::$app->controller->getCurrentUrl();

$token_found=false;
?>

<ul class="nav nav-tabs">

<li class="<?php if($urlStatus['actionId']=='paypalsetup')echo'active';?>"> <a href="<?=Url::to(['/payment/paypalsetup'])?>"><span class="glyphicon glyphicon-eye-open"></span> PayPal Process Test</a></li>

    <li class="<?php if($urlStatus['actionId']=='index')echo'active';?>"> <a href="<?=Url::to(['/payment/index'])?>"><span class="glyphicon glyphicon-eye-open"></span> oAuth 2</a></li>

<?php if($confParams->qb_token) { $token_found=true; ?>
    <li class="<?php if($urlStatus['actionId']=='Disconnect')echo'active';?>"><a href="<?=Url::to(['/payment/disconnect'])?>"><span class="glyphicon glyphicon-wrench"></span> Disconnect</a></li>
    <li class="<?php if($urlStatus['actionId']=='Reconnect')echo'active';?>"><a href="<?=Url::to(['/payment/reconnect'])?>"> <span class="glyphicon glyphicon-comment"></span> Reconnect</a></li>
<?php }

 if ($confParams->qb_oa2_refresh_token) { $token_found=true; ?>
	<li class="<?php if($urlStatus['actionId']=='process')echo'active';?>"><a href="<?=Url::to(['/payment/process'])?>"> <span class="glyphicon glyphicon-screenshot"></span> Intuit CC Test</a></li>
 <?php } 
 if ($token_found) { ?>
	<li class="<?php if($urlStatus['actionId']=='Company Info')echo'active';?>"><a href="<?=Url::to(['/payment/page'])?>">  <span class="glyphicon glyphicon-book"></span> Company Info</a></li>
	<li class="<?php if($urlStatus['actionId']=='inventory')echo'active';?>"><a href="<?=Url::to(['/payment/inventory'])?>"> <span class="glyphicon glyphicon-save"></span> (QB) Pull Inventory</a></li>
	<li class="<?php if($urlStatus['actionId']=='invoice')echo'active';?>"><a href="<?=Url::to(['/payment/invoice'])?>"> <span class="glyphicon glyphicon-comment"></span> Invoice Page</a></li>
 <?php } ?>
</ul>
