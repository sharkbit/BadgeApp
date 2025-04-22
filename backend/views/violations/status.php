<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\ViolationStatus;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ViolationStatusSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Violation Status';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="violation-status-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'badge_number',
            'violation_count',
            [
                'attribute' => 'status',
                'value' => function($model) {
                    return ViolationStatus::getStatusLabel($model->status);
                },
                'filter' => ViolationStatus::getStatusOptions(),
            ],
            'last_violation_date',
            'blocked_until',
            'admin_contact_required:boolean',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', 
                            ['/violations/index', 'ViolationsSearch[badge_involved]' => $model->badge_number],
                            ['title' => 'View Violations']);
                    },
                    'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', 
                            ['/violations/create', 'badge_involved' => $model->badge_number],
                            ['title' => 'Add Violation']);
                    },
                ],
            ],
        ],
    ]); ?>

</div> 