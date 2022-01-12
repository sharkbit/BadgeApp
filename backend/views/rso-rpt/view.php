<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Params */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'View RSO Report #'.$model->id;
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['rso-rpt/update']];

?>

<?=$this->render('_view-tab-menu').PHP_EOL ?>


<div class="col-xs-12 col-sm-8">
	<div class="block-badge-view">

	   <?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			[	'attribute' => 'rso',
				'value' => function($model) {
					$rsos=json_decode($model->rso);
					$names='';
					foreach ($rsos as $badge) {
						$names .= yii::$app->controller->decodeBadgeName((int)$badge).', ';
					}
					return $names;
				},
			],
			'date_open',
			[	'attribute'=>	'date_close',
				'visible' => ($model->closed==1) ? true : false,
			],
			'cash_bos',
			'cash_eos',
			'wb_trap_cases',
			'wb_color',
			'mics',
			'notes',
			'shift',
			'shift_anom',
			'closing',
			'par_50','par_100','par_200','par_steel','par_nm_hq','par_m_hq','par_trap','par_arch','par_pel','par_spr','par_cio_stu','par_act',
			[	'attribute'=>'remarks',
				'format'=>'raw',
				'value' => function($model) {
					$remarks=json_decode($model->remarks);
					$remark='';
					foreach ($remarks as $item) {
						$remark .= $item->created_at.' - '.$item->changed.' - '.$item->data."<br /> \n";
					}
					return $remark;
				},
				'visible' => (yii::$app->controller->hasPermission('rso-rpt/remarks')) ? true : false,
			],
		], ]); ?>
	</div>
</div>

