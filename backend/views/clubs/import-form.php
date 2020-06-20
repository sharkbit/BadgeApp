<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model backend\models\Clubs 
*/
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Import Club Names';
$this->params['breadcrumbs'][] = ['label' => 'Admin Function', 'url' => ['/badge/admin-function']];
$this->params['breadcrumbs'][] = ['label' => 'Member Club List', 'url' => ['/clubs/index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <idv class="col-xs-12" id="uploadingInfo" style="display: none;">
        <h4> <img src="<?=yii::$app->params['rootUrl']?>/images/animation_processing.gif" style="width: 100px;">  Please wait while the Data is being imported.</h4>
    </idv>

    <div class="row sucessbox">
    </div>
</div>

 <div class="row" ng-controller="ImportClubs">
  <div class="col-sm-12">
   <form name='fileUpload' id="uploadFile" method="post" action="" enctype="multipart/form-data">
   	<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
   	<div class="form-group">
   		<label> Import Clubs</label>
   		<input type="file" name="file" class="form-control">
   	</div>
    <div class="form-group">
    	<input type="submit" value="Import"  class="btn btn-primary pull-right" />
    </div>

    <p> <a href="<?=yii::$app->params['rootUrl']?>/sample/clubname_sample.xls" targe="_blank"> Download Sample </a> </p>
    
   </form>
  </div>
 </div>

