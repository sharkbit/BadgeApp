<?php 

use yii\helpers\Url;
use yii\helpers\Html;
$urlStatus = yii::$app->controller->getCurrentUrl();
?>

 <?php if($urlStatus['requestUrl']=='work-credits/credit-transfer?type=init') $this->title = 'Pending Requests';
else if($urlStatus['requestUrl']=='work-credits/credit-transfer?type=success') $this->title = 'Success Requests';

if(strpos($urlStatus['requestUrl'],'type=pen')) { $pen=true; } else { $pen=false; }
?>

<ul class="nav nav-tabs">
	<li class="<?php if(($urlStatus['actionId']=='index') && (!$pen)) echo'active';?>">
		<a href="<?=Url::to(['/work-credits/index'])?>">Work Credits</a></li>
	<li class="<?php if($urlStatus['actionId']=='create')echo'active';?>">
		<a href="<?=Url::to(['/work-credits/create'])?>">Log Work</a></li>
    <li class="<?php if($urlStatus['actionId']=='transfer-form')echo'active';?>">
		<a href="<?=Url::to(['/work-credits/transfer-form'])?>">Transfer Credits</a></li>

<?php if(yii::$app->controller->hasPermission('work-credits/approve')) { ?>
	<li class="<?php if(strpos($urlStatus['requestUrl'],'type=pen')) echo'active';?>">
		<a href="<?=Url::to(['/work-credits/index','type'=>'pen'])?>">Pending Requests</a></li>
<?php } ?>

<?php if(yii::$app->controller->hasPermission('work-credits/import')) { ?>
	<li class="<?php if($urlStatus['actionId']=='import')echo'active';?>">
		<a href="<?=Url::to(['work-credits/import'])?>">Import Credits</a></li>
<?php } ?>
</ul>
<div class="row">
	<div class="col-xs-12">
		<h2><?= Html::encode($this->title) ?></h2>
	</div>
</div>

