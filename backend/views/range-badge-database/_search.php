<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\search\BadgesDatabaseSearch */
/* @var $form yii\widgets\ActiveForm */

if (isset($_REQUEST['BadgesDatabaseSearch']['pagesize'])) { 
	$pagesize = $_REQUEST['BadgesDatabaseSearch']['pagesize']; 
	$_SESSION['pagesize'] = $_REQUEST['BadgesDatabaseSearch']['pagesize'];
} elseif (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=20;
}
?>

<div class="badges-database-search">
<div class="row">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
	<div class="col-xs-0 col-sm-2">
	<?= $form->field($model, 'pagesize')->dropDownlist([ 20 => 20, 50 => 50, 100 => 100, 200=>200],['value'=>$pagesize ,'id' => 'pagesize'])->label('Page size: ') ?>
	</div>
    <?php // echo $form->field($model, 'badge_number') ?>
    <?php // echo $form->field($model, 'prefix') ?>
    <?php // echo $form->field($model, 'first_name') ?>
    <?php // echo $form->field($model, 'last_name') ?>
    <?php // echo $form->field($model, 'suffix') ?>
    <?php // echo $form->field($model, 'address') ?>
    <?php // echo $form->field($model, 'city') ?>
    <?php // echo $form->field($model, 'state') ?>
    <?php // echo $form->field($model, 'zip') ?>
    <?php // echo $form->field($model, 'gender') ?>
    <?php // echo $form->field($model, 'yob') ?>
    <?php // echo $form->field($model, 'email') ?>
    <?php // echo $form->field($model, 'phone') ?>
    <?php // echo $form->field($model, 'phone_op') ?>
    <?php // echo $form->field($model, 'ice_contact') ?>
    <?php // echo $form->field($model, 'ice_phone') ?>
    <?php // echo $form->field($model, 'club_name') ?>
    <?php // echo $form->field($model, 'club_id') ?>
    <?php // echo $form->field($model, 'mem_type') ?>
    <?php // echo $form->field($model, 'primary') ?>
    <?php // echo $form->field($model, 'incep') ?>
    <?php // echo $form->field($model, 'expires') ?>
    <?php // echo $form->field($model, 'qrcode') ?>
    <?php // echo $form->field($model, 'wt_date') ?>
    <?php // echo $form->field($model, 'wt_instru') ?>
    <?php // echo $form->field($model, 'remarks') ?>
    <?php // echo $form->field($model, 'status') ?>
    <?php // echo $form->field($model, 'soft_delete') ?>
    <?php // echo $form->field($model, 'created_at') ?>
    <?php // echo $form->field($model, 'updated_at') ?>
    <div class="col-xs-0 col-sm-2" style="margin-top: 18px;">
	<div class="form-group" >
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>
	</div>

    <?php ActiveForm::end(); ?>
</div>
</div>
