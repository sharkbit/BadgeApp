<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Clubs */

$this->title = 'Update Club : ' . $model->club_name;
$this->params['breadcrumbs'][] = ['label' => 'Admin Function', 'url' => ['/badge/admin-function']];
$this->params['breadcrumbs'][] = ['label' => 'Member Club List', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->club_id, 'url' => ['view', 'id' => $model->club_id]];
$this->params['breadcrumbs'][] = 'Update';

?>
<div class="clubs-update">
<h2><?= Html::encode($this->title) ?></h2>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>