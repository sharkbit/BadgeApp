<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Subscription - '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Badges', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->badge_number, 'url' => ['/badges/view','badge_number'=>$model->badge_number]];
$this->params['breadcrumbs'][] = ['label' => 'Renewal History', 'url' => ['/badges/view-renewal-history','badge_number'=>$model->badge_number]];
$this->params['breadcrumbs'][] = $this->title;

$startYear = date('Y')+3;
$endYear = date('Y')-3;
$years = [];
for ($year = $startYear; $year >= $endYear; $year--) {
    $years[$year] = $year;
}
?>

<div class="row">
    <div class="col-xs-12">
        <div class="certification-box">
			<ul> 
			<?php $form = ActiveForm::begin(); ?>
			<li> <?=Html::label($model->getLabel('badge_number'))?> <span class="pull-right"> <?=$model->badge_number?>  </span> </li>
			<li> <?= $form->field($model, 'badge_year')->dropDownList($years,['value'=>$model->badge_year]); ?>
			<li> <?=Html::label($model->getLabel('transaction_type'))?> <span class="pull-right"> <?=$model->transaction_type?>  </span> </li>
			<li> <?php if($model->is_migrated) { 
				echo Html::label($model->getLabel('payment_type'))?> <span class="pull-right"> <?=$model->payment_type?>  </span>
			<?php } else { echo $form->field($model, 'payment_type')->dropdownList(['cash'=>'Cash','check'=>'Check',
						'credit'=>'Credit Card','online'=>'Online','other'=>'Other'],['prompt'=>'Payment Type']); } ?> </li>
			<li> <?=Html::label($model->getLabel('badge_fee'))?> <span class="pull-right"> <?=$model->badge_fee?>  </span> </li>
			<li> <?=Html::label($model->getLabel('paid_amount'))?> <span class="pull-right"> <?=$model->paid_amount?>  </span> </li>
			<li> <?=Html::label($model->getLabel('discount'))?> <span class="pull-right"> <?=$model->discount?>  </span> </li>
			<li> <?php if($model->is_migrated) { 
				 echo Html::label($model->getLabel('sticker'))?> <span class="pull-right"> <?=$model->sticker?>  </span>
			<?php } else { echo $form->field($model, 'sticker')->textInput(['value'=>$model->sticker]);  } ?> </li>
			<li> <?=Html::label($model->getLabel('created_at'))?> <span class="pull-right"> <?= date('M d, Y h:i A',strtotime($model->created_at))?>  </span> </li>
			              
			<div class="form-group">
				<?= Html::submitButton( '<i class="fa fa-plus-pencil" aria-hidden="true"></i> Update', ['class' => 'btn btn-primary pull-right']) ?>
			</div>
			<?php ActiveForm::end(); ?>
			</ul>
        </div>
    </div>
</div>


<style type="text/css">

  .certification-box ul li {
    list-style-type: none;
    padding: 6px 0px;
    font-size: 18px;
}

.certification-box {
    background: #ebecec;
    margin: 10px 289px;
    padding: 10px 76px 48px 2px;
}
</style>