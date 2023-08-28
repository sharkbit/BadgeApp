<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Badges */

$this->title = 'Update Badge: ' . $model->badge_number;
$this->params['breadcrumbs'][] = ['label' => 'Range Badges', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->badge_number, 'url' => ['view', 'badge_number' => $model->badge_number]];
?>
<div class="badges-update" ng-controller="UpdateBadgeController">
	<?=$this->render('_view-tab-menu',['model'=>$model]) ?>
	
    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_update', [
        'model' => $model,
		'confParams'=>$confParams,
        'badgeSubscriptions'=>$badgeSubscriptions,
        'badgeCertification'=> $badgeCertification,
    ]) ?>

</div>
