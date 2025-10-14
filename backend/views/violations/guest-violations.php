<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\Violations;
use backend\models\search\ViolationsSearch;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\ViolationsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Guest Violations';
$this->params['breadcrumbs'][] = ['label' => 'Violations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Get guest violation statistics
$sql = "SELECT 
    COUNT(*) as total_guest_violations,
    COUNT(DISTINCT badge_involved) as unique_guests,
    COUNT(CASE WHEN vi_type = '4' THEN 1 END) as class4_violations
FROM violations 
WHERE was_guest = 1";
$connection = Yii::$app->getDb();
$command = $connection->createCommand($sql);
$stats = $command->queryOne();
?>

<div class="violations-guest">
    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Guest Violations</h5>
                    <p class="card-text display-4"><?= $stats['total_guest_violations'] ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Unique Guests with Violations</h5>
                    <p class="card-text display-4"><?= $stats['unique_guests'] ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Class 4 Violations</h5>
                    <p class="card-text display-4"><?= $stats['class4_violations'] ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                    ],
                    [
                        'attribute' => 'badge_involved',
                        'label' => 'Guest Badge',
                        'value' => function($model) {
                            return $model->badge_involved;
                        }
                    ],
                    [
                        'attribute' => 'vi_date',
                        'format' => 'datetime',
                        'value' => function($model) {
                            return Yii::$app->formatter->asDatetime($model->vi_date);
                        }
                    ],
                    [
                        'attribute' => 'vi_type',
                        'label' => 'Violation Class',
                        'value' => function($model) {
                            return 'Class ' . $model->vi_type;
                        },
                        'filter' => [
                            '1' => 'Class 1',
                            '2' => 'Class 2',
                            '3' => 'Class 3',
                            '4' => 'Class 4'
                        ]
                    ],
                    [
                        'attribute' => 'vi_loc',
                        'label' => 'Location',
                        'value' => function($model) {
                            return $model->getLocations($model->vi_loc);
                        }
                    ],
                    [
                        'attribute' => 'vi_rules',
                        'label' => 'Rules Violated',
                        'format' => 'ntext'
                    ],
                    [
                        'attribute' => 'vi_sum',
                        'label' => 'Summary',
                        'format' => 'ntext'
                    ],
                    [
                        'attribute' => 'badge_reporter',
                        'label' => 'Reported By',
                        'value' => function($model) {
                            return $model->badge_reporter . ' (' . $model->reporter_name . ')';
                        }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view} {update}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('<i class="fa fa-eye"></i>', ['view', 'id' => $model->id], [
                                    'title' => 'View',
                                    'class' => 'btn btn-sm btn-info'
                                ]);
                            },
                            'update' => function ($url, $model) {
                                return Html::a('<i class="fa fa-pencil"></i>', ['update', 'id' => $model->id], [
                                    'title' => 'Update',
                                    'class' => 'btn btn-sm btn-primary'
                                ]);
                            },
                        ],
                    ],
                ],
                'tableOptions' => ['class' => 'table table-striped table-bordered'],
                'summary' => 'Showing <b>{begin}-{end}</b> of <b>{totalCount}</b> guest violations.',
            ]); ?>
        </div>
    </div>
</div>

<?php
// Add custom CSS
$this->registerCss("
    .card {
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .card-body {
        text-align: center;
    }
    .display-4 {
        font-size: 2.5rem;
        font-weight: 300;
        line-height: 1.2;
    }
    .mt-4 {
        margin-top: 1.5rem;
    }
");
?> 