<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\Badges;
use backend\models\clubs;

/* @var $this yii\web\View */
/* @var $model backend\models\BadgesDatabase */

$this->title = $model->badge_number;
$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']];
$this->params['breadcrumbs'][] = ['label' => 'Badge Databases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="badges-database-view">

    <h3><?= Html::encode($this->title) ?></h3>

    <p>
        <?= Html::a('Update', ['update', 'badge_number' => $model->badge_number], ['class' => 'btn btn-primary']) ?>
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
            'badge_number',
			[
				'attribute'=>'status',
				'value'=>function($model,$attribute) {
					return (new Badges)->getMemberStatus($model->status);}
			],
            'prefix',
            'first_name',
            'last_name',
            'suffix',
            'address:ntext',
            'city',
            'state',
            'zip',
            [
				'attribute'=>'gender',
				'value'=> function($model, $attribute) {
					if($model->gender) { return 'Female';} else { return 'Male';}
				}
			],
            'yob',
            'email:email',
            'phone',
            'phone_op',
            'ice_contact',
            'ice_phone',
			'club_id',
			[
				'attribute' => 'club_name',
				'value'=> function($model, $attribute) {
					return (new clubs)->getMyClubsNames($model->badge_number);
				},
			],
            'mem_type',
            'primary',
            'incep',
            'expires',
            'qrcode:ntext',
            'wt_date',
            'wt_instru',
            'remarks:ntext',
            'created_at',
            'updated_at'		
		],
    ]) ?>

</div>
