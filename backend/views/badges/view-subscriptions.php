<?php
use backend\models\CardReceipt;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $model backend\models\Badges */

$this->title = $subciptionsArray->badge_number;
$this->params['breadcrumbs'][] = ['label' => 'Range Badges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$urlStatus = yii::$app->controller->getCurrentUrl();
$formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);

$rcpt = CardReceipt::find()->where(['id'=>$subciptionsArray->cc_x_id,'badge_number'=>$subciptionsArray->badge_number])->one();
if(isset($rcpt->on_qb) && $rcpt->on_qb==0) {
	echo "<script> window.open('".yii::$app->params['rootUrl']."/badges/print-rcpt?x_id=".$subciptionsArray->cc_x_id."&badge_number=".
		$subciptionsArray->badge_number."','Recipt','fullscreen=no,titlebar=no,location=0,menubar=no,status=no,toolbar=no,width=500')".
		"</script>";
} ?>

<div class="badges-view">
    <div class="row" > 
        <div class="col-xs-12">
    <?= $this->render('_view-tab-menu',['model'=>$subciptionsArray]).PHP_EOL; ?>
            <h3> Subscriptions Details </h3>
            <div class="col-xs-12 col-sm-4 pull-right">

            </div>
            <div class="col-xs-12 col-sm-8">

            <div class="item panel panel-default"><!-- widgetBody -->
        <div class="panel-heading">
            <h3 class="panel-title pull-left"> Subscriptions Details</h3>
            <div class="pull-right">
<?php if($subciptionsArray->cc_x_id) {
		echo  Html::a('[ <span class="glyphicon glyphicon-print"> Print Recipt ]</span> ',
			['/badges/print-rcpt','x_id'=>$subciptionsArray->cc_x_id,"badge_number"=>$subciptionsArray->badge_number], [
			'target'=>'_blank',
			'data-toggle'=>'tooltip',
			'data-placement'=>'top',
			'title'=>'Print Recipt',
		]); }  ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
           <div class="clients-view">
                <table id="w0" class="table table-striped table-bordered detail-view">
                    <tbody>
                        <tr>
                            <th>Badge Number</th>
							<td><?=str_pad($subciptionsArray->badge_number, 5, '0', STR_PAD_LEFT)?></td>
                        </tr>
                        <tr>
                            <th>Transaction Type</th>
                            <td><?=ucfirst($subciptionsArray->transaction_type)?></td>
                        </tr>
                        <tr>
                            <th>Membership Status</th>
                            <td><?php 
                                if($subciptionsArray->status=='active') echo'<span class="label label-success"> '.ucfirst($subciptionsArray->status).' </span>';
                                else if ($subciptionsArray->status=='expired') echo'<span class="label label-warning">'.ucfirst($subciptionsArray->status).'</span>';
                             ?></td>
                        </tr>
                        <tr>
                            <th>Expire Date</th>
                            <td><?=date('M d, Y',strtotime($subciptionsArray->valid_true))?></td>
                        </tr>
                        <tr>
                            <th>Membership renewed on</th>
                            <td><?=date('M d, Y H:i A',strtotime($subciptionsArray->created_at))?></td>
                        </tr>
                        <tr>
                            <th>Payment Type</th>
                            <td><?=ucfirst($subciptionsArray->payment_type)?></td>
                        </tr>
                        <tr>
                            <th>Badge Fee</th>
                            <td> <?=$formatter->formatCurrency($subciptionsArray->badge_fee, 'USD');?> </td>
                        </tr>
                        <tr>
                            <th>Discount ( if any ) </th>
                            <td> <?=$formatter->formatCurrency($subciptionsArray->discount, 'USD');?> </td>
                        </tr>
                        <tr>
                            <th>Paid Amount</th>
                            <td> <?=$formatter->formatCurrency($subciptionsArray->paid_amount, 'USD');?> </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
            </div>
            <div class="col-xs-12 col-sm-8">
            </div>
        </div>
    </div>
</div>

<style>
.info-box-credit {
    width: 100%;
    float: left;
}
.info-box-icon {
    float: left; 
    width: 30%;

}
.aqua {
    background: #00aff0;
    color: #fff;
}
i.fa.fa-user {
    font-size: 60px !important;
}
.info-box-details .head{
    padding: 4px;
    font-size: 16px;
    color: #262626;
}
.info-box-details {

}
.info-box-credit {
    background: #eee;
    border: 1px solid #00aff0;
}
.info-box-details {
    margin-left: 122px;
}
.info-box-details h4 {
    font-size: 16px;
}
.info-box-details span {
    font-size: 26px;
}
</style>