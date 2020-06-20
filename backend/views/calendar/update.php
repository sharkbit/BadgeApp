<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Calendar */

$this->title = "Calendar Item: $model->event_name";

if (($model->recur_every) && ($model->recurrent_calendar_id == $model->calendar_id)) {$myUrl='recur';} else {$myUrl='index';}
$this->params['breadcrumbs'][] = ['label' => 'Calendar', 'url' => ['calendar/'.$myUrl]];
$this->params['breadcrumbs'][] = ['label' => $this->title];

?>
<div class="calendar-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
