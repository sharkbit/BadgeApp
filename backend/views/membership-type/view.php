<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\FeesStructure */

$this->title = $model->type. " (".$model->id.")";
$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']];
$this->params['breadcrumbs'][] = ['label' => 'Membership Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fees-structure-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'type',
            
			'sku_full',
            [
                'attribute'=>'fullprice.price',
                'value'=>function($model,$attribute) {
                    return money_format('$%i', $model->fullprice->price);
                },
            ],
			'sku_half',
			            [
                'attribute'=>'halfprice.price',
                'value'=>function($model,$attribute) {
                    return money_format('$%i', $model->halfprice->price);
                },
            ],
            [       
                'attribute'=>'status',
                'value'=> function($model,$attribute) {
                    if($model->status=='1') {
                        return 'Active';
                    }
                    else {
                        return 'Inactive';
                    }
                },
            ],
            //'status',
        ],
    ]) ?>

</div>