<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\WorkCredits */

$this->title = 'Credit Transfer View';
$this->params['breadcrumbs'][] = ['label' => 'Work Credits', 'url' => ['/work-credits/index']];
$this->params['breadcrumbs'][] = ['label' => 'Credit Transfer', 'url' => ['/work-credits/credit-transfer','type'=>'init']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="work-credits-view">

    

    <p>
      <?php if($model->status!='success') { ?>  
        <?= Html::a('Approve ', ['/work-credits/transfer-confirm', 'id' => $model->id], [
            'class' => 'btn btn-success',
            'data' => [
                'confirm' => 'Are you sure you want to approve ?',
                'method' => 'post',
            ],
        ]) ?>
        <?php } ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
        	[
        		'attribute'=>'work_hours',
        		'label' => 'Transfer Credit',
        	],
        	'frombadgeDetails.badge_number',
            'frombadgeDetails.first_name',
            'frombadgeDetails.last_name',
            'tobadgeDetails.badge_number',
            'tobadgeDetails.first_name',
            'tobadgeDetails.last_name',
            [
        		'attribute'=>'work_hours',
        		'label' => 'Issued on',
        		'value' => function($model) {
        			return date('M d, Y i:m A',strtotime($model->created_at));
        		},
        	],
        	[
        		'attribute'=>'status',
        		'label' => 'Status',
        		'value' => function($model) {
        			if($model->status=='init') {
        				return 'Waiting for Approval';
        			}
        			else if($model->status=='success') {
        				return 'Success';
        			}
        			else if($model->status=='rejected') {
        				return 'Rejected';
        			}
        			else if($model->status=='pending') {
        				return 'pending';
        			}
        		},
        	],
        	'note',
        	'adminUser.username',
        	'approved_by',
        	[
        		'attribute'=>'approved_at',
        		'label' => 'Approval Date',
        		'value' => function($model) {
        			if($model->approved_at!=null) {
        				return date('M d, Y i:m A',strtotime($model->approved_at));	
        			}
        			else {
        				return 'Not available';
        			}
        			
        		},
        	],

            /*'badge_number',
            [    
                'attribute'=>'work_date',
                'value'=>function($model,$attribute) {
                    return date('M d, Y',strtotime($model->work_date));
                },
            ],
            'work_hours',
            'project_name',
            'remarks:ntext',
            'autherized_by',
            [
                'attribute'=>'created_at',
                'value'=> function($model, $attribute) {
                    return date('M d, Y h:i A', strtotime($model->created_at));
                },
            ],
            [
                'attribute'=>'updated_at',
                'value'=> function($model, $attribute) {
                    return date('M d, Y h:i A', strtotime($model->updated_at));
                },
            ],*/
        ],
    ]) ?>

</div>
