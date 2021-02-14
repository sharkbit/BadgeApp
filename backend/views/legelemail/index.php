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
	$pagesize=20;
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
			'last_name',
			'first_name',
			'email',
			'title',
			[
				'attribute' => 'office',
				'contentOptions' =>['style' => 'width:10%'],
			],
			[
				'attribute' => 'committee',
				'contentOptions' =>['style' => 'width:5%'],
			],
			[
				'attribute' => 'district',
				'contentOptions' =>['style' => 'width:5%'],
			],
			[
				'attribute' => 'groups',
				'format' => 'raw',
				'contentOptions' =>['style' => 'width:40%; overflow: auto; word-wrap: break-word; white-space: normal;'],
				'value'=> function($searchModel, $attribute) {
					return (new Legelemail)->getMyGroups($searchModel->contact_id, true);
					},
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'groups',(new Legelemail)->getGroupList(),['class'=>'form-control','prompt' => 'All']),
			],
		//	'date_modified',
			[	'attribute'=>'is_active',
				'value'=>function($model) { if($model->is_active) {return "Yes";} else  {return "No";} },
				'headerOptions' => ['style' => 'width:5%'],
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'is_active',['1'=>'Yes','0'=>'No'],['class'=>'form-control','prompt' => 'All']),
			],
			[
				'header' => 'Actions',
				'class' => 'yii\grid\ActionColumn',
				'template'=>$myTemplate,
			/*	'buttons'=>[
					'view' => function ($url, $model) {
						return  Html::a(' <span class="glyphicon glyphicon-eye-open"></span>', ['view','id'=>$model->id],
						[	'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'View',
							'class'=>'edit_item',
						]);
					},
					'update' => function ($url, $model) {
						return  Html::a(' <span class="glyphicon glyphicon-pencil"></span>', ['update','id'=>$model->id],
						[	'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'Edit',
							'class'=>'edit_item',
						]);
					},
					'delete' => function($url,$model) {
						return  Html::a(' <span class="glyphicon glyphicon-trash"></span>', $url,
						[	'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'Delete',
							'data' => [
								'confirm' => 'Are you sure you want to delete this item?',
								'method' => 'post',
							],
						]);
					},

				] */
			]
		]
	]); ?>
</div>
</div>
