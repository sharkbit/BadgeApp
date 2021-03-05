<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model backend\models\Badges */

$this->title = $model->badge_number;
$this->params['breadcrumbs'][] = ['label' => 'Range Badges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$urlStatus = yii::$app->controller->getCurrentUrl();
?>
<div class="badges-view">
    <div class="row" > 
        <div class="col-xs-12">
            <?= $this->render('_view-tab-menu',['model'=>$model]).PHP_EOL ?>
            <h3> Certifications  </h3>
            <div class="col-xs-12 col-sm-12">
             
           <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'header'=>'Certifications',
                        'value' => function($model) {
						return $model->store_items->item;} ,
                    ],
					[
						'attribute'=>'status',
						'value' => function($model) {
                            if($model->status=='0') {return 'Active'; }
							else if($model->status=='1') {return 'Suspended';}
							else if($model->status=='2') {echo "Revoked"; }
                        },
                    ],
                    [
                        'header'=> 'Sticker',
                        'value' => 'sticker',
                    ],
                    [
                        'header'=>'Fee',
                        'value' => function($model) {
							$formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
							return $formatter->formatCurrency($model->fee, 'USD');
                        },
                    ],
                    [
                        'header'=>'Discount',
                        'value' => function($model) {
							$formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
							return $formatter->formatCurrency($model->discount, 'USD');
                        },
                    ],
                    [
                        'header'=>'Paid Amount',
                        'value' => function($model) {
							$formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
							return $formatter->formatCurrency($model->amount_due, 'USD');
                        },
                    ],
                    [
                        'header'=>'Issued on',
                        'value' => function($model) {
                            return date('M d, Y h:i A',strtotime($model->created_at));
                        },
                    ],
                    
                    ['class' => 'yii\grid\ActionColumn',
						'template'=>'{view}{update}{delete}',
						'buttons'=>[
						'update' => function ($url, $model) {    
							if(!yii::$app->controller->hasPermission('badges/delete-renewal')) {
								return  Html::a(' <span class="glyphicon glyphicon-pencil"></span> ',['/badges/update-certificate','membership_id'=>$model->badge_number,'view_id'=>$model->id], [
									'data-toggle'=>'tooltip',
									'data-placement'=>'top',
									'title'=>'Edit',
									'class'=>'edit_item',
								]); } else {return '';}
						},
						'view' => function($url,$model) {
							
							return  Html::a(' <span class="glyphicon glyphicon-eye-open"></span> ', ['/badges/view-certificate','membership_id'=>$model->badge_number,'view_id'=>$model->id], [
								'target'=>'_blank',
								'data-toggle'=>'tooltip',
								'data-placement'=>'top',
								'title'=>'View',
							]); 
						},
						'delete' => function($url,$model) {
							if(yii::$app->controller->hasPermission('badges/delete')) {
							return  Html::a(' <span class="glyphicon glyphicon-trash"></span> ', ['/badges/delete-certificate','membership_id'=>$model->badge_number,'view_id'=>$model->id], [
									'data-toggle'=>'tooltip',
									'data-placement'=>'top',
									'title'=>'Delete',
									'data' => [
										'confirm' => 'Are you sure you want to delete this item?',
										'method' => 'post',
									],
							]); } else{ return '';}
						},
                    ]                            
                ],
            ],
    ]).PHP_EOL ?>
            </div>
            <div class="col-xs-12 col-sm-8">

            </div>
        </div>
    </div>
</div>


