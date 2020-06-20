<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\widgets\DatePicker;

$this->title = 'Transfer Credits';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>   
<div class="row">
    
    <div class="row">
        <h3>Transfer Form</h3>
        <div class="col-xs-12 col-sm-4">
            <?= $form->field($model, 'badgeNumber')->textInput(['value'=>'0001']) ?>
             
        </div>
        <div class="col-xs-12 col-sm-4">
            <?= $form->field($model, 'badgeName')->textInput([]) ?>
        </div>
        <div class="col-xs-12 col-sm-4">
            <?= $form->field($model, 'hours')->textInput([]) ?>
        </div>
    </div>
    <div class="row">
        <h3>Transfer To</h3>
        <div class="col-xs-12 col-sm-4">
            <?= $form->field($model, 'badgeNumber')->textInput(['value'=>'0002']) ?>
             
        </div>
        <div class="col-xs-12 col-sm-4">
            <?= $form->field($model, 'badgeName')->textInput([]) ?>
        </div>
        <div class="col-xs-12 col-sm-4">
            <?= $form->field($model, 'hours')->textInput([]) ?>
        </div>

    </div>
    <div class="row">
        <div class="col-xs-8">
             <?= $form->field($model, 'remarks')->textarea(['rows' => '1']) ?>
        </div>
        <div class="col-xs-4">
             <?= $form->field($model, 'authorizedBy')->textInput([]) ?>
             <a href="/badge/brows-work-credits" class="btn btn-primary pull-right"> TRANSFER CREDITS</a>
       </div>
       
            
        
      
    </div>
       
        
    
    
</div>         
<?php ActiveForm::end(); ?>
    
             

           

