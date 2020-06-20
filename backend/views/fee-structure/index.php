<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use backend\models\FeesStructure;
$feesStructure = new FeesStructure();
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\FeesStructureSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Fee Schedules';
$this->params['breadcrumbs'][] = ['label' => 'Admin Function', 'url' => ['/badge/admin-function']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/fee-structure/index']];
?>
<div class="fees-structure-index">

    <div class="row">
        <div class="col-xs-12">
            <h2><?= Html::encode($this->title) ?></h2>
            <p> <?= Html::a('Create Fees Structure', ['create'], ['class' => 'btn btn-success pull-right']) ?> </p>

            <?php Pjax::begin(); ?>    
            <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        //['class' => 'yii\grid\SerialColumn'],

                        //'id',
                        'label',
                        [    
                            'header'=>'Membership Type',
                            'attribute'=>'membership_id',
                            'value'=>'membershipType.type',
                            'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'membership_id', $feesStructure->getMembershipList() ,['class'=>'form-control','prompt' => 'All']),

                        ],
						'membership_id',
                        'type' => [   'header'=>'Fee Type',
                            'value' => function($model, $attribute){ if($model->type=='badge_fee') {return 'Badge Fee';} else if($model->type=='certification') {return 'Certification Fee';} },
                            'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'type', ['badge_fee' => 'Badge Fee', 'certification' => 'Certification Fee'],['class'=>'form-control','prompt' => 'All']),
                        ],
                        'sku_full',
                        'sku_half',
                        [
                            'attribute'=>'fee',
                            'value'=>function($model,$attribute) {
                                return money_format('$%i', $model->fee);
                            },
                            'contentOptions' => ['class' => 'text-right'],
                        ],
                        'status' => [   'header'=>'Status',
                            'value' => function($model, $attribute){ if($model->status==0) {return 'Active';} else if($model->status==1) {return 'Inactive';} },
                            'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'status', ['0' => 'Active', '1' => 'Inactive'],['class'=>'form-control','prompt' => 'All']),
                        ],
                        ['class' => 'yii\grid\ActionColumn'],
                    ],
                ]); ?>
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>
