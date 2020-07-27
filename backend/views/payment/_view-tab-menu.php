<?php

use yii\helpers\Url;
use yii\helpers\Html;
$urlStatus = yii::$app->controller->getCurrentUrl();

$token_found=false;
?>

<ul class="nav nav-tabs">
    <li class="<?php if($urlStatus['actionId']=='index')echo'active';?>"> <a href="<?=Url::to(['/payment/index'])?>"><span class="glyphicon glyphicon-eye-open"></span> Index</a></li>

<?php if($confParams->pp_id) { ?>
	<li class="<?php if($urlStatus['actionId']=='paypalsetup')echo'active';?>"> <a href="<?=Url::to(['/payment/paypalsetup'])?>"><span class="glyphicon glyphicon-ruble"></span> PayPal Process Test</a></li> <?php } ?>

<?php if(($confParams->conv_p_pin) || ($confParams->conv_d_pin)){ ?>
	<li class="<?php if($urlStatus['actionId']=='converge')echo'active';?>"> <a href="<?=Url::to(['/payment/converge'])?>"><span class="glyphicon glyphicon-tags"></span> Converge Test</a></li><?php } ?>
<?php if(yii::$app->controller->hasPermission('payment/inventory')) { ?>
<li class="<?php if($urlStatus['actionId']=='inventory')echo'active';?>"><a href="<?=Url::to(['/payment/inventory'])?>"> <span class="glyphicon glyphicon-save"></span> Pull Inventory</a></li><?php } ?>

</ul>
