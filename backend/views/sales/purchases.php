<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\BadgesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Purchases';
$this->params['breadcrumbs'][] = ['label' => 'Store', 'url' => ['index']];

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
			[
				'attribute'=>'badge_number',
				'contentOptions' =>['style' => 'width:100px'],
				'format' => 'raw',
				'value'=>function ($data) {
				//	if(yii::$app->controller->hasPermission('badge/create')) {
				//		return Html::a(str_pad($data->badge_number, 5, '0', STR_PAD_LEFT),'/badges/update?badge_number='.$data->badge_number);
				//	} else {
						return str_pad($data->badge_number, 5, '0', STR_PAD_LEFT); //,'/badges/view?badge_number='.$data->badge_number);
				//	}
				}
			],
			'name',
			'tx_date',
            [
				'attribute'=>'cart',
				'format' => 'raw',
				'value' => function($model, $attribute) {
					$items='';
			yii::$app->controller->createLog(false, 'trex_V_S_P:41', var_export($model->cart,true));
					foreach(json_decode($model->cart) as &$item ) {
						$items .=$item->item.' ['.$item->ea.' x '.$item->qty.'] '.($item->price)."<br/>\n";
					}
					
					//$cart = json_decode($model->cart);
					//yii::$app->controller->createLog(false, 'trex_V_S_P', var_export($cart,true));
					return $items;
				}
			],
			'amount',
			'tx_type',
			[
                'header' => 'Actions',
                'class' => 'yii\grid\ActionColumn',
				'template'=>' {print} ',
				//'template'=>' {view} {update} {print} {delete} ',
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
