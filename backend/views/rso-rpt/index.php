<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\WorkCreditsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'RSO Reports';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['rso-rpt/index']];

?>

<?=$this->render('_view-tab-menu').PHP_EOL ?>

<div class="row">
<div class="col-xs-12">

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			'date',
			[
				'attribute'=>'rso',
				//'format' => 'raw',
				'value'=>function ($model) {
					return str_pad($model->rso, 5, '0', STR_PAD_LEFT);
				}
			],
			'shift',
			[
				'header' => 'Actions',
				'class' => 'yii\grid\ActionColumn',

				'template'=>'{view}{update}{delete}',
				'buttons'=>[
					'update' => function ($url, $model) {
						//if (yii::$app->controller->hasPermission('rso-rpt/update') ||
						//	($model->badge_number == $_SESSION['badge_number'] && (!$model->time_out))) {
						return  Html::a(' <span class="glyphicon glyphicon-pencil"></span> ', ['/rso-rpt/update','id'=>$model->id],
						[	'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'Edit',
							'class'=>'edit_item',
						]); 
						//}
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
