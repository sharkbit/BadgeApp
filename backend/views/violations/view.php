<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\Violations;
use backend\models\clubs;
use backend\models\RuleList;

/* @var $this yii\web\View */
/* @var $model backend\models\BadgesDatabase */

$this->title = "View: ".$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Violations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo $this->render('_view-tab-menu').PHP_EOL ?>
<div class="violations-view">

    <h3><?= Html::encode($this->title) ?></h3>

    <p>
<?php if(yii::$app->controller->hasPermission('violations/update')) {
		echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']).PHP_EOL; }
      if(yii::$app->controller->hasPermission('violations/delete')) {
		echo Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]).PHP_EOL; } ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
			'vi_date',
			'vi_type',
			[
				'attribute' => 'vi_overrride',
				'value' => function($model) {
					if($model->vi_override) { return 'Yes'; } else { return 'No'; }; 
				}
			],
			[
				'attribute' => 'badge_reporter',
				'value' => function($model) {
					return yii::$app->controller->decodeBadgeName((int)$model->badge_reporter).' ('.$model->badge_reporter.')';
				},
			],
			[
				'attribute' => 'badge_involved',
				'format' => 'raw',
				'value' => function($model) {
					return yii::$app->controller->decodeBadgeName((int)$model->badge_involved).' (<a href="/badges/update?badge_number='.$model->badge_involved.'">'.$model->badge_involved.'</a>)';
				},
			],
			[
				'attribute' => 'club',
				'value' => function($model) {
					return (new clubs)->getMyClubsNames($model->badge_involved);
				},
			],
			
			[
				'attribute' => 'vi_loc',
				'value'=> function($model, $attribute) {
					return $model->getLocations($model->vi_loc);
				},
			],
			[
				'attribute' => 'was_guest',
				'value' => function($model) {
					if($model->was_guest) { return 'Yes'; } else { return 'No'; }; 
				}
			],
			[
				'attribute' => 'badge_witness',
				'value' => function($model) { if($model->badge_witness > 0) {
					return yii::$app->controller->decodeBadgeName((int)$model->badge_witness).' ('.$model->badge_witness.')';
				} else {return 'None'; } },
			],
			'vi_sum',
			[
				'attribute' => 'vi_rules',
				'value'=> function($model, $attribute) {
					return (new RuleList)->getRuleNames($model->vi_rules);
				},
			],
			'vi_report',
			'vi_action',
			'hear_date',
			'hear_sum'
        ],
    ]) ?>

</div>