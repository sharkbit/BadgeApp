<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\Privileges */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Club Roles';
$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/clubs/roles']];

if (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=20;
}
$dataProvider->pagination = ['pageSize' => $pagesize];
?>
<div class="privileges-index">
	<div class="row">
		<div class="col-xs-12">
			<h2><?= Html::encode($this->title) ?></h2>

			<div class="btn btn-group pull-right"> 
				<?= Html::a('Add Role', ['createrole'], ['class' => 'btn btn-success']) ?> 
			</div >
			
			<?php Pjax::begin(); ?>	
			<?= GridView::widget([
				'dataProvider' => $dataProvider,
				'filterModel' => $searchModel,
				'columns' => [
					[	'attribute'=>'id',
						'headerOptions' => ['style' => 'width:15%'],
					],
					'role',
					[	'header'=>'Action',
						'class' => 'yii\grid\ActionColumn',
						'template'=>' {update} {delete} ',
					],
				],
			]); ?>
			<?php Pjax::end(); ?>
		</div>
	</div>
</div>
