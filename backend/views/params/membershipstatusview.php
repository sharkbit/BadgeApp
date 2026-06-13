<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Discount */

$this->title = $model->act_name;
if(yii::$app->controller->hasPermission('site/admin-menu')) {
	$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']]; }
$this->params['breadcrumbs'][] = ['label' => 'Membership Status', 'url' => ['membershipstatus']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-xs-12">
		<?php // $this->render('_view-tab-menu') ?>
	</div>
	<div class="col-xs-12" class="discount-view">

    <h2><?= Html::encode($this->title) ?></h2>

    <p>
        <?= Html::a('Update', ['membershipstatusupdate', 'id' => $model->act_id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [

            'act_id',
            'act_name',
            'act_short',
			[   'attribute' => 'act_active',
                'value' => function($model) { if($model->act_active==0) return'No'; else return 'Yes'; },
               // 'headerOptions' => ['style' => 'width:5%'],
            ],
			[   'attribute' => 'act_login',
                'value' => function($model) { if($model->act_login==0) return'No'; else return 'Yes'; },
               // 'headerOptions' => ['style' => 'width:5%'],
            ],
			[   'attribute' => 'act_new',
                'value' => function($model) { if($model->act_new==0) return'No'; else return 'Yes'; },
               // 'headerOptions' => ['style' => 'width:5%'],
            ],
			[   'attribute' => 'act_renew',
                'value' => function($model) { if($model->act_renew==0) return'No'; else return 'Yes'; },
               // 'headerOptions' => ['style' => 'width:5%'],
            ],
			[   'attribute' => 'act_prefill',
                'value' => function($model) { if($model->act_prefill==0) return'No'; else return 'Yes'; },
               // 'headerOptions' => ['style' => 'width:5%'],
            ],
			[   'attribute' => 'act_signup',
                'value' => function($model) { if($model->act_signup==0) return'No'; else return 'Yes'; },
               // 'headerOptions' => ['style' => 'width:5%'],
            ],
			'act_order',
			'act_color',
			'act_desc',
        ],
    ]) ?>
	</div>
</div>
