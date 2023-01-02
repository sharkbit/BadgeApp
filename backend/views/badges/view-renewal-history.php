<?php

use backend\models\Badges;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model backend\models\Badges */

$this->title = $model->badge_number;
$this->params['breadcrumbs'][] = ['label' => 'Range Badges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$urlStatus = yii::$app->controller->getCurrentUrl();
?>
<div class="badges-view">
    <div class="row" >
        <div class="col-xs-12">
            <?= $this->render('_view-tab-menu',['model'=>$model]) ?>
            <h3> Renewal History </h3>
		</div>
	</div>
	<div class="row" >
    	<div class="col-xs-12 col-sm-12">

           <?php Pjax::begin(); ?>
<?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'badge_year',
            'sticker',
            [
                'header'=>'Payment Type',
                'attribute'=>'payment_type',
                'value'=> function($model) {
                    switch ($model->payment_type) {
                        case 'cash':
                            return 'Cash';
                            break;
                        case 'check':
                            return 'Check';
                            break;
                        case 'credit';
                            return 'Credit Card';
                            break;
                        case 'online':
                            return 'Online';
                            break;
                        case 'other':
                            return 'Other';
                            break;
                        default:
                            return 'Not Available';
                            break;
                    }
                },

            ],
            [
                'header'=>'Status',
                'attribute'=>'status',
                'value'=>function($model) {
                    return ucfirst($model->status);
                },
            ],
            [
                'header'=>'Badge Fee',
                'attribute'=>'badge_fee',
                'value'=>function($model) {
					$formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
                    return $formatter->formatCurrency($model->badge_fee, 'USD');
                },
            ],
            [
                'header'=>'Paid Amount',
                'attribute'=>'paid_amount',
                'value'=>function($model) {
					$formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
                    return $formatter->formatCurrency($model->paid_amount, 'USD');
                },
            ],
            [
                'header'=>'Discount',
                'attribute'=>'discount',
                'value'=>function($model) {
					$formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
                    return $formatter->formatCurrency($model->discount, 'USD');
                },
            ],
            [
                'header'=>'Renewed on',
                'attribute'=>'created_at',
                'value'=>function($model) {
                    return date('M d, Y h:i A',strtotime($model->created_at));
                },
            ],
            [
				'class' => 'yii\grid\ActionColumn',
				'template'=>' {view} {update} {print} {email} {delete}',
				'buttons'=>[
					'update' => function ($url, $model) {
						if(yii::$app->controller->hasPermission('badges/update-renewal')) {
						return  Html::a(' <span class="glyphicon glyphicon-pencil"></span> ', ['/badges/update-renewal','id'=>$model->id], [
							'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'Edit',
							'class'=>'edit_item',
						]); }
					},
					'view' => function ($url, $model) {
						return  Html::a(' <span class="glyphicon glyphicon-eye-open"></span> ', ['/badges/view-subscriptions','id'=>$model->id], [
							'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'View',
						]);
					},
					'print' => function($url,$model) {
						if($model->cc_x_id) {
						return  Html::a(' <span class="glyphicon glyphicon-print"></span> ',
							['/badges/print-rcpt','x_id'=>$model->cc_x_id,'badge_number'=>$model->badge_number], [
							'target'=>'_blank',
							'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'Print',
						]); }
					},
					'email' => function($url,$model) {
						if($model->cc_x_id) {
							$model_badge = Badges::find()->where(['badge_number'=>$model->badge_number])->one();
							$email=$model_badge->email;
							if($email) {
								return  Html::a(' <span class="glyphicon glyphicon-envelope"></span> ',
									['/badges/print-rcpt','x_id'=>$model->cc_x_id,'badge_number'=>$model->badge_number,'email'=>$email], [
									'target'=>'_blank',
									'data-toggle'=>'tooltip',
									'data-placement'=>'top',
									'title'=>'Email',
								]); 
							} 
						}
					},
					'delete' => function ($url, $model) {
						if((yii::$app->controller->hasPermission('badges/delete-renewal')) && ($model->is_migrated==0)) {
						return  Html::a(' <span class="glyphicon glyphicon-trash"></span> ', ['/badges/delete-renewal','badge_number'=>$model->badge_number,'id'=>$model->id], [
							'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'Delete',
							'class'=>'delete_item',
						]); }
					}
					
				]
			]
        ],
    ]); ?>
<?php Pjax::end(); ?>
		</div>
	</div>
</div>

<style>
.info-box-credit {
    width: 100%;
    float: left;
}
.info-box-icon {
    float: left;
    width: 30%;
}
.aqua {
    background: #00aff0;
    color: #fff;
}
i.fa.fa-user {
    font-size: 60px !important;
}
.info-box-details .head{
    padding: 4px;
    font-size: 16px;
    color: #262626;
}
.info-box-credit {
    background: #eee;
    border: 1px solid #00aff0;
}
.info-box-details {
    margin-left: 122px;
}
.info-box-details h4 {
    font-size: 16px;
}
.info-box-details span {
    font-size: 26px;
}
</style>