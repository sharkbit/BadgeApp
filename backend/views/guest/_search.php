<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\search\BadgesSearch */
/* @var $form yii\widgets\ActiveForm */

if (isset($_REQUEST['BadgesDatabaseSearch']['pagesize'])) { 
	$pagesize = $_REQUEST['BadgesDatabaseSearch']['pagesize']; 
	$_SESSION['pagesize'] = $_REQUEST['BadgesDatabaseSearch']['pagesize'];
} elseif (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=20;
}
?>
<div class="guest-search">
<div class="row">
	<div class="col-xs-6 col-sm-4">
		<h1><?= Html::encode($this->title) ?></h1>
	</div>

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
		'id'=>'viewPrintGuestFilter',
	]); ?>
	<div class="col-xs-0 col-sm-2"> <p> </p></div>
	
	<div class="col-xs-2 col-sm-2">
		<?= $form->field($model, 'pagesize')->dropDownlist([ 20 => 20, 50 => 50, 100 => 100, 200=>200],['value'=>$pagesize ,'id' => 'pagesize'])->label('Page size: ') ?>
	</div>

	<div class="col-sm-2">

		<?= $form->field($model, 'atRange_condition')->dropDownlist(['all'=>'All','atRange'=>'At Range','gone'=>'Past Visitors'],['value'=>$model->atRange_condition !=null ? $model->atRange_condition : 'atRange'])->label('Fliter by') ?>
	</div>
	<div class="col-sm-2">
		<div class=" form-group btn-group ">
		<br>
			<?= Html::submitButton('<i class="fa fa-search" aria-hidden="true"></i> Search', ['class' => 'btn btn-primary']) ?>
			<?php //= Html::a('<i class="fa fa-eraser" aria-hidden="true"></i> Reset', ['/guest/index'],['class' => 'btn btn-danger']) ?>
		</div>
	</div>

	<?php ActiveForm::end(); ?>
</div>
</div>