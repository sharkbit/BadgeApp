<?php

use backend\controllers\PaymentController;
use backend\models\StoreItems;

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\ClubsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

class inventory extends StoreItems {
    public $Id;
	public $Name;
	public $FullyQualifiedName;
	public $UnitPrice;
	public $Taxable;
	public $SalesTaxIncluded;

    public function rules() {
        return [
            [['Id'], 'number'],
			[['Name','FullyQualifiedName','UnitPrice','Taxable','SalesTaxIncluded'], 'string']
		];
	}
}


$this->title = 'QuickBooks Items';
$this->params['breadcrumbs'][] = ['label' => 'Store', 'url' => ['/sales']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/sales/qb-items']];


$inven = PaymentController::GetInventory();
foreach ($inven as $item ) {
yii::$app->controller->createLog(false, 'trex_V_S_QB', gettype($item));
yii::$app->controller->createLog(false, 'trex_V_S_QB', var_export($item,true));
  //  echo $item ;
  exit;
}

yii::$app->controller->createLog(false, 'trex_V_S_QB', gettype($dataProvider));
//

echo $this->render('_view-tab-menu').PHP_EOL ?>


<div class="sales-qbitems">
    <div class="row">
        <div class="col-xs-12">
            <h2><?= Html::encode($this->title) ?></h2>

            <div class="btn btn-group pull-right"> 
                <?= Html::a('Create Club', ['create'], ['class' => 'btn btn-success']) ?> 
            </div >
            
            <?php //Pjax::begin(); ?>    
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                //  'filterModel' => $searchModel,
                'columns' => [
                    'Id',
                    'Name',
                    'FullyQualifiedName',
					'UnitPrice',
					'Taxable',
					'SalesTaxIncluded',
                    [
                        'header'=>'Action',
                        'class' => 'yii\grid\ActionColumn'
                    ],
                ],
            ]); ?>
            <?php //Pjax::end(); ?>
        </div>
    </div>
</div>