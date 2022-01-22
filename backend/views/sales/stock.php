<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\StoreItems;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\BadgesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Store Items';
$this->params['breadcrumbs'][] = ['label' => 'Store', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['sales/stock']];

if (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=20;
}
$dataProvider->pagination = ['pageSize' => $pagesize];

echo $this->render('_view-tab-menu').PHP_EOL ?>
<p> </p>

<div class="row">

    <div class="col-xs-12">
	<?php if(yii::$app->controller->hasPermission('sales/create')) { ?>
	<div class="btn-group pull-right">
	<?= Html::a('<i class="fa fa-plus-square" aria-hidden="true"></i> Add Store Item', ['create'], ['class' => 'btn btn-success ']) ?>

	</div>
<?php } ?>
        <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => [
			'firstPageLabel' => 'First',
			'lastPageLabel'  => 'Last'
		],
		'columns' => [
			[	'attribute' => 'type',
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'type',$searchModel->getTypes(),['class'=>'form-control','prompt' => 'All']),
			],
			[	'header' => 'Group',
				'attribute' => 'paren',
				'value' => function($model, $attribute) { return (new StoreItems)->getParen($model->paren); },
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'paren',$searchModel->getGroups(),['class'=>'form-control','prompt' => 'All']),
			],
			'item',
            'sku',
			'price',
			'tax_rate',
			[	'attribute'=>'stock',
				'value' => function($model, $attribute) { if($model->type=='Kits') { return ''; } else { return $model->stock; } },
			],
			'img',
			[	'attribute' => 'new_badge',
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'new_badge',['1'=>'Yes','0'=>'No'],['class'=>'form-control','prompt' => 'All']),
				'value' => function($model, $attribute) { if($model->new_badge) {return 'Yes';} else { Return 'No';} },
			],
			[	'attribute' => 'active',
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'active',['1'=>'Yes','0'=>'No'],['class'=>'form-control','prompt' => 'All']),
				'value' => function($model, $attribute) { if($model->active) {return 'Yes';} else { Return 'No';} },
			],
			[
                'header' => 'Actions',
                'class' => 'yii\grid\ActionColumn',
				'template'=>' {update} {delete} ',
				'buttons'=>[
					'update' => function ($url, $model) {
						if(yii::$app->controller->hasPermission('sales/update')) {
						return  Html::a(' <span class="glyphicon glyphicon-pencil"></span> ', ['/sales/update','id'=>$model->item_id], [
							'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'Edit',
							'class'=>'edit_item',
						]); }
					},
					'delete' => function($url,$model) {
						if(yii::$app->controller->hasPermission('sales/delete')) {
							$chk_sub = StoreItems::find()->where(['paren'=>$model->item_id])->all();
							if(!$chk_sub) {
						return  Html::a(' <span class="glyphicon glyphicon-trash"></span> ', $url, [
							'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'Delete',
							'data' => [
								'confirm' => 'Are you sure you want to delete '.$model->item.'?',
								'method' => 'post',
							],
						]); } }
					},
				],
            ],

        ],
    ]); ?>
    </div>
</div>
<div class="badges-index">

</div>
