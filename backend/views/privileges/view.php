<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Params;

/* @var $form yii\widgets\ActiveForm */
/* @var $this yii\web\View */
/* @var $model backend\models\Privileges */

$this->title = 'View Privilege';
$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']];
$this->params['breadcrumbs'][] = ['label' => 'Privileges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="privileges-update">

    <h2><?= Html::encode($this->title) ?></h2>

<?php $form = ActiveForm::begin(); ?>
<div class="row" style="margin: auto;">

<div class="col-xs-12">

	<div class="row" >

	<div class="col-xs-6 col-sm-4 col-md-2">
		<?= $form->field($model, 'priv_sort')->textInput(['maxlength' => true,'readonly'=>true]) ?>
	</div>
	<div class="col-xs-12 col-sm-6 col-md-4">
		<?= $form->field($model, 'privilege')->textInput(['maxlength' => true,'readonly'=>true]) ?>
	</div>
	<div class="col-xs-6 col-sm-4 col-md-2">
		<?= $form->field($model, 'timeout')->textInput(['maxlength' => true,'readonly'=>true]) ?>
	</div>
   
	</div>
</div>
</div>
<?php ActiveForm::end(); ?>

<?= $this->render('_show_priv', ['model' => $model]) ?>
