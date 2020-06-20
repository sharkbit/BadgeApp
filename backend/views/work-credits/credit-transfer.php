<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\WorkCreditsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

if($_GET['type']=='init'){$this->title = 'Pending Requests';} else {$this->title = 'Successful Requests';}

$this->params['breadcrumbs'][] = ['label' => 'Work Credit', 'url' => ['/work-credits/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="work-credits-index">
    <div class="row">
        <div class="col-xs-12">
             <?= $this->render('_view-tab-menu',[]) ?>
        </div>
        <div class="col-xs-12">
               
            <p> <?= Html::a('<i class="fa fa-plus"> </i> New Transfer Request', ['/work-credits/transfer-form'], ['class' => 'btn btn-success pull-right']) ?> </p>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    //['class' => 'yii\grid\SerialColumn'],

                    //'id',
                    'from_badge_number',
                    'to_badge_number',
                    [
                        'attribute'=>'work_hours',
                        'contentOptions' => ['class' => 'text-right'],
                    ],
                    
                    'status' => [   'header'=>'Status',
                            'value' =>function($model) {
                                if($model->status=='init') {
                                    return 'Waiting';
                                }
                                else if ($model->status=='success') {
                                    return 'Success';
                                }
                                else if ($model->status=='rejected') {
                                    return 'Rejected';
                                }
                                else if ($model->status=='pending') {
                                    return 'Pending';
                                }
                            },

                            'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'status', ['init' => 'Init', 'success' => 'Success','rejected'=>'rejected','pending'=>'Pending'],['class'=>'form-control','prompt' => 'All']),
                    ],
                    //'approved_by',
                    [
                        'header'=>'Created At',
                        'value'=> function($model) {
                            return date('M d, Y h:i A',strtotime($model->created_at));
                        },
                    ],
                   
                   [
                    'header'=>'Action',
                   'class' => 'yii\grid\ActionColumn',
                          'template'=>'{view}',
                            
                            'buttons'=>[

                                    'view' => function($url,$model) {
                                    return  Html::a(' View <span class="glyphicon glyphicon-eye-open"></span> ', ['/work-credits/transfer-view','request_id'=>$model->id], [
                                            'data-toggle'=>'tooltip',
                                            'data-placement'=>'top',
                                            'title'=>'View',
                                        ]); 

                                },
                            ]                            
                    ],
                    
                ],
            ]); ?>
        </div>
    </div>

    
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

   
    
</div>
