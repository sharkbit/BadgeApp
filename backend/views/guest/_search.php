<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $model backend\models\search\GuestSearch */
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

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
		'id'=>'viewPrintGuestFilter',
	]); ?>
	<div class="col-xs-4 col-sm-2 col-md-2 col-lg-2 col-xl-2" > <!-- <p> <br /></p> -->
		Export Data - 
		<?=ExportMenu::widget([
			'dataProvider' => $dataProvider,
			'filterModel' => $model,
			'columns' => $gridColumns,
			'fontAwesome' => true,
			'batchSize' => 0,
			'clearBuffers' => true,
			'filename'=>  $this->title,
			'target' => '_blank',
			'folder' => '@webroot/export', // this is default save folder on server
			'exportConfig' => [
				ExportMenu::FORMAT_HTML => false,
				ExportMenu::FORMAT_EXCEL => false,
				ExportMenu::FORMAT_EXCEL_X => false,
				//ExportMenu::FORMAT_PDF => false
			]
		])// . "<br /> <br />\n"
		;?>
	</div>
	<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2 col-xl-2">
		<?=  $form->field($model, 'SearchTime', [
		'options'=>['class'=>'drp-container form-group']
		])->widget(DateRangePicker::classname(), [
		'convertFormat'=>true,
		'pluginOptions' => [
			'opens'=>'left',
			'locale'=>['format'=>'Y-m-d','separator'=>' - ',],
		]])->label('Date range:'); ?>
	</div>
	
	<div class="col-xs-6 col-sm-2">
		<?= $form->field($model, 'pagesize')->dropDownlist([ 20 => 20, 50 => 50, 100 => 100, 200=>200],['value'=>$pagesize ,'id' => 'pagesize'])->label('Page size: ') ?>
	</div>

	<div class="col-xs-6 col-sm-2">

		<?= $form->field($model, 'atRange_condition')->dropDownlist(['all'=>'All','atRange'=>'At Range','gone'=>'Past Visitors'],['value'=>$model->atRange_condition !=null ? $model->atRange_condition : 'atRange'])->label('Fliter by') ?>
	</div>
	<div class="col-xs-12 col-sm-6 col-md-3 col-lg-2 col-xl-1">
		<div class=" form-group btn-group ">
		<br>
			<?= Html::submitButton('<i class="fa fa-search" aria-hidden="true"></i> Search', ['class' => 'btn btn-primary']) ?>
			<?= Html::a('<i class="fa fa-eraser" aria-hidden="true"></i> Reset', ['/guest/index'], ['class' => 'btn btn-danger']) ?>
		</div>
	</div>

	<?php ActiveForm::end(); ?>
</div>
</div>