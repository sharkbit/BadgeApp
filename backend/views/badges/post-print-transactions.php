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

if (isset($_REQUEST['PostPrintTransactionsSearch']['pagesize'])) {
	$pagesize = $_REQUEST['PostPrintTransactionsSearch']['pagesize'];
	$_SESSION['pagesize'] = $_REQUEST['PostPrintTransactionsSearch']['pagesize'];
} elseif (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=20;
}
$dataProvider->pagination = ['pageSize' => $pagesize];

$this->title = 'Print Transactions'; // - '.date('M d, Y',strtotime(yii::$app->controller->getNowTime()));
$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/badges/post-print-transactions']];
?>
<h2><?= Html::encode($this->title) ?></h2>
<div class="clubs-index" ng-controller="PostPrintTransactionForm">
	<div class="row">
	 <?php $form = ActiveForm::begin([
                'id'=>'postPrintTransactionForm',
                'action' => ['/badges/post-print-transactions'],
                'method' => 'get',
            ]); ?>
        <div class="col-xs-4">
            <?=html::a('<i class="fa fa-download" aria-hidden="true"></i> Export as CSV',['#'],['id'=>'customExportCsv','class'=>'btn btn-primary'])?>
        </div>
		<div class="col-xs-2" style="min-width:100px">
			<?= $form->field($searchModel, 'pagesize')->dropDownlist([ 20 => 20, 50 => 50, 100 => 100, 200=>200],['value'=>$pagesize ,'id' => 'pagesize'])->label('Page size: ') ?>
		</div>
        <div class="col-xs-4">

<?=  $form->field($searchModel, 'created_at', [
		'options'=>['class'=>'drp-container form-group']
		])->widget(DateRangePicker::classname(), [
			'presetDropdown'=>true,
			'hideInput'=>true,
			'pluginOptions' => [
				'opens'=>'left',
				'locale'=>['format'=>'MM/DD/YYYY'],
			]]); ?>
		</div>
		<div class="col-xs-2">
			<br />
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
							if ($searchModel->badge_number==99999) { return 'Cash Paymeny'; }
							else {
								$badgeArry = Badges::find()->where(['badge_number'=>$searchModel->badge_number])->one();
								if($badgeArry) {
									return $badgeArry->prefix.' '.$badgeArry->first_name.' '.$badgeArry->last_name.' '.$badgeArry->suffix;
								} else { return 'Admin'; }
							} 
                        },
                    ],
					'ClubNames',
                    [
                        'header'=>'Fee',
                        'value'=>function($searchModel) {
							$formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
							return $formatter->formatCurrency($searchModel->fee, 'USD');
                        },
                        'contentOptions' => ['class' => 'text-right'],
                    ],
                    [
                        'header'=>'Paid Amount',
                        'value'=>function($searchModel) {
							$formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
							return $formatter->formatCurrency($searchModel->paid_amount, 'USD');
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
