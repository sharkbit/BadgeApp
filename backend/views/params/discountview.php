<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Discount */

$this->title = $model->dis_name;
if(yii::$app->controller->hasPermission('site/admin-menu')) {
	$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']]; }
$this->params['breadcrumbs'][] = ['label' => 'Discount List', 'url' => ['discount']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-xs-12">
		<?php // $this->render('_view-tab-menu') ?>
	</div>
	<div class="col-xs-12" class="discount-view">

    <h2><?= Html::encode($this->title) ?></h2>

    <p>
        <?= Html::a('Update', ['discountupdate', 'id' => $model->dis_id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [

            'dis_id',
            'dis_name',
            'dis_short',
			'dis_amount',
			[   'attribute' => 'dis_allowed',
                'value' => function($model) { return implode(", ", $model->dis_allowed); },
                'headerOptions' => ['style' => 'width:5%'],
            ],
            [   'attribute' => 'dis_active',
                'value' => function($model) { if($model->dis_active==0) return'No'; else return 'Yes'; },
                'headerOptions' => ['style' => 'width:5%'],
            ],
        ],
    ]) ?>
	</div>
</div>