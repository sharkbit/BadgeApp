<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\widgets\DatePicker;

$this->title = 'Create Authorized User';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $form = ActiveForm::begin(['id' => 'form-signup']); ?> 


<div class="row">
   <div class="col-xs-12">
         <?= $form->field($model, 'username')->textInput(['placeholder'=>'username','value'=>'martin@43']) ?> 
         <?= $form->field($model, 'fullName')->textInput(['placeholder'=>'Full Name','value'=>'Martin G']) ?>
         <?= $form->field($model, 'password')->passwordInput([]) ?>
         <?= $form->field($model, 'confirmPassword')->passwordInput([]) ?>
         <?= $form->field($model, 'privilege')->dropDownList(['Root','Admin'],[]) ?>
         <a href="/badge/users-index" class="btn btn-primary pull-right"> Update Authorized User</a>
   </div>
</div>         
<?php ActiveForm::end(); ?>
    
             

           

