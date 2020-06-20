<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\WorkCredits */
/* @var $form yii\widgets\ActiveForm */

if(strpos($_SERVER['REQUEST_URI'],'create')) {
	$model->work_date = date("M d, Y ");
	$session = Yii::$app->session;
	$stickyData = $session->get('stickyData');
	if(!empty($stickyData)) {
		$model->work_date = date("M d, Y ",strtotime($stickyData['work_date']));
		$model->authorized_by = $stickyData['authorized_by'];
		$model->work_hours = $stickyData['work_hours'];
		$model->project_name = $stickyData['project_name'];
		$model->remarks = $stickyData['remarks'];
		$model->supervisor = $stickyData['supervisor'];
	} 
} ?>

<?= $this->render('_view-tab-menu') ?>
	
<div class="work-credits-form" ng-controller="WorkCreditFrom">
    <?php $form = ActiveForm::begin([ 'id'=>'creditEntryForm' ]); ?>
	<div class="row">
        <div class="col-sm-3">
<?php	if((yii::$app->controller->hasPermission('work-credits/approve')) || (yii::$app->controller->hasPermission('work-credits/add'))) {
			echo $form->field($model, 'badge_number')->textInput(['readOnly'=>$model->isNewRecord ? false : true,'value'=>$model->isNewRecord ? $_SESSION['badge_number'] : $model->badge_number]);
		} else {
			echo $form->field($model, 'badge_number')->textInput(['readOnly'=>'false','value'=>$model->isNewRecord ? $_SESSION['badge_number'] : $model->badge_number]);
		} ?>
		</div>
		<div class="col-sm-4">
			<?= $form->field($model, 'badge_holder_name')->textInput(['readOnly'=>'true']) ?>
		</div>
		<div class="col-sm-4">
		<?= $form->field($model, 'supervisor')->textInput(['maxlength' => true]) ?>
		</div>
	</div>
	<div class="row">
        <div class="col-sm-3">
	<?php	if($model->isNewRecord) { 
				echo $form->field($model, 'work_date')->widget(DatePicker::classname(), [
					'options' => ['placeholder' => 'Work Date'],
					'pluginOptions' => [
						'autoclose'=>true,
						'format' => 'M dd, yyyy',
						'todayHighlight' => true ] ] ); 
			} else {
				echo $form->field($model, 'work_date')->textInput(['readOnly'=>'true','value'=>date('M d, Y',strtotime($model->work_date))]);
			} ?>
		</div>
		<div class="col-sm-4">
	<?php	if($model->isNewRecord) {  
				echo $form->field($model, 'work_hours')->textInput().PHP_EOL;
            } else {
				echo $form->field($model, 'work_hours_new')->textInput(['value'=>$model->work_hours]).PHP_EOL;
				echo $form->field($model, 'work_hours')->hiddenInput([])->label(false).PHP_EOL;
            } ?>
		</div>
		<div class="col-sm-4"> 
		</div>
	</div>

    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'project_name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'remarks')->textarea(['rows' => 6]) ?>
        </div>  
    </div>

    <div class="btn-group pull-right">
        <?php if($model->isNewRecord && empty($badgeArray)){ ?>
        <?= Html::submitButton('Next <i class="fa fa-arrow-right"> </i>',['class' => 'btn btn-primary next-Credit']) ?>
        <?php } ?>
	<?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success done-Credit' : 'btn btn-primary']) ?>
         <?php if($model->isNewRecord && empty($badgeArray)){ ?>
        <?= Html::resetButton('Reset <i class="fa fa-eraser"> </i>', ['class' => 'btn btn-warning']) ?>
    	<?php } ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
