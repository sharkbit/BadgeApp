<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\widgets\DatePicker;

$this->title = 'Update Badge';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>   
<div class="row">
    <div class="row">
        <div class="col-xs-12 col-sm-4">
            <?= $form->field($model, 'badgeNumber')->textInput(['readonly' => true,'value'=>'0001']) ?>
             <?= $form->field($model, 'workDate')->widget(DatePicker::classname(), [
                'options' => ['placeholder' => 'Work Date'],
                'pluginOptions' => [
                    'endDate' => date('d-m-Y h:i:s'),
                    'autoclose'=>true
                ]
            ]);?>
        </div>
        <div class="col-xs-12 col-sm-4">
            <?= $form->field($model, 'badgeHolderName')->textInput([]) ?>
            <?= $form->field($model, 'workHours')->textInput([]) ?>
        </div>
        <div class="col-xs-12 col-sm-4">
            <?= $form->field($model, 'authorizedBy')->textInput([]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
             <?= $form->field($model, 'projectName')->textarea(['rows' => '3']) ?>
             <?= $form->field($model, 'remarks')->textarea(['rows' => '3']) ?>
        </div>
        <div class="col-xs-6">
            <a href="/badge/brows-work-credits" class="btn btn-primary pull-right"> SAVE AND CONTINUE</a>
        </div>
        <div class="col-xs-6">
            <a href="/site/index" class="btn btn-primary pull-right"> SAVE AND EXIT</a>
        </div>
    </div>
       
        
    
    
</div>         
<?php ActiveForm::end(); ?>
    
             

           

