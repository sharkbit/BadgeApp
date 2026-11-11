<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\InitialAdminForm */

$this->title = 'Create Initial Administrator';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-login">
	<div class="row">
		<div class="col-xs-12">
			<div class="login-box">
				<h2><?= Html::encode($this->title) ?></h2>
				<p>No user accounts exist yet. Create the first account; it will receive root administrator privileges.</p>

				<?php $form = ActiveForm::begin(['id' => 'initial-admin-form']); ?>
				<?= $form->field($model, 'full_name')->textInput(['autofocus' => true]) ?>
				<?= $form->field($model, 'username')->textInput(['autocomplete' => 'username']) ?>
				<?= $form->field($model, 'email')->input('email', ['autocomplete' => 'email']) ?>
				<?= $form->field($model, 'password')->passwordInput(['autocomplete' => 'new-password']) ?>
				<?= $form->field($model, 'confirmPassword')->passwordInput(['autocomplete' => 'new-password']) ?>
				<div class="form-group">
					<?= Html::submitButton('Create Root Administrator', [
						'class' => 'btn btn-primary pull-right',
						'name' => 'initial-admin-button',
					]) ?>
				</div>
				<div class="clearfix"></div>
				<?php ActiveForm::end(); ?>
			</div>
		</div>
	</div>
</div>
