<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Calendar */

$this->title = "Create Calendar Item";
//if (($model->recur_every) && ($model->recurrent_calendar_id == 0)) {$myUrl='recur';} else {$myUrl='index';}
if ((isset($_GET['recur'])) && ($_GET['recur']==1)) {$myUrl='recur';} else {$myUrl='index';}
$this->params['breadcrumbs'][] = ['label' => 'Calendar', 'url' => ['calendar/'.$myUrl]];
$this->params['breadcrumbs'][] = ['label' => $this->title];

?>
<div class="calendar-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
