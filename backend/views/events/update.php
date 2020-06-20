<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Events */

$this->title = 'Update Event';
$this->params['breadcrumbs'][] = ['label' => 'Event List', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title.": ".$model->e_name, 'url' => ['update','id'=>$model->e_id ]];
?>
<div class="events-update">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
