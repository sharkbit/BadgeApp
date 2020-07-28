<?php

use backend\models\Badges;
use backend\models\PostPrintTransactions;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\PostPrintTransactionsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Print Transactions'; // - '.date('M d, Y',strtotime(yii::$app->controller->getNowTime()));
$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/badges/post-print-transactions']];
?>
<h2><?= Html::encode($this->title) ?></h2>
<div class="clubs-index" ng-controller="PostPrintTransactionForm">
	<div class="row">
         <div class="col-xs-5">
            <?=html::a('<i class="fa fa-download" aria-hidden="true"></i> Export as CSV',['#'],['id'=>'customExportCsv','class'=>'btn btn-primary'])?>
        </div>
        
        <div class="col-xs-5">
             <?php $form = ActiveForm::begin([
                'id'=>'postPrintTransactionForm',
                'action' => ['/badges/post-print-transactions'],
                'method' => 'get',
            ]); ?>
			
<?=  $form->field($searchModel, 'created_at', [
		'options'=>['class'=>'drp-container form-group']
		])->widget(DateRangePicker::classname(), [
			'presetDropdown'=>true,
			'hideInput'=>true,
			'pluginOptions' => [
				'opens'=>'left',
				'locale'=>['format'=>'MM/DD/YYYY'],
			]])->label(false); ?>
		</div>
		<div class="col-xs-2">
			<?= Html::submitButton('<i class="fa fa-search pull-right" aria-hidden="true"></i> Search', ['class' => 'btn btn-primary']) ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <div class="row"><div class="col-sm-6">
	 <?= (new PostPrintTransactions)->getPPTSum($searchModel->created_at) ?>
	</div></div>
    <div class="row">
        <div class="col-xs-12">
            <?php 

                $gridColumns = [
                    [
						'header'=>'Date',
						'value' => function($searchModel) {
							return date('Y-m-d',strtotime($searchModel->created_at));}
					],
                    [
                        'header'=>'Badge Number',
                        'value' => 'badge_number',
                        'contentOptions' => ['class' => 'text-left'],
                    ],
                    [
                        'header'=>'Transaction Type',
                        'value' => 'transaction_type',
                    ],
                    [
                        'header'=> 'Name',
                        'value' => function($searchModel) {
                            $badgeArry = Badges::find()->where(['badge_number'=>$searchModel->badge_number])->one();
                            return $badgeArry->prefix.' '.$badgeArry->first_name.' '.$badgeArry->last_name.' '.$badgeArry->suffix;
                        },
                    ],
                    [
                        'header'=>'Club',
                        'value'=>'clubDetails.club_name',
                    ],
                    [
                        'header'=>'Fee',
                        'value'=>function($searchModel) {
                             return money_format('$%i', $searchModel->fee);
                        },
                        'contentOptions' => ['class' => 'text-right'],
                    ],
                    [
                        'header'=>'Paid Amount',
                        'value'=>function($searchModel) {
                             return money_format('$%i', $searchModel->paid_amount);
                        },
                        'contentOptions' => ['class' => 'text-right'],
                    ],
                 ];

                echo ExportMenu::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => $gridColumns,
                    'fontAwesome' => true,
                    'batchSize' => 10,
                    'filename'=>  $this->title,
                    'target' => '_blank',
                    'folder' => '@webroot/export', // this is default save folder on server
                ]) . "<hr>\n".
                GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => $gridColumns,
                ]);
                     
            ?>
        </div>
       
    </div>
</div>
