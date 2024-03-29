<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Badges;
use backend\models\clubs;

/* @var $this yii\web\View */
/* @var $model backend\models\BadgesDatabase */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="badges-database-form" >
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'badge_number')->textInput() ?>
    <?= $form->field($model, 'prefix')->textInput() ?>
    <?= $form->field($model, 'first_name')->textInput() ?>
    <?= $form->field($model, 'last_name')->textInput() ?>
    <?= $form->field($model, 'suffix')->textInput() ?>
    <?= $form->field($model, 'address')->textarea(['rows' => 1]) ?>
    <?= $form->field($model, 'city')->textInput() ?>
    <?= $form->field($model, 'state')->textInput() ?>
    <?= $form->field($model, 'zip')->textInput() ?>
    <?= $form->field($model, 'gender')->dropDownList([ 'm'=>'Male', 'f'=>'Female']) ?>
    <?= $form->field($model, 'yob')->textInput() ?>
    <?= $form->field($model, 'email')->textInput() ?>
    <?= $form->field($model, 'phone_op')->textInput() ?>
    <?= $form->field($model, 'ice_contact')->textInput() ?>
    <?= $form->field($model, 'ice_phone')->textInput() ?>
    <?= $form->field($model, 'club_id')->dropDownList((new clubs)->getClubList(false,false),['value'=>(new clubs)->getMyClubs($model->badge_number),'multiple'=>true]).PHP_EOL; ?>
    <?= $form->field($model, 'mem_type')->dropDownList((new Badges)->getMemberShipList()).PHP_EOL; ?>
    <?= $form->field($model, 'primary')->textInput() ?>
    <?= $form->field($model, 'incep')->textInput() ?>
    <?= $form->field($model, 'expires')->textInput() ?>
    <?= $form->field($model, 'qrcode')->textInput() ?>
    <?= $form->field($model, 'wt_date')->textInput() ?>
    <?= $form->field($model, 'wt_instru')->textInput() ?>
    <?= $form->field($model, 'remarks')->textarea(['rows' => 2]) ?>
    <?= $form->field($model, 'status')->dropDownList((new Badges)->getMemberStatus(), ['prompt' => '']) ?>
    <?= $form->field($model, 'created_at')->textInput() ?>
    <?= $form->field($model, 'updated_at')->textInput() ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>

<script>
$("#badgesdatabase-club_id").select2({placeholder_text_multiple:'Choose Clubs',width: "100%"});
</script>