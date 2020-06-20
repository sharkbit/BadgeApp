<?php 

use backend\models\FeesStructure;

$certificationDetailsModel = FeesStructure::findOne($certificationModel->certification_type);

$this->title = 'Certification - '.$certificationDetailsModel->label;
$this->params['breadcrumbs'][] = ['label' => 'Range Badges', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $badgeModel->badge_number, 'url' => ['/badges/view','badge_number'=>$badgeModel->badge_number]];
$this->params['breadcrumbs'][] = ['label' => 'Certifications', 'url' => ['/badges/view-certifications-list','badge_number'=>$badgeModel->badge_number]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="certification-box">
          <ul> 
              <li> Badge Number <span class="pull-right"> <?=$badgeModel->badge_number?>  </span> </li>
              <li> Name <span class="pull-right"> <?= $badgeModel->prefix.' '.$badgeModel->first_name.' '.$badgeModel->last_name.' '.$badgeModel->suffix ?>  </span> </li>
              <li> Certifcation Label <span class="pull-right"> <?= $certificationDetailsModel->label ?>  </span> </li>
              <li> Sticker <span class="pull-right"> <?= $certificationModel->sticker ?>  </span> </li>
              <li> Fee <span class="pull-right"> <?=  money_format('$%i', $certificationModel->fee)  ?>  </span> </li>
              <li> Discount <span class="pull-right"> <?= money_format('$%i',$certificationModel->discount)  ?>  </span> </li>
              <li> Amount Paid <span class="pull-right"> <?= money_format('$%i',  $certificationModel->amount_due)  ?>  </span> </li>
              <li> Issued on <span class="pull-right"> <?= date('M d, Y h:i A',strtotime($certificationModel->created_at))?>  </span> </li>
              <li> Certification Status <span class="pull-right"> 
                <?php if($certificationModel->status=='0') echo'Active'; else if($certificationModel->status=='1') echo 'Suspended'; else if($certificationModel->status=='2') echo "Revoked"; ?>  </span> </li>
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