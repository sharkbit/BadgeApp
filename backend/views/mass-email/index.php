<?php

use backend\models\User;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\WorkCreditsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mass Emails';
$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['mass-email/index']];

if (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=20;
}
$dataProvider->pagination = ['pageSize' => $pagesize];
?>

<div class="MassEmail-index">
<div class="row">
<div class="col-xs-12">
<?php if(yii::$app->controller->hasPermission('mass-email/create')) { ?>
	<div class="btn-group pull-right">
	<?= Html::a('<i class="fa fa-plus-square" aria-hidden="true"></i> Create Mass Email', ['create'], ['class' => 'btn btn-success ']) ?>

	</div>
<?php } ?>
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'attribute' =>'mass_subject',
				'format' => 'raw',
				'value'=>function($model) {
					return Html::a($model->mass_subject,'/mass-email/update?id='.$model->id);
				},
			],
			[
				'attribute'=>'mass_to',
				'value'=>function($model) {
					$str='';
					if($model->mass_to_users!='') { $str .= "Users: [".(new User)->getPrivilege_Names($model->mass_to_users)."], "; }
					if (strpos($model->mass_to,"@")) { $str .= $model->mass_to; }
					else { 
						if (strpos(" ".$model->mass_to,"*A")) { $str .= "Active Members, ";}
						if (strpos(" ".$model->mass_to,"*E")) { $str .= "Expired Members";}
					}
					return rtrim($str,", ");
				},
			],
			[
				'attribute'=>'mass_created',
				'value'=>function($model) {
					return yii::$app->controller->pretydtg($model->mass_created);
				},
			],
			[
				'attribute'=>'mass_created_by',
				//'format' => 'raw',
				'value'=>function ($model) {
					return yii::$app->controller->decodeBadgeName((int)$model->mass_created_by).' ('.$model->mass_created_by.')';
				}
			],
			[
				'attribute'=>'mass_running',
				'value'=>function($model) {
					if ($model->mass_running==0) {     return 'Draft'; }
					elseif ($model->mass_running==1) { return 'Running'; }
					elseif ($model->mass_running==2) { return 'Sent'; }
					else { return "new status=$model->mass_running (index:76)"; }
				},
			],
			'mass_lastbadge',
			[
				'header' => 'Actions',
				'class' => 'yii\grid\ActionColumn',

				'template'=>'{update}{delete}',
				'buttons'=>[
					'update' => function ($url, $model) {
						if (yii::$app->controller->hasPermission('mass-email/update') ||
							($model->badge_number == $_SESSION['badge_number'] && (!$model->time_out))) {
						return  Html::a(' <span class="glyphicon glyphicon-pencil"></span> ', ['/mass-email/update','id'=>$model->id],
						[	'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'Edit',
							'class'=>'edit_item',
						]); }
					},
					'delete' => function($url,$model) {
						if (yii::$app->controller->hasPermission('mass-email/update')) {
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
