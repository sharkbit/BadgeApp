<?php

use yii\helpers\Url;
use yii\helpers\Html;
$urlStatus = yii::$app->controller->getCurrentUrl();

?>


<ul class="nav nav-tabs">

    <li class="<?php if($urlStatus['actionId']=='index') echo'active'; ?>">
		<a href="<?=Url::to(['/privileges/index'])?>">  <span class="glyphicon glyphicon-th"></span> Privileges</a></li>
<?php if(yii::$app->controller->hasPermission('privileges/raw')) { ?>
	<li class="<?=($urlStatus['actionId'] ?? '') === 'raw' ? 'active' : '' ?>">
		<a href="<?=Url::to(['/privileges/raw'])?>">  <span class="glyphicon glyphicon-th"></span> Full Privilege List</a></li> 
<?php } ?>
</ul>
