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
   		<a href="/badge/users-index" class="btn btn-primary"> <i class="fa fa-long-arrow-left" aria-hidden="true"></i> Back </a>
         <?= $form->field($model, 'username')->textInput(['readOnly'=>true,'placeholder'=>'username','value'=>'martin@43']) ?> 
         <?= $form->field($model, 'fullName')->textInput(['readOnly'=>true,'placeholder'=>'Full Name','value'=>'Martin G']) ?>
      
         <?= $form->field($model, 'privilege')->dropDownList(['Root','Admin'],['readOnly'=>true,]) ?>
   </div>
</div>         
<?php ActiveForm::end(); ?>
    
             

           

