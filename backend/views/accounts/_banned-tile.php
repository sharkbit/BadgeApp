<?php

use yii\helpers\Html;

/* @var $model backend\models\ViewBannedPeeps */
/* @var $index integer */

$photoName = null;
if ($model->ban_status === 'guest') {
    $candidate = $model->person_id . '.jpg';
    if (is_file(Yii::getAlias('@webroot') . '/files/badge_photos/' . $candidate)) {
        $photoName = $candidate;
    }
} elseif ($model->ban_status === 'x-member') {
    $candidate = str_pad($model->person_id, 5, '0', STR_PAD_LEFT) . '.jpg';
    if (is_file(Yii::getAlias('@webroot') . '/files/badge_photos/' . $candidate)) {
        $photoName = $candidate;
    } 
}

$fullName = trim($model->first_name . ' ' . $model->last_name);
?>
<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2 banned-tile">
    <div class="banned-tile-card">
        <?php if ($photoName !== null): ?>
            <?= Html::img('/files/badge_photos/' . $photoName, [
                'class' => 'banned-tile-photo',
                'alt' => $fullName,
            ]) ?>
        <?php else: ?>
            <div class="banned-tile-no-photo" role="img" aria-label="No photo on file">
                <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
            </div>
        <?php endif; ?>
        <div class="banned-tile-info">
            <h4 class="banned-tile-name"><?= Html::encode($fullName) ?></h4>
            <span class="label <?= $model->ban_status === 'x-member' ? 'label-danger' : 'label-warning' ?>">
                <?= Html::encode(ucfirst($model->ban_status)) ?>
            </span>
	<?php if ( (empty($photoName)) && (yii::$app->controller->hasPermission('badges/photo-add')) ) { ?>
		&nbsp; <b><?= Html::a(
            '<span class="glyphicon glyphicon-camera"></span> Add',
            ['/badges/photo-add', 'badge' => $model->person_id],
            ['encode' => false]
        ) ?></b>
	<?php } ?>
			
            <div>Person ID: <?= Html::encode($model->person_id) ?></div>
        </div>
    </div>
</div>
