<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\money\MaskMoney;
/* @var $this yii\web\View */
/* @var $model backend\models\MembershipType */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="membership-type-form" ng-controller="MembershipTypeForm">

    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => false,
        'enableClientValidation' => true,
        'id' => 'ajax'
        ]); ?>
<div class="row">
    <div class="col-xs-6 col-sm-6">
		<?= $form->field($model, 'id')->textInput(['readonly'=>$model->isNewRecord ? false:true,'maxlength' => true]) ?>
	</div>
	<div class="col-xs-6 col-sm-6">
		<?= $form->field($model, 'type')->textInput()?>
	</div>
	<div class="col-xs-6 col-sm-6">
	<?= $form->field($model, 'sku_full')->textInput(['maxlength' => true]) ?>
	</div>
	<div class="col-xs-6 col-sm-6"><br >
<?php if((!$model->isNewRecord) && (isset($model->fullprice->item_id))) { 
		$h_price = '$ '.$model->fullprice->price; $lnk = "update?id=".$model->fullprice->item_id;
		} else { $h_price=''; $lnk='stock'; } ?>
		<b><?=$h_price?></b> <p>Prices can be edited on the <a href="/sales/<?=$lnk?>">Store Stock page </a> <br></p>
		<br /> <p class="help-block Top_space_qr_block"></p>
 	</div>
	<div class="col-xs-6 col-sm-6">
	<?= $form->field($model, 'sku_half')->textInput(['maxlength' => true]) ?>
	</div>
	<div class="col-xs-6 col-sm-6"><br >
	<?php if((!$model->isNewRecord) && (isset($model->halfprice->item_id))) { 
		$h_price = '$ '.$model->halfprice->price; $lnk = "update?id=".$model->halfprice->item_id;
		} else { $h_price=''; $lnk='stock'; } ?>
		<b><?=$h_price?></b> <p>Prices can be edited on the <a href="/sales/<?=$lnk?>">Store Stock page </a> <br></p>
		<br /> <p class="help-block Top_space_qr_block"></p>
	</div>
	<div class="col-xs-6 col-sm-6">
    <?= $form->field($model, 'status')->dropDownList([ '1'=>'Active', '0'=>'Inactive', ],['value'=>$model->isNewRecord ? 1: $model->status]) ?>
	</div>
	<div class="col-xs-6 col-sm-6">
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset <i class="fa fa-eraser"> </i>', ['class' => 'btn btn-danger']) ?>
    </div>
	</div>
</div>
    <?php ActiveForm::end(); ?>

</div>
