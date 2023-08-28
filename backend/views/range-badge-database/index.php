<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\Badges;
use backend\models\clubs;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\BadgesDatabaseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Badge Databases';
$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']];
$this->params['breadcrumbs'][] = $this->title;

if (isset($_REQUEST['BadgesDatabaseSearch']['pagesize'])) { 
	$pagesize = $_REQUEST['BadgesDatabaseSearch']['pagesize']; 
	$_SESSION['pagesize'] = $_REQUEST['BadgesDatabaseSearch']['pagesize'];
} elseif (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=20;
}
$dataProvider->pagination = ['pageSize' => $pagesize];
?>
<div class="badges-database-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
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
				'contentOptions' =>['style' => 'width:80px'],
				'format' => 'raw',
				'value'=>function ($data) {
					return Html::a(str_pad($data->badge_number, 5, '0', STR_PAD_LEFT),'/range-badge-database/update?badge_number='.$data->badge_number);
				}
			],
            [
				'attribute'=>'prefix',
				'contentOptions' =>['style' => 'width:10px'],
			],
            [
				'attribute'=>'first_name',
				'contentOptions' =>['style' => 'width:120px'],
			],
			[
				'attribute'=>'last_name',
				'contentOptions' =>['style' => 'width:150px'],
			],
			//'suffix',
            //'address:ntext',
            [
				'attribute'=>'city',
				'contentOptions' =>['style' => 'width:150px'],
			],
            [
				'attribute'=>'state',
				'contentOptions' =>['style' => 'width:10px'],
			],
            [
				'attribute'=>'zip',
				'contentOptions' =>['style' => 'width:80px'],
			],
            //'gender',
            [
				'attribute'=>'yob',
				'contentOptions' =>['style' => 'width:20px'],
			],
            [
				'attribute'=>'email',
				'contentOptions' =>['style' => 'width:180px'],
			],
            [
				'attribute'=>'phone',
				'contentOptions' =>['style' => 'width:130px'],
				'value'=> function($model, $attribute) {
					if ($model->phone) {$myPhone="(".substr($model->phone,0,3).") ".substr($model->phone,3,3)." - ".substr($model->phone,6,4);} else { $myPhone='';}
					return $myPhone;
				},
			],
            // 'phone_op',
            // 'ice_contact',
            // 'ice_phone',
            // 'club_name',
            // 'club_id',
			[	'attribute' => 'club_id',
				'format' => 'raw',
				'contentOptions' =>['style' => 'width:100px; overflow: auto; word-wrap: break-word; white-space: normal;'],
				'value'=> function($searchModel, $attribute) {
					$myClubsNames='';
					foreach($searchModel->clubView as $club){
						$myClubsNames .= $club['short_name'].' <img src="/images/note.png" title="'.$club['club_name'].'" style="width:18px" />, ';
					}
					return rtrim($myClubsNames, ', ');
				},
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'club_id',(new clubs)->getClubList(),['class'=>'form-control','prompt' => 'All']),
				
			],
            // 'mem_type',
            // 'primary',
            // 'incep',
            // 'expires',
            // 'qrcode:ntext',
            // 'wt_date',
            // 'wt_instru',
            // 'remarks:ntext',
			[
				'header'=>'Status',
				'attribute' => 'status',
				'contentOptions' =>['style' => 'width:90px'],
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'status',(new Badges)->getMemberStatus(),['class'=>'form-control','prompt' => 'All']),
				'value'=>function($model,$attribute) {
					return (new Badges)->getMemberStatus($model->status);}
			],
            // 'soft_delete',
            // 'created_at',
            // 'updated_at',
            [   'header' => 'Action',
                'class' => 'yii\grid\ActionColumn',
				'contentOptions' =>['style' => 'width:60px'],
				'template' => '{view} {update}',
				'buttons'=>[
					'update' => function ($url, $model) {
						return  Html::a(' <span class="glyphicon glyphicon-pencil"></span> ', ['update','badge_number'=>$model->badge_number], [
							'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'Edit',
							'class'=>'edit_item',
						]);
					},
					'view' => function($url,$model) {
						return  Html::a(' <span class="glyphicon glyphicon-eye-open"></span> ', ['view','badge_number'=>$model->badge_number], [
							'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'View',
						]);
					},
				]

            ],
        ],
    ]); ?>
</div>
