<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\WorkCredits */

$this->title = "View: ".$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Work Credits', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="work-credits-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p><?php
        echo Html::a('View Badge Profile', ['/badges/view', 'badge_number' => $model->badge_number], ['class' => 'btn btn-success']);
        if(yii::$app->controller->hasPermission('work-credits/update')) {
			echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']); }
        if(yii::$app->controller->hasPermission('work-credits/delete')) {
			echo Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
			]); } ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
			[
				'attribute' => 'badge_number',
				'value' => function($model) {
					return yii::$app->controller->decodeBadgeName((int)$model->badge_number).' ('.$model->badge_number.')';
				},
			],
            [    
                'attribute'=>'work_date',
                'value'=>function($model,$attribute) {
                    return date('M d, Y',strtotime($model->work_date));
                },
            ],
            'work_hours',
            'project_name',
			'supervisor',
            'remarks:ntext',
            [
                'attribute'=>'created_at',
                'value'=> function($model, $attribute) {
                    return date('M d, Y h:i A', strtotime($model->created_at));
                },
            ],
			[	'attribute'=>'status',
				'format' => 'raw',
				'value'=> function($model, $attribute) {
					if($model->status==1) {return 'Approved';} 
					else if($model->status==2) {
						if(yii::$app->controller->hasPermission('work-credits/approve')) {
							return Html::a('Pending Approval','/work-credits/approve?id='.$model->id);} 
						else {return 'Pending';}
					} 
					else if($model->status==3) {return 'Paid';}
				},
			],
			'authorized_by',
            [
                'attribute'=>'updated_at',
                'value'=> function($model, $attribute) {
                    return date('M d, Y h:i A', strtotime($model->updated_at));
                },
            ],
        ],
    ]) ?>

</div>
