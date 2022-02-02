<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\RsoReports;

/* @var $this yii\web\View */
/* @var $model backend\models\Params */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'View RSO Report for '.$model->date_open.' Shift: '.$model->shift;
$this->params['breadcrumbs'][] = ['label' => 'RSO Reports', 'url' => ['rso-rpt/index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['rso-rpt/view?id='.$model->id]];

$rpt_pre = RsoReports::find()->where(['<','date_open',$model->date_open])->orderBy(['date_open'=>SORT_DESC])->one();
$rpt_nxt = RsoReports::find()->where(['>','date_open',$model->date_open])->orderBy(['date_open'=>SORT_ASC])->one();
?>

<?=$this->render('_view-tab-menu').PHP_EOL ?>

<div class="row ">
	<div class="col-xs-6 ">
	<?php if($rpt_pre) {
		echo Html::a('<i class="fa fa-refresh"> </i> Prev: '.$rpt_pre->date_open,['/rso-rpt/view?id='.$rpt_pre->id],['class' => 'btn btn-info']); 
	}  else {
		echo Html::a(' First ','',['class' => 'btn btn-warning']); 
	} ?>
	</div>
	<div class="col-xs-6 ">
	<?php if($rpt_nxt) {
		echo Html::a('<i class="fa fa-refresh"> </i> Next: '.$rpt_nxt->date_open,['/rso-rpt/view?id='.$rpt_nxt->id],['class' => 'btn btn-info']); 
	} else {
		echo Html::a(' Last ','',['class' => 'btn btn-warning']); 
	} ?>
	</div>
</div><br />
<div class="col-xs-12 col-sm-8">
	<div class="block-badge-view">

	   <?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			[	'attribute' => 'rso',
				'value' => function($model) {
					$rsos=json_decode($model->rso);
					$names='';
					if($rsos) {
						foreach ($rsos as $badge) {
							$names .= yii::$app->controller->decodeBadgeName((int)$badge).', ';
						}
						return $names;
					} else { return ""; } 
				},
			],
			'date_open',
			[	'attribute'=>'shift',
				'value' => function($model) { if($model->shift=='m') {return 'Morning';} else {return 'Evening';} }, ],
			[	'attribute'=>	'date_close',
				'visible' => ($model->closed==1) ? true : false,
			],
			'cash_bos',
			'cash_eos',
			'wb_trap_cases',
			[	'attribute'=>'wb_color',
				'value' => function($model) {
				  switch ($model->wb_color){
					case 'g': return 'Green';
					case 'b': return 'Blue';
					case 'r': return 'Red';
					case 'l': return 'Lavender';
					case 'k': return 'Black';
				  }
				}
			],
			[	'attribute'=>'mics',
				'value' => function($model) {
				  switch ($model->mics){
					case 'o': return 'Mics Set Out';
					case 's': return 'Mics stored in closet';
					case 't': return 'Mics in Trap 3';
				  }
				}
			],
			'notes',
			'shift_anom',
			'closing',
			'stickers',
			'cash',
			'checks',
			'violations',
			'par_50','par_100','par_200','par_steel','par_nm_hq','par_m_hq','par_trap','par_arch','par_pel','par_spr','par_cio_stu','par_act',
			[	'attribute'=>'remarks',
				'format'=>'raw',
				'value' => function($model) {
					$remarks=json_decode($model->remarks);
					$remark='';
					if($remarks) {
						foreach ($remarks as $item) {
							$remark .= $item->created_at.' - '.$item->changed.' - '.$item->data."<br /> \n";
						}
						return $remark;
					} else { return ""; } 
				},
				'visible' => (yii::$app->controller->hasPermission('rso-rpt/remarks')) ? true : false,
			],
		], ]); ?>
	</div>
</div>

