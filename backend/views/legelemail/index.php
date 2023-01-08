<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use backend\models\Legelemail;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\WorkCreditsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Legislative Contacts';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['legelemail/index']];

if (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=50;
}
$dataProvider->pagination = ['pageSize' => $pagesize];


//if(yii::$app->controller->hasPermission('Legelemail/delete')) {
	$myTemplate=' {update}  {delete} ';
//} elseif(yii::$app->controller->hasPermission('Legelemail/update')) {
//	$myTemplate=' {view}  {update} ';
//} else {$myTemplate='{view}';}
?>

<?=$this->render('_view-tab-menu').PHP_EOL ?>
<h1><?= Html::encode($this->title) ?></h1>
<div class="Legelemail-index">

<?php $form = ActiveForm::begin(['action' => ['legelemail/import'],'id'=>'GuestForm','options' => ['enctype' => 'multipart/form-data']]); ?>
<div class="row">
	<div class="btn btn-group pull-right"><?= Html::a('Create Contact', ['create'], ['class' => 'btn btn-success']).PHP_EOL;  ?></div>
</div>
<?php ActiveForm::end(); ?>

<div class="col-xs-12 col-md-12">

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'attribute' => 'last_name',
				'contentOptions' =>['style' => 'width:10%; overflow: auto; word-wrap: break-word; white-space: normal;'],
			],
			[
				'attribute' => 'first_name',
				'contentOptions' =>['style' => 'width:5%; overflow: auto; word-wrap: break-word; white-space: normal;'],
			],
			[
				'attribute' => 'email',
				'contentOptions' =>['style' => 'width:20%; font-stretch: condensed; overflow: auto; word-break: break-all; white-space: normal;'],
			],
			[
				'attribute' => 'title',
				'contentOptions' => function($model) {
					if($model->title !=='Delegate' && $model->title !=='Senator') {$fontsz="font-size: x-small;";} else {$fontsz="";}
					return ['style' => 'width:6%; text-align: center; overflow: auto; word-wrap: break-word; white-space: normal;'.$fontsz];},
			],
			[
				'attribute' => 'office',
				'contentOptions' =>['style' => 'width:7%; text-align: center; font-size: x-small; overflow: auto; word-wrap: break-word; white-space: normal;'],
			],
			[
				'attribute' => 'committee',
				'contentOptions' =>['style' => 'width:7%; text-align: center; white-space: normal;'],
			],
			[
				'attribute' => 'district',
				'contentOptions' =>function($model) {
					if(strlen($model->district) > 3) {$fontsz="font-size: x-small;";} else {$fontsz="";}
					return ['style' => 'width:6%; text-align: center; overflow: auto; word-wrap: break-word; white-space: normal;'.$fontsz];},
			],
			[
				'attribute' => 'groups',
				'format' => 'raw',
				'contentOptions' =>['style' => 'width:20%; font-stretch: condensed; overflow: auto; word-wrap: break-word; white-space: normal;'],
				'value'=> function($searchModel, $attribute) {
					return (new Legelemail)->getMyGroups($searchModel->contact_id, true);
					},
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'groups',(new Legelemail)->getGroupList(),['class'=>'form-control','prompt' => 'All']),
			],
		//	'date_modified',
			[	'attribute'=>'is_active',
				'contentOptions' =>['style' => 'width:6%; text-align: center; white-space: normal;'],
				'value'=>function($model) { if($model->is_active) {return "Yes";} else  {return "No";} },
		//		'headerOptions' => ['style' => 'width:5%;'],
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'is_active',['1'=>'Yes','0'=>'No'],['class'=>'form-control','prompt' => 'All', 'style' => 'padding-left: 5%; text-align: left;']),
		//		'filterOptions' =>['style' => 'text-align: left; font-size: x-small;'],
			],
			
			]
		]
	]); ?>
</div>
</div>
