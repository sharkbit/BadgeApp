<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\FeesStructure */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Admin Function', 'url' => ['/badge/admin-function']];
$this->params['breadcrumbs'][] = ['label' => 'Fees Structure', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fees-structure-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
			'type',
            'label',
			'membership_id',
            'membershipType.type',
            [
                'attribute'=>'fee',
                'value'=>function($model,$attribute) {
                    return money_format('$%i', $model->fee);
                },
            ],
			'sku_full',
			'sku_half',
            [       
                'attribute'=>'status',
                'value'=> function($model,$attribute) {
                    if($model->status=='0') {
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
