<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use backend\models\clubs;
?>
<hr />
<h2> Club Officers</h2>

<?PHP if (1==1) { ?>


<?php $formO = ActiveForm::begin(['action' => ['/clubs/officers'],'method' => 'post']); ?>
<div class="row" style="margin: auto;">
	<div class="col-xs-6 col-sm-32" >
<?php $currentUrl = yii::$app->controller->getCurrentUrl();
	if($currentUrl['controllerId']=='clubs'&&$currentUrl['actionId']=='update') {

		echo Html::dropDownList('club_id', '', (new clubs)->getClubList(false,false,true),['prompt'=>'Select Club'] );
	}
?>

	</div>
	<div class="col-xs-6 col-sm-3" >
	
	</div>
	<div class="col-xs-6 col-sm-3" >
		<?php //= $formO->field($model, 'Roll')->textInput(['maxlength' => true,'readonly'=>true]) ?>
	</div>
	<div class="col-xs-6 col-sm-3" >
		 <?= Html::submitButton('Add', ['class' => 'btn btn-success']) ?>
	</div>
</div>

<?php } ?>


Pull Table of Roles...

