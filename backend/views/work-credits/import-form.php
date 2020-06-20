<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Clubs */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Import Work Credits';
$this->params['breadcrumbs'][] = ['label' => 'Work Credits', 'url' => ['/work-credits/index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<?= $this->render('_view-tab-menu') ?>

<div class="row">
    <idv class="col-xs-12" id="uploadingInfo" style="display: none;">
        <h4> <img src="<?=yii::$app->params['rootUrl']?>/images/animation_processing.gif" style="width: 100px;"> Please wait while the Data is being imported. </h4>
    </idv>

    <div class="row sucessbox">
    </div>

    <idv class="col-xs-12" id="uploadingInfoError" style="display: none;">
      <div class="alert alert-danger alert-dismissable fade in">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Error!</strong> importing file size exceeded, Maximum importing records should be less than or equal to 2000
        <br><strong>Requested records count <span id="erroCount"> </span> </strong> 
      </div>
    </idv>
</div>

 <div class="row" ng-controller="ImportWorkCredits">
  <div class="col-sm-12">
   <form name='fileUpload' id="uploadFile" method="post" action="" enctype="multipart/form-data">
   	<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
   	<div class="form-group">
   		<input type="file" name="file" class="form-control">
   	</div>
    <div class="form-group">
    	<input type="submit" value="Import"  class="btn btn-primary pull-right" />
    </div>
    <p> <a href="<?=yii::$app->params['rootUrl']?>/sample/credits_sample.xls" targe="_blank"> Download Sample </a> </p>
   </form>
  </div>
 </div>

