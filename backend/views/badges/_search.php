<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\search\BadgesSearch */
/* @var $form yii\widgets\ActiveForm */

if (isset($_REQUEST['pagesize'])) { 
	$pagesize = $_REQUEST['pagesize']; 
	$_SESSION['pagesize'] = $_REQUEST['pagesize'];
} elseif (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=20;
}
?>
<div class="badges-search">

<!-- <div class="pull-right"> -->
<div class="col-xs-0 col-sm-2"> <p> </p></div>
<div class="col-xs-3 col-sm-2" style="min-width:100px">
<?= $form->field($model, 'pagesize')->dropDownlist([ 20 => 20, 50 => 50, 100 => 100, 200=>200],['value'=>$pagesize ,'id' => 'pagesize'])->label('Page size: ') ?>
</div>

<div class="col-xs-3 col-sm-2" style="min-width:200px">
<?= $form->field($model, 'expire_condition')->dropDownlist(['active'=>'Active','active+2'=>'Active +2','expired<2'=>'Expired <2','expired>2'=>'Expired >2','inactive'=>'Inactive','all'=>'All'],['value'=>$model->expire_condition !=null ? $model->expire_condition : 'active+2'])->label('Expire Range') ?>

</div>

<div class="col-xs-3 col-sm-2 pull-right" style="min-width:200px">
	<div class="help-block"><p> </p></div>
	<div class=" form-group btn-group ">
	
	
	<div class="help-block"><p> </p></div>
		<?= Html::submitButton('<i class="fa fa-search" aria-hidden="true"></i> Search', ['class' => 'btn btn-primary']) ?>
		<?= Html::a('<i class="fa fa-eraser" aria-hidden="true"></i> Reset', ['/badges/index?reset=true'],['class' => 'btn btn-danger']) ?>
	</div>
</div>
<!-- </div> -->

</div>
