<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\cal_calendar */

$this->title = $model->event_name;
$this->params['breadcrumbs'][] = ['label' => 'Calendar List', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-xs-12" class="calendar-view">

    <h2><?= Html::encode($this->title) ?></h2>

    <p>
        <?php //= Html::a('Update', ['update', 'id' => $model->club_id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [

            'event_name',
            'event_date',
            'poc_badge',
			'date_requested',
			'cal_start_time',
            'cal_end_time',
		/*	[	'attribute' => 'is_club',
                'value' => function($model) { if($model->is_club==0) return'COI or Other'; else return 'Yes'; },
                'headerOptions' => ['style' => 'width:5%'],
			],*/
            [   'attribute' => 'deleted',
                'value' => function($model) { if($model->deleted==0) return'Deleted'; else return 'Active'; },
                'headerOptions' => ['style' => 'width:5%'],
            ],

        ],
    ]) ?>
	</div>
</div>