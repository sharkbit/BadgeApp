<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\widgets\DatePicker;

$this->title = 'Create Badge';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $form = ActiveForm::begin(['id' => 'form-signup']); ?> 


<div class="row">
   <div class="col-xs-12">
         <?= $form->field($model, 'clubName')->textInput(['placeholder'=>'Club Name']) ?> 
         <?= $form->field($model, 'clubShortName')->textInput(['placeholder'=>'Cub Short Name']) ?>
         <a href="/badge/club-name-look-up" class="btn btn-success pull-right"> Create Club</a>
   </div>
</div>         
<?php ActiveForm::end(); ?>
    
             

           

