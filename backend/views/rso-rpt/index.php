<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\ActiveForm;
use backend\models\RsoReports;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\RsoReportsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'RSO Reports';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['rso-rpt/index']];

if (isset($_REQUEST['RsoReportsSearch']['pagesize'])) { 
	$pagesize = $_REQUEST['RsoReportsSearch']['pagesize']; 
	$_SESSION['pagesize'] = $_REQUEST['RsoReportsSearch']['pagesize'];
} elseif (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=20;
}
$dataProvider->pagination = ['pageSize' => $pagesize];
?>

<?=$this->render('_view-tab-menu').PHP_EOL ?>

<div class="row">
	<div class="col-sm-8">
		<h1><?= Html::encode($this->title) ?></h1>
	</div>
    <div class="rsoreports-search">
		<?php $form = ActiveForm::begin([
			'action' => ['index'],
			'method' => 'get',
			'id'=>'RSOreportsFilter',
		]); ?>
		<div class="col-xs-3 col-sm-2" style="min-width:100px">
			<?= $form->field($searchModel, 'pagesize')->dropDownlist([ 20 => 20, 50 => 50, 100 => 100, 200=>200],['value'=>$pagesize ,'id' => 'pagesize'])->label('Page size: ') ?>
		</div>
		<div class="col-sm-2">
			<div class=" form-group btn-group ">
			<br>
				<?= Html::submitButton('<i class="fa fa-search" aria-hidden="true"></i> Search', ['class' => 'btn btn-primary']) ?>
				<?= Html::a('<i class="fa fa-eraser" aria-hidden="true"></i> Reset', ['/rso-rpt/index'],['class' => 'btn btn-danger']) ?>
			</div>
		</div>

		<?php ActiveForm::end(); ?>

	</div>
</div>

<div class="row">
<div class="col-xs-12">

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[	'attribute'=>'id',
				'visible' => (yii::$app->controller->hasPermission('params/update')) ? true : false,
				'headerOptions' => ['style' => 'width:5%'],
			],
			[	'attribute'=>'date_open',
				'headerOptions' => ['style' => 'width:10%'],
			],
			[	'attribute' => 'rso',
				'value' => function($model) {
					$rsos=json_decode($model->rso);
					$names='';
					if($rsos) {
						foreach ($rsos as $badge) {
							$names .= yii::$app->controller->decodeBadgeName((int)$badge).', ';
						}
						return $names;
					} else { return ""; } 
				},
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'rso',(new RsoReports)->listRSOs(),['class'=>'form-control','prompt' => 'All']),
			],
			[	'attribute'=>'wb_color',
				'value' => function($model) {
					switch ($model->wb_color){
					case 'g': return 'Green';
					case 'b': return 'Blue';
					case 'r': return 'Red';
					case 'l': return 'Lavender';
					case 'k': return 'Black';
					}
				}
			],
			[	'attribute'=>'shift',
				'value'=>function($model) {
					if($model->shift=='m') {return 'Morning';}
					else { return 'Evening';}
				},
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'shift',['m'=>'Morning','e'=>'Evening'],['class'=>'form-control','prompt' => 'All']),
				'headerOptions' => ['style' => 'width:10%']
			],
			[	'attribute'=>'closed',
				'value'=>function($model) {
					if($model->closed=='0') {return 'Open';}
					else { return 'Closed';}
				},
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'closed',[0=>'Open',1=>'Closed'],['class'=>'form-control','prompt' => 'All']),
				'headerOptions' => ['style' => 'width:10%'] 
			],
			[
				'header' => 'Actions',
				'class' => 'yii\grid\ActionColumn',
				'headerOptions' => ['style' => 'width:5%'],
				'template'=>'{view}{update}{delete}',
				'buttons'=>[
					'update' => function ($url, $model) {
						if (yii::$app->controller->hasPermission('rso-rpt/update')) {
						return  Html::a(' <span class="glyphicon glyphicon-pencil"></span> ', ['/rso-rpt/update','id'=>$model->id],
						[	'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'Edit',
							'class'=>'edit_item',
						]); }
					},
					'delete' => function($url,$model) {
						if (yii::$app->controller->hasPermission('rso-rpt/delete')) {
						return  Html::a(' <span class="glyphicon glyphicon-trash"></span> ', $url, [
							'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'Delete',
							'data' => [
								'confirm' => 'Are you sure you want to delete this item?',
								'method' => 'post',
							],
						]); }
					},
				]
			]
		]
	]); ?>
</div>
</div>
</div>
