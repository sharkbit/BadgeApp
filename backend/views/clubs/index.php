<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\ClubsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Member Club List';
$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/clubs/index']];
?>
<div class="clubs-index">
    <div class="row">
        <div class="col-xs-12">
            <h2><?= Html::encode($this->title) ?></h2>

            <div class="btn btn-group pull-right"> 
                <?= Html::a('Create Club', ['create'], ['class' => 'btn btn-success']) ?> 
            </div >
            
            <?php Pjax::begin(); ?>    
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    //['class' => 'yii\grid\SerialColumn'],
                    [   
                        'attribute'=>'club_id',
                        'headerOptions' => ['style' => 'width:15%'],
                    ],
                    'club_name',
                    'short_name',
					'avoid',
                    'poc_email',
					
                    'is_club' => [   'attribute'=>'is_club',
                            'value' => function($model, $attribute) { if($model->is_club==0) {return 'No';} elseif($model->is_club==1){ return 'Yes';} else { return 'AGC Sponsored'; } },
                            'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'is_club', ['0' => 'No', '1' => 'Yes' , '2' => 'AGC Sponsored'],['class'=>'form-control','prompt' => 'All']),
                    ],
					
                    'status' => [   'header'=>'Status',
                            'value' => function($model, $attribute){ if($model->status==0) {return 'Active';} else if($model->status==1) {return 'Inactive';} },
                            'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'status', ['0' => 'Active', '1' => 'Inactive'],['class'=>'form-control','prompt' => 'All']),
                    ],
					
                    //'status',
                    [
                        'header'=>'Action',
                        'class' => 'yii\grid\ActionColumn'
                    ],
                ],
            ]); ?>
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>
