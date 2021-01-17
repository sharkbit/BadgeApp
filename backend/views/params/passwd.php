<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Params */

$this->title = 'Update Remote User Password';
//$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['site/admin-menu']];
$this->params['breadcrumbs'][] = ['label' => $this->title, ];
?>

<div class="pwd-form">
<?php $form = ActiveForm::begin(['id'=>'paramsform']); ?>

    <h3><?= Html::encode($this->title) ?></h3>
<br />
<p>Please Note your username is case sensitive:  <b><?=$_SESSION['r_user'] ?></b></p>

<div class="row">
	<div class="col-xs-12 col-sm-6">
		<?= $form->field($model, 'pwd')->passwordInput() ?>
	</div>

</div>
<div class="row">
	<div class="col-xs-12 col-sm-6">
		<?= $form->field($model, 'pwd2')->passwordInput() ?>
	</div>
</div>
<div class="row">
	<div class="col-xs-6 col-sm-3">
		<p> </p>
		<?= Html::checkbox('reveal-password', false, ['id' => 'reveal-password']) ?> <?= Html::label('Show password', 'reveal-password') ?>
	</div>
	<div class="col-xs-6 col-sm-3">
		<?= Html::submitButton('Update', ['class' => 'btn btn-primary pull-right']) ?>
	</div>
</div>
<?php
$this->registerJs("jQuery('#reveal-password').change(function(){console.log('yesss');jQuery('#paramsform-pwd').attr('type',this.checked?'text':'password');})");
?>
<?php ActiveForm::end(); ?>
</div>
