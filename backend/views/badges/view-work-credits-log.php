<?php

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

            <h3> Work Credits Logs </h3>
            <div class="col-xs-12 col-sm-12">

           <?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            
            [
                'header'=>'Transaction Date',
                'value'=>function($model) {
                    return date('M d, Y h:i A',strtotime($model->updated_at));
                },
                'headerOptions' => ['style' => 'width:20%'],
            ],
            [
                'header'=>'Transaction ID',
                'value'=>function($model) {
                    return '#'.$model->id;
                },
                'headerOptions' => ['style' => 'width:14%'],

            ],
            [
                'header'=>'Narration',
                'value'=>'remarks',
            ],

            [
                'header'=>'Debit',
                'value'=> function($model) {
                    if($model->type=='debit') {
                        return $model->value;
                    }
                    else {
                        return '';
                    }
                },
                'contentOptions' => ['class'=>'text-right'],
                'headerOptions' => ['class'=>'text-right'],
            ],
            [
                'header'=>'Credit',
                'value'=> function($model) {
                    if($model->type=='credit') {
                        return $model->value;
                    }
                    else {
                        return '';
                    }
                },
                'headerOptions' => ['class'=>'text-right'],
                'contentOptions' => ['class'=>'text-right'],
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?>
    
            </div>
            <div class="col-xs-12 col-sm-8">

            </div>
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
.info-box-details {

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