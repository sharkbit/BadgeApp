<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Clubs */

$this->title = $model->club_name;
if(yii::$app->controller->hasPermission('site/admin-menu')) {
	$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']]; }
$this->params['breadcrumbs'][] = ['label' => 'Member Club List', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-xs-12">
		<?= $this->render('_view-tab-menu') ?>
	</div>
	<div class="col-xs-12" class="clubs-view">

    <h2><?= Html::encode($this->title) ?></h2>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->club_id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [

            'club_id',
            'club_name',
            'short_name',
			'avoid',
            'poc_email',
			[	'attribute' => 'is_club',
                'value' => function($model) { if($model->is_club==0) return'COI or Other'; else return 'Yes'; },
                'headerOptions' => ['style' => 'width:5%'],
			],
            [   'attribute' => 'allow_members',
                'value' => function($model) { if($model->allow_members==0) return'No'; else return 'Yes'; },
                'headerOptions' => ['style' => 'width:5%'],
            ],
            [   'attribute' => 'status',
                'value' => function($model) { if($model->status==0) return'Active'; else return 'Inactive'; },
                'headerOptions' => ['style' => 'width:5%'],
            ],

        ],
    ]) ?>
	</div>
</div>