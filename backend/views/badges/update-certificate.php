<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;

yii::$app->controller->createLog(true, 'trex_WTF', 'B_V_B_update_cert Still needed');

$this->title = 'Certification - ?';
$this->params['breadcrumbs'][] = ['label' => 'Badges', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $badgeModel->badge_number, 'url' => ['/badges/view','badge_number'=>$badgeModel->badge_number]];
$this->params['breadcrumbs'][] = ['label' => 'Certifications', 'url' => ['/badges/view-certifications-list','badge_number'=>$badgeModel->badge_number]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-xs-12">
        <div class="certification-box">
          <ul> 
              <?php $form = ActiveForm::begin(); ?>
              <li> Badge Number <span class="pull-right"> <?=$badgeModel->badge_number?>  </span> </li>
              <li> Name <span class="pull-right"> <?= $badgeModel->prefix.' '.$badgeModel->first_name.' '.$badgeModel->last_name.' '.$badgeModel->suffix ?>  </span> </li>
              <li> Email <span class="pull-right"> <?= $badgeModel->email ?>  </span> </li>
              <li> Sticker <span class="pull-right"> <?= $certificationModel->sticker ?>  </span> </li>
              <li> Fee <span class="pull-right"> <?=  money_format('$%i', $certificationModel->fee)  ?>  </span> </li>
              <li> Discount <span class="pull-right"> <?= money_format('$%i',$certificationModel->discount)  ?>  </span> </li>
              <li> Amount Paid <span class="pull-right"> <?= money_format('$%i',  $certificationModel->amount_due)  ?>  </span> </li>
              <li> Issued on <span class="pull-right"> <?= date('M d, Y h:i A',strtotime($certificationModel->created_at))?>  </span> </li>
              <li> Updated at <span class="pull-right"> <?= date('M d, Y h:i A',strtotime($certificationModel->updated_at))?>  </span> </li>
              <?= $form->field($certificationModel, 'status')->dropdownList(['0'=>'Active','1'=>'Suspended','2'=>'Revoked'],['prompt'=>'status']) ?>
              
              <div class="form-group">
                    <?= Html::submitButton( '<i class="fa fa-plus-square" aria-hidden="true"></i> Change Certifcation', ['class' => 'btn btn-primary pull-right']) ?>
                </div>
              <?php ActiveForm::end(); ?>

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