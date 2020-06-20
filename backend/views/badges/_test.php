<?php 

use kartik\export\ExportMenu;
use kartik\grid\GridView;

    $gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
    	'header'=>'Badge Number',
    	'value' => 'badge_number',
    ],
    [
    	'header'=>'Transaction Date',
        'value'=>function($model) {
            return date('M d, Y h:i A',strtotime($model->updated_at));
        },
    ],
    
   // ['attribute'=>'buy_amount','format'=>['decimal',2], 'hAlign'=>'right', 'width'=>'110px'],
  //  ['attribute'=>'sell_amount','format'=>['decimal',2], 'hAlign'=>'right', 'width'=>'110px'],
    ['class' => 'kartik\grid\ActionColumn', 'urlCreator'=>function(){return '#';}]
];
 


echo ExportMenu::widget([
    'dataProvider' => $dataProvider,
    'columns' => $gridColumns,
    'fontAwesome' => true,
    'batchSize' => 20,
    'target' => '_blank',
    //'folder' => '@webroot/tmp', // this is default save folder on server
]) . "<hr>\n".
GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => $gridColumns,
]);




?>


