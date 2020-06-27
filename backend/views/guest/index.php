<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\Guest;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\WorkCreditsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Visitor Log';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['guest/index']];

$guestModel = new Guest();

if (isset($_REQUEST['GuestSearch']['pagesize'])) {
	$pagesize = $_REQUEST['GuestSearch']['pagesize'];
	$_SESSION['pagesize'] = $_REQUEST['GuestSearch']['pagesize'];
} elseif (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=20;
}
$dataProvider->pagination = ['pageSize' => $pagesize];

if(yii::$app->controller->hasPermission('guest/delete')) {
	$myTemplate=' {view}  {update}  {delete} ';
} elseif(yii::$app->controller->hasPermission('guest/update')) {
	$myTemplate=' {view}  {update} ';
} else {$myTemplate='{view}';}
?>

<?=$this->render('_view-tab-menu').PHP_EOL ?>
<?=$this->render('_search',['model'=>$searchModel,'guestModel'=>$guestModel]).PHP_EOL ?>

<div class="row">
<div class="col-xs-12">
<?php if(yii::$app->controller->hasPermission('guest/create')) { ?>
	<div class="btn-group pull-right">
	<?= Html::a('<i class="fa fa-plus-square" aria-hidden="true"></i> Add Guest', ['create'], ['class' => 'btn btn-success ']) ?>

	</div>
<?php } ?>
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'attribute'=>'badge_number',
				//'format' => 'raw',
				'value'=>function ($model) {
					return str_pad($model->badge_number, 5, '0', STR_PAD_LEFT);
				}
			],
			'g_first_name',
			'g_last_name',
			'g_city',
			'g_state',
			'g_yob',
			[
				'attribute'=>'g_paid',
				'format' => 'raw',
				'value'=>function($model) {
					if ($model->g_paid <> 1) {
						$p_type='';
						switch ($model->g_paid) { // Updated from SalesController Purchases Line:75
							case 'a': $p_type=' (Cash)'; break;
							case 'h': $p_type=' (Check)'; break;
							case 'm': return "Minor"; break;
							case 'o': return "Observer"; break;
							case 's': return "Spouse"; break;
							case 'y': return "Junior Event"; break;
						}
						return Html::a('Pay Now'.$p_type,'/sales/index?badge='.$model->badge_number.'&id='.$model->id);
					} else {
						return 'paid';
					}
				},
			],
			//'tmp_badge',
			[
				'attribute'=>'time_in',
				'value'=>function($model) {
					return yii::$app->controller->pretydtg($model->time_in);
				},
			],
			[
				'attribute'=>'time_out',
				'format' => 'raw',
				'value'=>function($model) {
					if (!$model->time_out) {
						//return 'Check Out';
						return Html::a('Check Out','/guest/out?id='.$model->id);
					} else {
						return yii::$app->controller->pretydtg($model->time_out);
					}
				},
			],
			[
				'header' => 'Actions',
				'class' => 'yii\grid\ActionColumn',

				'template'=>'{update}{delete}',
				'buttons'=>[
					'update' => function ($url, $model) {
						if (yii::$app->controller->hasPermission('guest/modify') ||
							($model->badge_number == $_SESSION['badge_number'] && (!$model->time_out))) {
						return  Html::a(' <span class="glyphicon glyphicon-pencil"></span> ', ['/guest/update','id'=>$model->id],
						[	'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'Edit',
							'class'=>'edit_item',
						]); }
					},
					'delete' => function($url,$model) {
						if (yii::$app->controller->hasPermission('guest/delete')) {
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
