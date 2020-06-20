<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Clubs */

$this->title = $model->rule_abrev;
$this->params['breadcrumbs'][] = ['label' => 'Admin Function', 'url' => ['badge/admin-function']];
$this->params['breadcrumbs'][] = ['label' => 'Rules List', 'url' => ['rules/index']];
$this->params['breadcrumbs'][] = "View: ".$this->title;
?>
<div class="clubs-view">

    <h2><?= Html::encode($this->title) ?></h2>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php /* Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) */ ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [

            'rule_abrev',
			'vi_type',
            'rule_name',
			[	
				'attribute'=>'is_active',
				'value'=>function($model) { if($model->is_active) {return "Yes";} else  {return "No";} },
			],
                        
        ],
    ]) ?>

</div>