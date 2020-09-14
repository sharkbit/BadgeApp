<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use backend\models\MembershipType;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\MembershipTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Membership Types & Prices';
$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/membership-type/index']];
?>
<div class="membership-type-index">

    <div class="row">
        <div class="col-xs-12">
            <h2><?= Html::encode($this->title) ?></h2>
            <p> <?= Html::a('Create Membership Type', ['create'], ['class' => 'btn btn-success pull-right']) ?> </p>

            <?php Pjax::begin(); ?>
            <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
						'id',
                        [	'attribute'=>'type',
                            'value'=>'type',
                            'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'id', (new MembershipType)->getMembershipList() ,['class'=>'form-control','prompt' => 'All']),
                        ],
						[	'attribute'=>'self_service',
							'value'=>function($model) { if($model->self_service=='1') {return 'Visible'; } else {return 'Not Visible'; } },
						],
                        'sku_full',
						[	'attribute'=>'full_price',
							'value'=>function($model) { return $model->fullprice->price; },
						],
                        'sku_half',
						[	'attribute'=>'half_price',
							'value'=>function($model) { return $model->halfprice->price; },
						],
                        [	'attribute'=>'stauts',
                            'value' => function($model, $attribute){ if($model->status==1) {return 'Active';} else if($model->status==0) {return 'Inactive';} },
                            'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'status', ['1' => 'Active', '0' => 'Inactive'],['class'=>'form-control','prompt' => 'All']),
                        ],
                        ['class' => 'yii\grid\ActionColumn'],
                    ],
                ]); ?>
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>
