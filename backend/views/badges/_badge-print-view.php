<?php
/* @var $this yii\web\View */
/* @var $model backend\models\Badges */

use backend\models\User;
?> 
<div class="container" >

<?php if($page==1) { ?>
<link href='http://fonts.googleapis.com/css?family=Cookie' rel='stylesheet' type='text/css'>
<style>
.barcode {
	width: 250px;
}
.container {
	width:100%;
}
.front {
	float: left;
	//width: 250px;
	//height: 350px;
	//border-style: solid;
	//border-width: 1px;
}
.back {
	float: left;
	clear:both;
	//width: 350px;
//height: 250px;
	background-color: white;
	transform: rotate(-90deg);
//margin-top: 50px;
	//margin-left: -40px;
//	border-style: solid;
//	border-width: 1px;
	padding: 7px;
}
.face {
	width: 80px;
	height:100px;
}
body {
	margin-right: -10px;
	margin-left: -10px;
}
p {
	text-align: center;
}
tr { margin: 0px; padding: 0px; }
td { margin: 0px; padding: 0px; }
.info { font-size: 12px;  }
.large { font-size: 11px; font-weight: bold; }
.name { font-size: 15px; font-weight: bold; margin: 5px;}
.small { font-size: 11px; }
.scriptBig {font-size: 24; font-weight: bold; font: 'Cookie', Helvetica, sans-serif; margin: 4px; }
.scriptSmall {font-size: 20; font: 'Cookie', Helvetica, sans-serif; margin: 4px; }
</style>


<div class="front" >
	<div style="padding: 10px 10px" > </div>
<?php if(file_exists("files/badge_photos/".str_pad($model->badge_number, 5, '0', STR_PAD_LEFT).".jpg")) { ?>
	&nbsp;
	<img class="face" src="<?=Yii::$app->params['rootUrl']."/files/badge_photos/".str_pad($model->badge_number, 5, '0', STR_PAD_LEFT)?>.jpg" >
	&nbsp;
<?php } else { echo " &nbsp; &nbsp; &nbsp; No Photo &nbsp; &nbsp; "; } ?>
	<img src="<?php echo Yii::$app->params['rootUrl']; ?>/images/AGC_Logo.jpg" style="width: 95px" >
<?php	$Badge_bar ='<div class="row" style="background-color: black; color: white; font-weight: bold; height:30px;">'.
		'<div class="col-xs-2" style="font-size: 24px; text-align:center; ">';

	if($chk=='rso') { //RSO Badge 
	echo $Badge_bar.$model->last_name."</div>\n</div>\n"; ?>
	<div class="row" style="background-color: white; color: black; font-weight: bold; height:169px;
		background-image: url('<?php echo Yii::$app->params['rootUrl'];?>/images/rso.jpg'); background-size: 575px 490px; ">
	</div>
<?php } elseif($chk=='rso_c') { //CRSO Badge 
	echo $Badge_bar.$model->last_name."</div>\n</div>\n"; ?>
	<div class="row" style="background-color: white; color: black; font-weight: bold; height:169px;
		background-image: url('<?php echo Yii::$app->params['rootUrl'];?>/images/crso.jpg'); background-size: 575px 490px; ">
	</div>
<?php } elseif($chk=='rso_a') { //RSO A.S. Badge 
	echo $Badge_bar.$model->last_name."</div>\n</div>\n"; ?>
	<div class="row" style="background-color: white; color: black; font-weight: bold; height:169px;
		background-image: url('<?php echo Yii::$app->params['rootUrl'];?>/images/action.jpg'); background-size: 575px 490px; ">
	</div>
<?php } elseif($chk=='cio') { //CIO Badge ?>
	<div class="row" style="font-weight: bold; height:20px;"> </div>
	<div class="row" style="font-weight: bold; height:30px;">
		<div class="col-xs-2" style="font-size: 20px; text-align:center; ">CIO&nbsp; INSTRUCTOR</div>
	</div>
<?php $cio = User::find()->where(['badge_number'=>$model->badge_number])->one(); 
	$cio_len=strlen($cio->company);	
	if($cio_len > 32){
		echo '<div class="row" style="font-weight: bold; height:20px;"> </div>';
		$l_height='40px';$f_size='20px';
	} elseif($cio_len > 20){
		echo '<div class="row" style="font-weight: bold; height:10px;"> </div>';
		$l_height='50px';$f_size='28px';
	} else {
		$l_height='150px';$f_size='28px';
	} ?>
	<div class="row" style="text-align:center; vertical-align:middle; line-height:<?=$l_height?>; font-size: <?=$f_size?>" >
		
<?php echo $cio->company; ?>
	</div> 
<?php } elseif($chk=='m') { //Member Badge (Special casess  Same as Else)
	echo $Badge_bar.$model->badge_number."</div>\n</div>\n"; ?>
	<div class="row" style="background-color: red; color: white; font-weight: bold; height:168px;">
		<img src="<?php echo Yii::$app->params['rootUrl']; ?>/images/member.jpg" style="width: 100%; height:168px;" >
	</div>
<?php } elseif($model->mem_type==70) { //15 Year Badge
		echo $Badge_bar.$model->badge_number."</div>\n</div>\n"; ?>
	<div class="row" style="background-color: black; color: black; font-weight: bold; height:169px;
		background-image: url('<?php echo Yii::$app->params['rootUrl']; ?>/images/15yr.jpg'); ">
		<p class="scriptBig">15 Year<br />Member Badge<br /></p>
		<p class="scriptSmall">Expires<br />January<br /><?=substr($model->expires,0,4)?></p>
	</div>
<?php } elseif($model->mem_type==99) { //Life Badge                                   ?>
	<div class="row" style="background-color: red; color: white; font-size: 28px; font-weight: bold; height:200px; 
		background-image: url('<?php echo Yii::$app->params['rootUrl'];?>/images/life.jpg'); background-size: 575px 575px; ">
		<p style="font-size:22"> </p><br> 
		<div style="width 100%; text-align:center"><?=$model->badge_number?></div>
	</div>
<?php } else { //Member Badge (Everyone Else)
	echo $Badge_bar.$model->badge_number."</div>\n</div>\n"; ?>
	<div class="row" style="background-color: red; color: white; font-weight: bold; height:168px;">
		<img src="<?php echo Yii::$app->params['rootUrl']; ?>/images/member.jpg" style="width: 100%; height:168px;" >
	</div>
<?php } ?>
</div>

<?php } elseif($page==2) { ?>
<script src="<?=yii::$app->params['rootUrl']?>/js/JsBarcode.code128.min.js"></script>

<div class="back">
<div class="container" style="height:700px; ">
	<table style="height:700px;">
	<tr><td text-rotate='90'>
	<p class="info"><?=date('m/d/Y',strtotime(yii::$app->controller->getNowTime())); ?></p>
	</td><td text-rotate='90'>
	<p class="name"><?php if($model->mem_type==99) { echo "Life Member: ";} echo $model->first_name." ".$model->last_name; ?></p>
	</td><td text-rotate='90'>
	<p class="small">Property Of Associated Gun Clubs Of Baltimore, Inc.<br /></p>
	</td><td text-rotate='90'>
	11518 Marriottsville Road,
	</td><td text-rotate='90'>
	Marriottsville, MD 21104
	</td><td text-rotate='90'>
	<p class="small">410-461-8532</p>
	</td><td text-rotate='90'>
	<p class="large">OBEY ALL RANGE RULES, PRACTICE SAFETY<br /></p>
	</td><td text-rotate='90'>
	Badge-Holder agrees to <br />
	</td><td text-rotate='90'>
	AGC Waiver of Liability<br />
	</td><td text-rotate='90'>
	<p class="large">Safety is Everyone's Responsibility</p>
	</td></tr>
	</table>
	<p style="margin: 0px; padding: 0px;"><?=$model->qrcode?></p>
	<barcode code="<?=$model->qrcode?>" type="C128A" class="barcode" size="0.8" style="margin: 0px; padding: 0px;" />
</div>
</div>
<?php } elseif($page==3) { ?>
<script src="<?=yii::$app->params['rootUrl']?>/js/JsBarcode.code128.min.js"></script>
<style>
p { margin: 2px; }
.small { font-size: 8px; }
.name { font-size: 14px; font-weight: bold; margin: 4px;}
</style>
<div class="back" >
	<p class="info"><?=date('m/d/Y',strtotime(yii::$app->controller->getNowTime())); ?></p>
	<p class="name"><?php if($model->mem_type==99) { echo "Life Member: ";} echo $model->first_name." ".$model->last_name; ?></p>
	<p class="small">Property Of Associated Gun Clubs Of Baltimore, Inc.<br />
	11518 Marriottsville Road, Marriottsville, MD 21104<br />
	410-461-8532</p>
	<p class="large">OBEY ALL RANGE RULES, PRACTICE SAFETY,<br />
	Badge-Holder agrees to AGC Waiver of Liability, <br />
	Safety is Everyone's Responsibility</p>
	<p><barcode code="<?=$model->qrcode?>" type="C128A" class="barcode" size="0.8" /></p>
	<p class="info"><?=$model->qrcode?></p>
</div>
<?php } ?>

