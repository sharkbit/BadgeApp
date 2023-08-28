<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\LegalgroupsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Legal Groups';
$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/legelemail/groups']];

if (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=50;
}
$dataProvider->pagination = ['pageSize' => $pagesize];
?>

<?=$this->render('_view-tab-menu').PHP_EOL ?>
<h1><?= Html::encode($this->title) ?></h1>
<div class="legelgroups-index">
    <div class="row">
        <div class="col-xs-12">
           
            <div class="btn btn-group pull-right"> 
                <?= Html::a('Add Group', ['create'], ['class' => 'btn btn-success']) ?> 
            </div >
            
		<?php Pjax::begin(); ?>    
		<?= GridView::widget([
			'dataProvider' => $dataProvider,
			'filterModel' => $searchModel,
			'columns' => [
   
				[	'attribute'=>'group_id',
					'headerOptions' => ['style' => 'width:15%'],
				],
				'name',
				'display_order',
				[	'attribute'=>'is_active',
					'value' => function($model, $attribute) { if($model->is_active==0) {return 'No';} else { return 'Yes';} },
					'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'is_active',['1'=>'Yes','0'=>'No'],['class'=>'form-control','prompt' => 'All', 'style' => 'padding-left: 5%; text-align: left;']),
				],
				'date_created',
				'date_modified',
				[ 	'header' => 'Actions',
					'headerOptions' => ['style' => 'width:5%;'],
					'class' => 'yii\grid\ActionColumn',
					'template'=>'  ' , // ' {update}  {delete} '
				/*	'buttons'=>[
						'view' => function ($url, $model) {
							return  Html::a(' <span class="glyphicon glyphicon-eye-open"></span>', ['view','id'=>$model->id],
							[	'data-toggle'=>'tooltip',
								'data-placement'=>'top',
								'title'=>'View',
								'class'=>'edit_item',
							]);
						},
						'update' => function ($url, $model) {
							return  Html::a(' <span class="glyphicon glyphicon-pencil"></span>', ['update','id'=>$model->id],
							[	'data-toggle'=>'tooltip',
								'data-placement'=>'top',
								'title'=>'Edit',
								'class'=>'edit_item',
							]);
						},
						'delete' => function($url,$model) {
							return  Html::a(' <span class="glyphicon glyphicon-trash"></span>', $url,
							[	'data-toggle'=>'tooltip',
								'data-placement'=>'top',
								'title'=>'Delete',
								'data' => [
									'confirm' => 'Are you sure you want to delete this item?',
									'method' => 'post',
								],
							]);
						},

					] */
				],
			],
            ]); ?>
		<?php Pjax::end(); ?>
        </div>
    </div>
</div>
