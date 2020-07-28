<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\Privileges */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Privileges';
$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/privileges/index']];
?>
<div class="privileges-index">
    <div class="row">
        <div class="col-xs-12">
            <h2><?= Html::encode($this->title) ?></h2>

            <div class="btn btn-group pull-right"> 
                <?= Html::a('Add Privilege', ['create'], ['class' => 'btn btn-success']) ?> 
            </div >
            
            <?php Pjax::begin(); ?>    
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
       
                    [   
                        'attribute'=>'id',
                        'headerOptions' => ['style' => 'width:15%'],
                    ],
                    'privilege',
                    'priv_sort',
                    'timeout',
                    [
                        'header'=>'Action',
                        'class' => 'yii\grid\ActionColumn',
						'template'=>' {update} {delete} ',
                    ],
                ],
            ]); ?>
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>
