<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\BadgesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Purchases';
$this->params['breadcrumbs'][] = ['label' => 'Store', 'url' => ['index']];

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
			[
				'attribute'=>'badge_number',
				'contentOptions' =>['style' => 'width:100px'],
				'format' => 'raw',
				'value'=>function ($data) {
					return str_pad($data->badge_number, 5, '0', STR_PAD_LEFT); 
				}
			],
			'name',
			'tx_date',
            [
				'attribute'=>'cart',
				'format' => 'raw',
				'value' => function($model, $attribute) {
					$items='';
					foreach(json_decode($model->cart) as &$item ) {
						if(isset($item) && isset($item->item)){
						$items .=$item->item.' ['.$item->ea.' x '.$item->qty.'] '.($item->price)."<br/>\n";
						}
					}
					return $items;
				}
			],
			'amount',
			'tx_type',
			[
                'header' => 'Actions',
                'class' => 'yii\grid\ActionColumn',
				'template'=>' {print} ',
				'buttons'=>[
					'print' => function($url,$model) {
						return  Html::a(' <span class="glyphicon glyphicon-print"></span> ', ['print-rcpt','x_id'=>$model->id,'badge_number'=>$model->badge_number], [
							'target'=>'_blank',
							'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'Print',
						]);
					}
				]
			]
        ],
    ]); ?>
	
    </div>
</div>
<div class="badges-index">

</div>
