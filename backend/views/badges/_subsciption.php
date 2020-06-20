<?php 

use yii\helpers\Html;
use backend\models\BadgeSubscriptions;

$subciptionsArray = BadgeSubscriptions::findOne($model->badge_subscription_id);
//echo'<pre>'; print_r($subciptionsArray); 
?>

<div class="col-xs-12 col-sm-8">
    <div class="item panel panel-default"><!-- widgetBody -->
        <div class="panel-heading">
            <h3 class="panel-title pull-left"> Subscriptions Details</h3>
            <div class="pull-right">
                <div class="btn-group pull-right">
                    <?= Html::a('Renew Membership', ['/badges/renew-membership', 'membership_id' =>$model->badge_number], [
                    'class' => 'btn btn-primary',
                    'data' => [
                        'confirm' => 'Are you sure you want to Renew Membership',
                        'method' => 'post',
                    ],
               		]) ?>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
           <div class="clients-view">
                <table id="w0" class="table table-striped table-bordered detail-view">
                	<tbody>
                		<tr>
							<th>Badge Number</th>
							<td><?=$subciptionsArray->badge_number?></td>
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
							<td> <?=money_format('%i', $subciptionsArray->badge_fee);?> </td>
						</tr>
						<tr>
							<th>Discount ( if any ) </th>
						<td> <?=money_format('%i', $subciptionsArray->discount);?> </td>
						</tr>
						<tr>
							<th>Paid Amount</th>
							<td> <?=money_format('%i', $subciptionsArray->paid_amount);?> </td>
						</tr>
					</tbody>
				</table>
            </div>
        </div>
    </div>
</div>