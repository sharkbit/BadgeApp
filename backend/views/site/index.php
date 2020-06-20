<?php
use backend\components\Menu;

/* @var $this yii\web\View */

$this->title = 'Main Menu';
$activeUser = yii::$app->controller->getActiveuser();
?>

<div class="row">
    <div class="col">
        <h1 class="text-center">AGC Range Badge Tracking System</h1>
    </div>
</div>	
<div class="row">
	<div id="sum_box" >
	<div class="col-sm-3 col-md-3"></div>

		<div class="col-sm-3 col-md-3">
			<div class="panel info">
				<div class="panel-body">
					<p class="icon">
					   <i class="icon fa fa-users"></i>
					</p>
					<h4 class="value">
						<span><?=$badgeCount?></span><span></span></h4>
					<p class="description">
						Current Range Badges</p>
			   
				</div>
			</div>
		</div>
		<div class="clearfix visible-xs"></div>
		<div class="col-sm-3 col-md-3">
			<div class="panel info">
				<div class="panel-body">
					<p class="icon">
					   <i class="icon fa fa-users"></i>
					</p>
					<h4 class="value">
						<span><?=$guestCount?></span><span></span></h4>
					<p class="description">
						Current Visitors</p>
			   
				</div>
			</div>
		</div>
		<div class="clearfix visible-xs"></div>
		
	<div class="col-sm-3 col-md-3"></div>
	</div>

</div>	
<div class="row">
	 <div class="col">
        <div class="menu-box-parent">
            <?php if(!empty($activeUser)) {
                echo Menu::widget(['type'=>'home','privilege'=>$activeUser->privilege]);
            } ?>
        </div>
    </div>
</div>

<style type="text/css">
h4 {
    text-align: left;
    margin-top: 0;
    font-size: 30px;
    margin-bottom: 0;
    padding-bottom: 0;
}

.icon {
    color: #fff;
    font-size: 55px;
    margin-top: 7px;
    margin-bottom: 0;
    float: right;
}

.info{
        background-color: #5bc0de;
}
.btn-block {
	padding:20px;
}
@media(max-width:767px) {
	.icon {
		font-size:30px;
	}

	.panel-body {
		padding:0;
	}
}
</style>