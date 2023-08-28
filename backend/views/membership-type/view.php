<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\MembershipType */

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
			[	'attribute'=>'self_service',
				'value'=>function($model) { if($model->self_service=='1') {return 'Visible'; } else {return 'Not Visible'; } },
			],            
			'sku_full',
            [
                'attribute'=>'fullprice.price',
                'value'=>function($model,$attribute) {
					$formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
                    return $formatter->formatCurrency($model->fullprice->price, 'USD');
                },
            ],
			'sku_half',
			            [
                'attribute'=>'halfprice.price',
                'value'=>function($model,$attribute) {
					$formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
                    return $formatter->formatCurrency($model->halfprice->price, 'USD');
                },
            ],
			[	'attribute'=>'renew_yearly',
				'value'=>function($model) { if($model->renew_yearly=='1') {return 'Yes'; } else {return 'No'; } },
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
