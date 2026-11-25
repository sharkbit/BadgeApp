<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\Privileges */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Privileges';
$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/privileges/index']];

if (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=20;
}
$dataProvider->pagination = ['pageSize' => $pagesize];
$controller = Yii::$app->controller;

echo $this->render('_view-tab-menu').PHP_EOL;
?>
<div class="privileges-index">
    <div class="row">
	   <div class="col-xs-12">
		  <h2><?= Html::encode($this->title) ?></h2>

		  <div class="btn btn-group pull-right">
			 <?php if ($controller->hasPermission('privileges/create')): ?>
				 <?= Html::a('Add Privilege', ['create'], ['class' => 'btn btn-success']) ?>
			 <?php endif; ?>
		  </div >

		  <?php Pjax::begin(); ?>
		  <?= GridView::widget([
			 'dataProvider' => $dataProvider,
			 'filterModel' => $searchModel,
			 'columns' => [
				[	'attribute'=>'id',
				    'headerOptions' => ['style' => 'width:15%'],
				],
				'privilege',
				'priv_sort',
				'timeout',
				[	'attribute'=>'restricted',
					'value' => function($model, $attribute) { if($model->restricted==0) {return 'No';} else { return 'Yes';} },
				],
				[	'header'=>'Action',
				    'class' => 'yii\grid\ActionColumn',
					'template'=>' {view} {update} {delete} ',
					'visibleButtons' => [
						'view' => static function ($model, $key, $index) use ($controller) {
							return $controller->hasPermission('privileges/view');
						},
						'update' => static function ($model, $key, $index) use ($controller) {
							return $controller->hasPermission('privileges/update');
						},
						'delete' => static function ($model, $key, $index) use ($controller) {
							return $controller->hasPermission('privileges/delete');
						},
					],
				],
			 ],
		  ]); ?>
		  <?php Pjax::end(); ?>
	   </div>
    </div>
</div>
