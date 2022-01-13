<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Stickers */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Badge Stickers';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['rso-rpt/sticker']];

if (isset($_REQUEST['StickersSearch']['pagesize'])) { 
	$pagesize = $_REQUEST['StickersSearch']['pagesize']; 
	$_SESSION['pagesize'] = $_REQUEST['StickersSearch']['pagesize'];
} elseif (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=20;
}
$dataProvider->pagination = ['pageSize' => $pagesize];
?>

<?=$this->render('_view-tab-menu').PHP_EOL ?>
<div class="rsoreports-search">
<?php $form = ActiveForm::begin([
	'method' => 'post',
	'id'=>'RSOreportsFilter',
]); ?>

<div class="row">
	<div class="col-sm-6">
		<h1><?= Html::encode($this->title) ?></h1>
	</div>
    <div class="col-xs-3 col-sm-2" style="min-width:100px">
		<?= $form->field($searchModel, 'pagesize')->dropDownlist([ 20 => 20, 50 => 50, 100 => 100, 200=>200],['value'=>$pagesize ,'id' => 'pagesize'])->label('Page size: ') ?>
	</div>
	<div class="col-sm-2">
		<div class=" form-group btn-group ">
		<br>
			<?= Html::submitButton('<i class="fa fa-search" aria-hidden="true"></i> Search', ['class' => 'btn btn-primary']) ?>
			<?= Html::a('<i class="fa fa-eraser" aria-hidden="true"></i> Reset', ['/rso-rpt/sticker'],['class' => 'btn btn-danger']) ?>
		</div>
	</div>
<?php if (yii::$app->controller->hasPermission('sticker/move')) { ?>
	<div class="col-xs-1">
		<div class=" form-group btn-group ">
		<br>
			<?= Html::a('<i class="fas fa-arrows-alt-h" aria-hidden="true"></i> Move Stickers', '',['class' => 'btn btn-info', 'id'=>'java-show-move']) ?>
		</div>
	</div>
<?php } if (yii::$app->controller->hasPermission('sticker/add')) { ?>
	<div class="col-xs-1">
		<div class=" form-group btn-group ">
		<br>
			<?= Html::a('<i class="fas fa-plus" aria-hidden="true"></i> Add Stickers', '',['class' => 'btn btn-success', 'id'=>'java-show-add']) ?>
		</div>
	</div>
<?php } ?>
</div>

<div class="row" id="Move-Stickers" style="display: none" >
	<div class="col-xs-0 col-sm-3"> </div>
	<div class="col-sm-2">
		<h2 class='pull-right'>Move Stickers:</h2>
	</div>
	<div class="col-xs-2 col-sm-1">
		<?= $form->field($searchModel, 'to')->dropDownlist(['adm'=>'Admin','rso'=>'RSO','isu'=>'Issued'],['prompt'=>'select']); ?>
	</div>
	<div class="col-xs-4 col-sm-2">
		<?= $form->field($searchModel, 'stkrs')->textinput(['placeholder'=>'2,4,7-9']); ?>
	</div>
	</div>
	<div class="col-xs-1">
		<div class=" form-group btn-group ">
		<br>
			<?= Html::submitButton('<i class="fas fa-arrows-alt-h" aria-hidden="true"></i> move',['class' => 'btn btn-info','name' => 'sticker_move','value'=>1 ]); ?>
		</div>
	</div>
</div>

<div class="row" id="Add-Stickers" style="display: none" >
	<div class="col-xs-0 col-sm-3"> </div>
	<div class="col-sm-2">
		<h2 class='pull-right'>Add Stickers:</h2>
	</div>
	<div class="col-xs-2 col-sm-1">
		<?= $form->field($searchModel, 'yr')->dropDownlist(yii::$app->controller->getYear()); ?>
	</div>
	<div class="col-xs-2 col-sm-1">
		<?= $form->field($searchModel, 'start')->textinput(); ?>
	</div>
	<div class="col-xs-2 col-sm-1">
		<?= $form->field($searchModel, 'end')->textinput(); ?>
	</div>
	<div class="col-xs-1">
		<div class=" form-group btn-group ">
		<br>
			<?= Html::submitButton('<i class="fas fa-plus" aria-hidden="true"></i> Add', ['class' => 'btn btn-success','name' => 'sticker_add','value'=>1 ]); ?>
		</div>
	</div>
</div>

</div>
<?php ActiveForm::end(); ?>

<div class="row">
<div class="col-xs-12">

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[	'attribute'=>'s_id',	],
			[	'attribute'=>'sticker',	],
			[	'attribute'=>'status',	],
			[	'attribute'=>'holder',	],
			[	'attribute'=>'updated',	],
			],
		]); ?>
</div>
</div>

<script>
   $(document).ready(function () {
        var renewActionPermission = false;

        $("#java-show-move").click(function(e) {
            e.preventDefault();
			$("#Move-Stickers").show();
			$("#Add-Stickers").hide();
		});
		
		$("#java-show-add").click(function(e) {
            e.preventDefault();
			$("#Move-Stickers").hide();
			$("#Add-Stickers").show();
		});
   });
</script>
