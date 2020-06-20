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
    <div class="col-xs-12 col-sm-8">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <?= $form->field($model, 'badgeNumber')->textInput(['readonly' => true,'value'=>'0001']) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-2">
                <?= $form->field($model, 'prefix')->textInput([]) ?>
            </div>
            <div class="col-xs-12 col-sm-4">
                <?= $form->field($model, 'firstName')->textInput([]) ?>
            </div>
            <div class="col-xs-12 col-sm-4">
                <?= $form->field($model, 'lastName')->textInput([]) ?>
            </div>
            <div class="col-xs-12 col-sm-2">
                <?= $form->field($model, 'sufix')->textInput([]) ?>
            </div>

      
            <div class="col-xs-12 col-sm-3">
                <?= $form->field($model, 'city')->textInput([]) ?>
            </div>
            <div class="col-xs-12 col-sm-2">
                <?= $form->field($model, 'state')->textInput([]) ?>
            </div>
            <div class="col-xs-12 col-sm-2">
                <?= $form->field($model, 'zip')->textInput([]) ?>
            </div>
            <div class="col-xs-12 col-sm-2">
                <?= $form->field($model, 'gender')->dropDownList(['1'=>'Male','2'=>'Female'],['prompt'=>'select']) ?>
            </div>
            <div class="col-xs-12 col-sm-3">
                <?= $form->field($model, 'yob')->widget(DatePicker::classname(), [
                    'options' => ['placeholder' => 'Enter birth date ...'],
                    'pluginOptions' => [
                        'endDate' => date('d-m-Y h:i:s'),
                        'autoclose'=>true
                    ]
                ]);?>
                
            </div>

            <div class="col-xs-12 col-sm-6">
                <?= $form->field($model, 'email1')->textInput([]) ?>
            </div>
            <div class="col-xs-12 col-sm-6">
                <?= $form->field($model, 'email2')->textInput([]) ?>
            </div>
            <div class="col-xs-12 col-sm-6">
                <?= $form->field($model, 'phone1')->textInput([]) ?>
            </div>
            <div class="col-xs-12 col-sm-6">
                <?= $form->field($model, 'phone2')->textInput([]) ?>
            </div>
            <div class="col-xs-12 col-sm-6">
                <?= $form->field($model, 'emergancyContact1')->textInput([]) ?>
            </div>
            <div class="col-xs-12 col-sm-6">
                <?= $form->field($model, 'emergancyContact2')->textInput([]) ?>
            </div>
            <div class="col-xs-12 col-sm-8">
                <?= $form->field($model, 'clubName')->dropDownList(['1'=>'Club Name A','2'=>'Club Name B'],['prompt'=>'select']) ?>
            </div>
            <div class="col-xs-12 col-sm-4">
                <?= $form->field($model, 'clubId')->textInput(['readonly' => true,'value'=>'43']) ?>
            </div>
            <div class="col-xs-12 col-sm-3">
                <?= $form->field($model, 'memberType')->dropDownList(['1'=>'Primary','2'=>'Family','3'=>'Junior','4'=>'Life'],['prompt'=>'select']) ?>
            </div>
            <div class="col-xs-12 col-sm-3">
                <?= $form->field($model, 'badgeType')->textInput(['readonly' => true,'value'=>'demo']) ?>
            </div>
            <div class="col-xs-12 col-sm-3">
                <?= $form->field($model, 'primary')->dropDownList(['1'=>'Family Badge Holder A ','2'=>'Family Badge Holder 2'],['prompt'=>'select']) ?>
            </div>

            <div class="col-xs-12 col-sm-3">
                <?= $form->field($model, 'incep')->textInput(['readonly' => true,'value'=>date('d-m-Y H:i:s')]) ?>
            </div>
            <?php 
                $month =date('m');
                if($month<=10) {
                    $date = date('30-01-Y', strtotime('+1 year'));
                }
                else {
                    $date = date('30-01-Y', strtotime('+2 year'));
                }

             ?>
            <div class="col-xs-12 col-sm-3">
                <?= $form->field($model, 'expireDate')->textInput(['readonly' => true,'value'=>$date ]) ?>
            </div>

            <div class="col-xs-12 col-sm-3">
                <?= $form->field($model, 'qrcode')->textInput(['readonly' => true,'value'=>'http://chart.apis.google.com/chart?cht=qr&chs=100x100&chl=sample&chld=H|0' ]) ?>
            </div>

            <div class="col-xs-12 col-sm-3">
                <?= $form->field($model, 'wtDate')->textInput(['value'=>date('d-m-Y') ]) ?>
            </div>

            <div class="col-xs-12 col-sm-3">
                <?= $form->field($model, 'wtInstru')->textInput([]) ?>
            </div>

            <div class="col-xs-12 col-sm-12">
                <?= $form->field($model, 'remarks')->textarea(['rows' => '4']) ?>
            </div>

            <div class="col-xs-12">
                <?= Html::a('Save', ['#'], ['class' => 'btn btn-success pull-left']) ?>
                <?= Html::a('Exit', ['#'], ['class' => 'btn btn-danger pull-right']) ?>
            </div>
            

        </div>
    </div>
    <div class="col-xs-12 col-sm-4">
        <div class="row">
            <div class="summary-block-payment box">
                <h3 class="text-center"> Badge Renewal </h3>
                <div class="col-xs-12 col-sm-12">
                    <?= $form->field($model, 'procDate')->textInput(['readonly' => true,'value'=>date('d-m-Y H:i:s')]) ?>
                </div>
                <div class="col-xs-12 col-sm-12">
                    <?= $form->field($model, 'badgeFee')->textInput(['readonly' => true,'value'=>'$ 420.00']) ?>
                </div>
                <div class="col-xs-12 col-sm-12">
                    <?= $form->field($model, 'credits')->textInput(['readonly' => true,'value'=>'$ 210.00']) ?>
                </div>
                <div class="col-xs-12 col-sm-12">
                    <?= $form->field($model, 'amountDue')->textInput(['readonly' => true,'value'=>'$ 210.00']) ?>
                </div>
                <div class="col-xs-12 col-sm-12">
                    <?= $form->field($model, 'paymentMethod')->dropDownList(['1'=>'Cash','2'=>'Check','3'=>'Credit Card','4'=>'Online','5'=>'Other'],['prompt'=>'select']) ?>
                </div>
                <div class="col-xs-12 col-sm-12">
                    <?= $form->field($model, 'stikker')->textInput([]) ?>
                </div>
                <a href="" class="btn btn-primary" style="width: 100%; "> Renewal badge </a>
                <div class="clearfix"></div>
              
            </div>
            

        </div>



        <div class="row">
            <div class="summary-block-payment box">
                <h3 class="text-center"> Cetifications </h3>
                <div class="col-xs-12 col-sm-12">
                    <?= $form->field($model, 'procDate')->textInput(['readonly' => true,'value'=>date('d-m-Y H:i:s')]) ?>
                </div>
                <div class="col-xs-12 col-sm-12">
                    <?= $form->field($model, 'stikker')->textInput([]) ?>
                </div>
                <div class="col-xs-12 col-sm-12">
                    <?= $form->field($model, 'certificationType')->dropDownList(['certificaton type a'=>'certificaton type b','certificaton type c'=>'certificaton type c'],['prompt'=>'select']) ?>
                </div>
               
                <div class="col-xs-12 col-sm-12">
                    <?= $form->field($model, 'status')->dropDownList(['1'=>'active','2'=>'Inactive'],['prompt'=>'select']) ?>
                </div>
                
                <a href="" class="btn btn-primary" style="width: 100%; "> Add Certifications </a>
                <div class="clearfix"></div>
              
            </div>
            

        </div>


    </div>
</div>         
<?php ActiveForm::end(); ?>
    
             

           

