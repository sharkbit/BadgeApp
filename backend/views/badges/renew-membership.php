<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\MembershipType;
use backend\models\FeesStructure;
use kartik\money\MaskMoney;

$this->title = 'Renew Membership';
$this->params['breadcrumbs'][] = ['label' => 'Badges', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $badgeRecords->badge_number, 'url' => ['/badges/view-subscriptions','badge_number'=>$badgeRecords->badge_number,]];
$this->params['breadcrumbs'][] = $this->title;

/* @var $this yii\web\View */
/* @var $model backend\models\User */
/* @var $form yii\widgets\ActiveForm */

$memTypeArray = MembershipType::findOne($badgeRecords->mem_type);
$feeStructure = FeesStructure::find()->where(['membership_id'=>$badgeRecords->mem_type,'status'=>'0'])->one();
//echo'<pre>'; print_r($feeStructure); die();
$expireDate = date('Y-m-d', strtotime("+1 years",strtotime($badgeRecords->expires)));
//echo'<pre>'; print_r($date); die();


if($badgeRecords->work_credits>0) {
	$workcreditper = $feeStructure->fee / 40;
	if($badgeRecords->work_credits>40) {
		$discount = $workcreditper * 40;
		$redeemableCredit = 40;

	}
	else {
		$discount = $workcreditper * $badgeRecords->work_credits;
		$redeemableCredit = $badgeRecords->work_credits;
	}
}
else {
	$discount = 0.00;
	$redeemableCredit = 0;
}


?>

<div class="user-form">

    <!-- <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'badge_number')->textInput(['value'=>$badgeRecords->badge_number,'readOnly'=>true]) ?>
    <?= $form->field($model, 'mem_id')->hiddenInput(['value'=>$badgeRecords->mem_type])->label(false); ?>
    <?= $form->field($model, 'mem_type')->textInput(['value'=>$memTypeArray->type,'readOnly'=>true]) ?>
    <?= $form->field($model, 'expires')->textInput(['value'=>date('M d, Y',strtotime($expireDate)),'readOnly'=>true]) ?>
     <?= $form->field($model, 'badge_fee')->widget(MaskMoney::classname(), [
                        'pluginOptions' => [
                        'allowNegative' => false,
                        ],
                        'value'=>$feeStructure->fee,

                    ]); ?>

    <?= $form->field($model, 'badge_fee')->textInput(['value'=>money_format('%i', $feeStructure->fee),'readOnly'=>true]) ?>


    <div class="form-group pull-right">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success pull-right' : 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?> -->

    <div class="row" ng-controller="RenewBadge">
    	<div class="col-xs-12">
    		<?php $form = ActiveForm::begin(); ?>
    		<table id="w0" class="table table-striped table-bordered detail-view">
                	<tbody>
                		<tr>
							<th>Badge Number</th>
							<td> <?= $badgeRecords->badge_number?> </td>
						</tr>
                		<tr>
							<th> Membership Type </th>
							<td> <?=$memTypeArray->type?> </td>
						</tr>
						<tr>
							<th>Expires Date</th>
							<td> <?=date('M d, Y',strtotime($expireDate)) ?> </td>
						</tr>
						<tr>
							<th>Badge Fee </th>
							<td> <?= money_format('$%i', $feeStructure->fee) ?> </td>
						</tr>
						<tr>
							<th>Fee Discount (if any) </th>
							<td id="tableDiscount"> <?= money_format('$%i', $discount) ?> </td>
						</tr>
						<tr>
							<th>Net Amount Due</th>
							<td id="tableNetAmountDue"> <?= money_format('$%i', $feeStructure->fee - $discount) ?> </td>
						</tr>
						
						
						
					</tbody>
				</table>
				<?= $form->field($model, 'badge_number')->hiddenInput(['value'=>$badgeRecords->badge_number,])->label(false) ?>
				<?= $form->field($model, 'mem_id')->hiddenInput(['value'=>$badgeRecords->mem_type])->label(false) ?>
				<?= $form->field($model, 'mem_type')->hiddenInput(['value'=>$memTypeArray->type])->label(false) ?>
				<?= $form->field($model, 'expires')->hiddenInput(['value'=>date('M d, Y',strtotime($expireDate))])->label(false)?>
				<?= $form->field($model, 'amount_due')->hiddenInput(['value'=>$paymentArray->fee])->label(false)?>
				<?= $form->field($model, 'redeemable_credit')->hiddenInput(['value'=>$redeemableCredit])->label(false) ?>
				<?= $form->field($model, 'badge_fee')->hiddenInput(['value'=>$paymentArray->fee])->label(false) ?>
				<?= $form->field($model, 'sticker')->textInput([]) ?>
				<?= $form->field($model, 'discount')->textInput(['value'=>$discount]) ?>
				<?= $form->field($model, 'payment_type')->dropdownList(
																		['cash'=>'Cash','check'=>'Check','credit'=>'Credit Card','online'=>'Online','other'=>'Other'],['prompt'=>'Payment Type'])?>


				<?= Html::submitButton('Renew Membership', ['class' => 'btn btn-primary pull-right']) ?>

			
			<?php ActiveForm::end(); ?>
    	</div>
    </div>




</div>
