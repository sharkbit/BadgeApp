<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\ActiveForm;
use backend\models\Badges;
use backend\models\clubs;

$badgesModel = new Badges();

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\BadgesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Range Badges';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];

if (isset($_REQUEST['BadgesSearch']['pagesize'])) { 
	$pagesize = $_REQUEST['BadgesSearch']['pagesize']; 
	$_SESSION['pagesize'] = $_REQUEST['BadgesSearch']['pagesize'];
} elseif (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=20;
}
$dataProvider->pagination = ['pageSize' => $pagesize];
?>

<?php $form = ActiveForm::begin([
	'action' => ['index'],
	'method' => 'post',
	'id'=>'viewPrintbadgeFilter',
]); ?>

<div class="row">
     <div class="col-xs-4 col-sm-4">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
	<?php if(yii::$app->controller->hasPermission('badges/all')) {
	echo $this->render('_search',['model'=>$searchModel,'badgesModel'=>$badgesModel,'form'=>$form]).PHP_EOL; } ?>
</div>
<div class="row">

    <div class="col-xs-12">
        <div class="btn-group pull-right">
            <?php
			if(yii::$app->controller->hasPermission('badges/create')) {
			echo Html::a('<i class="fa fa-plus-square" aria-hidden="true"></i> Create Badges', ['create'], ['class' => 'btn btn-success']); } ?>
        </div>

        <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => [
                'firstPageLabel' => 'First',
                'lastPageLabel'  => 'Last'
            ],
        'columns' => [
			[
				'attribute'=>'badge_number',
				'contentOptions' =>['style' => 'width:100px'],
				'format' => 'raw',
				'value'=>function ($data) {
					if(yii::$app->controller->hasPermission('badge/create')) {
						return Html::a(str_pad($data->badge_number, 5, '0', STR_PAD_LEFT),'/badges/update?badge_number='.$data->badge_number);
					} else {
						return Html::a(str_pad($data->badge_number, 5, '0', STR_PAD_LEFT),'/badges/view?badge_number='.$data->badge_number);
					}
				}
			],
            [
                'attribute'=>'mem_type',
				'contentOptions' =>['style' => 'width:120px'],
                'value' => function($model, $attribute) { return $model->membershipType->type; },
                'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'mem_type',$badgesModel->getMemberShipList(),['class'=>'form-control','prompt' => 'All']),
            ],
            'first_name',
            'last_name',
			[
				'attribute' => 'suffix',
				'contentOptions' =>['style' => 'width:20px'],
			],
			[ 
				'attribute' => 'club_id',
				'contentOptions' =>['style' => 'overflow: auto; word-wrap: break-word; white-space: normal;'],
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'club_id',(new clubs)->getClubList(),['class'=>'form-control','prompt' => 'All']),
				'format' => 'raw',
                //'value' => 'activeClub.club_name',
				'value'=>function($model) {
					return (new clubs)->getMyClubsNames($model->badge_number,true);
				}
            ],
			[
				'attribute' => 'status',
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'status',(new Badges)->getMemberStatus(),['class'=>'form-control','prompt' => 'All']),
				'value'=>function($model,$attribute) {
					return (new Badges)->getMemberStatus($model->status);}
			],
            [
                'attribute'=>'expires',
				// 'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'expire_condition',['all'=>'All','active'=>'Active','active+2'=>'Active +2','expired<2'=>'Expired <2','expired>2'=>'Expired >2','inactive'=>'Inactive'],['value'=>$searchModel->expire_condition !=null ? $searchModel->expire_condition : 'active+2','class'=>'form-control']),
				'value' => function($model, $attribute) {
                    return date('M d, Y',strtotime($model->expires));
                },
            ],
            [
                'header' => 'Actions',
                'class' => 'yii\grid\ActionColumn',
				'template'=>' {view} {update} {print} {delete} ',
				'buttons'=>[
					'update' => function ($url, $model) {
						if((yii::$app->controller->hasPermission('badges/modify')) || ($model->badge_number==$_SESSION['badge_number'])) {
						return  Html::a(' <span class="glyphicon glyphicon-pencil"></span> ', ['/badges/update','badge_number'=>$model->badge_number], [
							'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'Edit',
							'class'=>'edit_item',
						]); }
					},
					'view' => function($url,$model) {
						return  Html::a(' <span class="glyphicon glyphicon-eye-open"></span> ', ['/badges/view','badge_number'=>$model->badge_number], [
							'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'View',
						]);
					},
					'print' => function($url,$model) {
						if(yii::$app->controller->hasPermission('badges/print')) {
						if(file_exists("files/badge_photos/".str_pad($model->badge_number, 5, '0', STR_PAD_LEFT).".jpg")) {
						return  Html::a(' <span class="glyphicon glyphicon-print"></span> ', ['/badges/print','badge_number'=>$model->badge_number], [
							'target'=>'_blank',
							'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'Print',
						]); }}
					},
					'delete' => function($url,$model) {
						if(yii::$app->controller->hasPermission('badges/delete')) {
						return  Html::a(' <span class="glyphicon glyphicon-trash"></span> ', $url, [
							'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'Delete',
							'data' => [
								'confirm' => 'Are you sure you want to delete '.$model->first_name." ".$model->last_name.'?',
								'method' => 'post',
							],
						]); }
					},
				]
            ],
        ],
    ]); ?>
    </div>
</div>

<?php ActiveForm::end(); ?>
<div class="badges-index">

</div>
