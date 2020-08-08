<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\BadgesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Store';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];

if (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=20;
}
$dataProvider->pagination = ['pageSize' => $pagesize];

echo $this->render('_view-tab-menu').PHP_EOL ?>
<p> </p>

<div class="row">
     <div class="col-xs-4">

    </div>
	<?php if(yii::$app->controller->hasPermission('badges/all')) {
	//echo $this->render('_search',['model'=>$searchModel,'badgesModel'=>$badgesModel]).PHP_EOL; 
	} ?>
</div>
<div class="row">

    <div class="col-xs-12">
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
			'item',
            'sku',
			'price',
			'stock',
			'img',
			[	'attribute' => 'new_badge',
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'new_badge',['1'=>'Yes','0'=>'No'],['class'=>'form-control','prompt' => 'All']),
				'value' => function($model, $attribute) { if($model->new_badge) {return 'Yes';} else { Return 'No';} },
			],
			[	'attribute' => 'active',
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'active',['1'=>'Yes','0'=>'No'],['class'=>'form-control','prompt' => 'All']),
				'value' => function($model, $attribute) { if($model->active) {return 'Yes';} else { Return 'No';} },
			],
			[   'header' => 'Actions',
                'class' => 'yii\grid\ActionColumn',
				'template'=>' {view} {update} {print} {delete} ',
			]
        ],
    ]); ?>
    </div>
</div>
<div class="badges-index">

</div>
