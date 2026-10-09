<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $bannedGuest backend\models\BannedGuests */

$this->title = 'Banned';
$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/accounts/banned']];

$this->registerCss('
    .banned-tiles { display: flex; flex-wrap: wrap; }
    .banned-tile { margin-bottom: 20px; }
    .banned-tile-card { height: 100%; overflow: hidden; border: 1px solid #ddd; border-radius: 4px; background: #fff; }
    .banned-tile-photo { display: block; width: 100%; height: auto; }
    .banned-tile-no-photo { display: flex; aspect-ratio: 13 / 17; align-items: center; justify-content: center; background: #f3f3f3; color: #777; }
    .banned-tile-no-photo .glyphicon { font-size: 52px; }
    .banned-tile-info { padding: 10px 12px; }
    .banned-tile-name { margin: 0 0 8px; overflow-wrap: anywhere; }
    .banned-add-form { margin-bottom: 20px; }
    .banned-add-form summary { display: inline-block; cursor: pointer; }
    .banned-add-form[open] summary { margin-bottom: 12px; }
');
?>
<div class="banned-index">
    <h2><?= Html::encode($this->title) ?></h2>
    <?php if (Yii::$app->controller->hasPermission('accounts/add-banned-guest')): ?>
        <details class="banned-add-form" <?= $bannedGuest->hasErrors() ? 'open' : '' ?>>
            <summary class="btn btn-success">Add Banned Guest</summary>
            <?php $form = ActiveForm::begin([
                'action' => ['/accounts/add-banned-guest'],
                'method' => 'post',
            ]); ?>
            <?= $form->errorSummary($bannedGuest) ?>
            <div class="row">
                <div class="col-xs-12 col-sm-4">
                    <?= $form->field($bannedGuest, 'bg_first_name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-xs-12 col-sm-4">
                    <?= $form->field($bannedGuest, 'bg_last_name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-xs-12 col-sm-4">
                    <?= Html::submitButton('Add Guest', ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </details>
    <?php endif; ?>
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '_banned-tile',
        'itemOptions' => ['tag' => false],
        'options' => ['class' => 'row banned-tiles'],
        'layout' => "{items}\n<div class=\"col-xs-12\">{pager}</div>",
    ]) ?>
</div>
