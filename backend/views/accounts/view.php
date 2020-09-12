<?php

use backend\models\clubs;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\User */

$this->title = $model->full_name;
$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']];
$this->params['breadcrumbs'][] = ['label' => 'Authorized Users', 'url' => ['/accounts/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h2><?= Html::encode($this->title) ?></h2>

    <p><?php
	if(!$model->id==0){
		if ((in_array(1, json_decode(yii::$app->user->identity->privilege))) ||
		((yii::$app->controller->hasPermission('accounts/update')) && (!array_intersect([1,2],json_decode($model->privilege))))) {
			echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-success']).PHP_EOL;
		}

		if(yii::$app->controller->hasPermission('accounts/delete')) {
			echo Html::a('Delete', ['delete', 'id' => $model->id], [
				'class' => 'btn btn-danger',
				'data' => [
					'confirm' => 'Are you sure you want to delete this item?',
					'method' => 'post',],]).PHP_EOL;
		}
	}
	?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'username',
            'email:email',
            'full_name',
			[
                'attribute' =>'company',
				'label'=>'Company',
                'value' => function($model) {
					if(in_array(8,json_decode($model->privilege))) { return $model->company; } },
				'visible'=> (in_array(8,json_decode($model->privilege))) ? true : false,
            ],
			[
				'attribute'=>'badge_number',
				'format' => 'raw',
				'value'=>function($model) { 
					if($model->badge_number >0){
						return Html::a($model->badge_number,'/badges/view?badge_number='.$model->badge_number); 
					} else {return $model->badge_number;} 
				}
			],
            [
                'attribute' =>'privilege',
                'value' => function($model) { return $model->getPrivilege_Names($model->privilege);},
            ],
			[
				'attribute' => 'clubs',
				'value'=> function($model) {
					if(is_array(json_decode($model->clubs))) {
					$clubList = (new clubs)->getClubList();	$clubStr='';
					foreach(json_decode($model->clubs) as $club) { $clubStr.=$clubList[$club].', '; }
					return rtrim($clubStr, ', ');
				}}
			],
            [
                'attribute' =>'status',
                'value' => function($model) { if($model->status==10) return'Active'; else return 'Inactive'; },
            ],
            [
                'attribute' =>'created_at',
                'value' => function($model) { return date('M d, Y H:i:s',$model->created_at); },
            ],
            [
                'attribute' =>'updated_at',
                'value' => function($model) { return date('M d, Y H:i:s',$model->updated_at); },
            ],
        ],
    ]) ?>

</div>
