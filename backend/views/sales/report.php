<?php

use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Sales Report';
$this->params['breadcrumbs'][] = ['label' => 'store', 'url' => ['/sales']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/payment/inventory']];

$dataProvider = $SalesReport->getSRdata($SalesReport->created_at,false);

if (isset($_REQUEST['SalesReport']['pagesize'])) { 
	$pagesize = $_REQUEST['SalesReport']['pagesize']; 
	$_SESSION['pagesize'] = $_REQUEST['SalesReport']['pagesize'];
} elseif (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=200;
}
$dataProvider->pagination = ['pageSize' => $pagesize];

echo $this->render('_view-tab-menu').PHP_EOL;
?>

<h2><?= Html::encode($this->title) ?></h2>
<div class="clubs-index" ng-controller="SalesReportForm">
	<div class="row">
		 <div class="col-xs-5">
	  <!--	  <?=html::a('<i class="fa fa-download" aria-hidden="true"></i> Export as CSV',['#'],['id'=>'customExportCsv','class'=>'btn btn-primary'])?> -->
		</div>

		<div class="col-xs-5">
			 <?php $form = ActiveForm::begin([
				'id'=>'SalesReportForm',
				'method' => 'get',
			]); ?>

<?=$form->field($SalesReport, 'created_at', [
		'options'=>['class'=>'drp-container form-group']
		])->widget(DateRangePicker::classname(), [
			'presetDropdown'=>true,
			'hideInput'=>false,
			'pluginOptions' => [
				'opens'=>'left',
				'locale'=>['format'=>'MM/DD/YYYY'],
			]])->label(false); ?>
		</div>
		<div class="col-xs-2">
			<?= Html::submitButton('<i class="fa fa-search pull-right" aria-hidden="true"></i> Search', ['class' => 'btn btn-primary']) ?>
			<?php ActiveForm::end(); ?>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
		<?= $SalesReport->getSRdata($SalesReport->created_at) ?>
		</div>
	</div>	
	<hr>
	<div class="row">
		<div class="col-xs-12">
			<?php
				$gridColumns = [
					'club_name',
					'short_name',
					[	'attribute'=>'new',
						'value' => function($dataProvider) {
							if($dataProvider['new']==0) {return '';} else {return $dataProvider['new'];}
						}
					],
					[	'header'=>'Renew',
						'value' => function($dataProvider) {
							if($dataProvider['renew']==0) {return '';} else {return $dataProvider['renew'];}
						}
					],
					[	'attribute'=>'certs',
						'value' => function($dataProvider) {
							if($dataProvider['certs']==0) {return '';} else {return $dataProvider['certs'];}
						}
					],
					[	'attribute'=>'guests',
						'value' => function($dataProvider) {
							if($dataProvider['guests']==0) {return '';} else {return $dataProvider['guests'];}
						}
					],
					[	'attribute'=>'students',
						'value' => function($dataProvider) {
							yii::$app->controller->createLog(true, 'trexstudents', var_export($dataProvider['students'],true));
							if(isset($dataProvider['students']) && (int)$dataProvider['students'] >0 ) {return $dataProvider['students'];} else {return '';}
						}
					],
				 ];

	/*		   echo ExportMenu::widget([
					'dataProvider' => $dataProvider,
					'columns' => $gridColumns,
					'fontAwesome' => true,
					'batchSize' => 10,
					'filename'=>  $this->title,
					'target' => '_blank',
					'folder' => '@webroot/export', // this is default save folder on server
				]) . "<hr>\n"; 
	*/
			   echo GridView::widget([
					'dataProvider' => $dataProvider,
					'columns' => $gridColumns,
				]);

			?>
		</div>

	</div>
</div>