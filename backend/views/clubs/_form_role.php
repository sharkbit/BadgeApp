<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\roles */
/* @var $form yii\widgets\ActiveForm */
/* @var $permissionGroups array */

$numList = '';
for ($x = 1; $x <= 99; $x++) {
	$numList .=json_encode([$x=>$x]);
}
$numList = json_decode(str_replace('}{',',',$numList));
?>

<div class="roles-form">
<?php $form = ActiveForm::begin(); ?>
<div class="row" style="margin: auto;">
	<div class="col-xs-3 col-sm-2 col-md-2" <?php if($model->isNewRecord){echo 'style="display: none;"';} ?> >
	<?= $form->field($model, 'role_id')->textInput(['maxlength' => true,'readonly'=>true]) ?>
	</div>
	<div class="col-xs-9 col-sm-4 col-md-4">
	<?= $form->field($model, 'role_name')->textInput(['maxlength' => true]) ?>
	</div>
	<div class="col-xs-6 col-sm-2 col-md-2">
	<?= $form->field($model, 'r_timeout')->textInput(['maxlength' => true]) ?>
	</div>
	<div class="col-xs-6 col-sm-2 col-md-2">
	<?= $form->field($model, 'disp_order')->dropDownList($numList,['value'=>$model->isNewRecord ? mt_rand(1, 100) : $model->disp_order]) ?>
	</div>
	<div class="col-xs-12">
		<?= $form->field($model, 'r_privs')->hiddenInput(['id' => 'role-permissions'])->label(false) ?>
		<?php
		$selectedPermissions = json_decode((string)$model->r_privs, true);
		if ($model->r_privs !== null && trim((string)$model->r_privs) !== '' && !is_array($selectedPermissions)) {
			throw new \UnexpectedValueException('The role permissions must be a JSON array.');
		}
		$selectedPermissions = is_array($selectedPermissions) ? $selectedPermissions : [];
		?>
		<h3>Permissions</h3>
		<?php foreach ($permissionGroups as $section => $permissions) { ?>
			<h4><?= Html::encode(ucfirst($section)) ?></h4>
			<div class="row">
				<?php foreach ($permissions as $permission) {
					$isEnabled = in_array($permission, $selectedPermissions, true);
				?>
					<div class="col-xs-6 col-sm-3 col-md-2">
						<button type="button"
							class="btn btn-xs role-permission-toggle <?= $isEnabled ? 'btn-success' : 'btn-default' ?>"
							data-permission="<?= Html::encode($permission) ?>"
							aria-pressed="<?= $isEnabled ? 'true' : 'false' ?>">
							<?= Html::encode($permission) ?>:
							<span><?= $isEnabled ? 'On' : 'Off' ?></span>
						</button>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
	<?php if(yii::$app->controller->hasPermission('clubs/role-create')) { ?>
    <div class="form-group btn-group pull-right">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    	<?= Html::resetButton('Reset <i class="fa fa-eraser"> </i>', ['class' => 'btn btn-danger']) ?>
    </div>
<?php } ?>
</div>
<?php ActiveForm::end(); ?>
</div>

<script>
	(function() {
		const permissionInput = document.getElementById('role-permissions');
		if (!permissionInput) {
			return;
		}

		function syncRolePermissions() {
			const enabled = Array.from(document.querySelectorAll('.role-permission-toggle[aria-pressed="true"]'))
				.map(function(button) {
					return button.dataset.permission;
				})
				.sort();
			permissionInput.value = JSON.stringify(enabled);
		}

		document.querySelectorAll('.role-permission-toggle').forEach(function(button) {
			button.addEventListener('click', function() {
				const isEnabled = button.getAttribute('aria-pressed') === 'true';
				button.setAttribute('aria-pressed', isEnabled ? 'false' : 'true');
				button.classList.toggle('btn-success', !isEnabled);
				button.classList.toggle('btn-default', isEnabled);
				button.querySelector('span').textContent = isEnabled ? 'Off' : 'On';
				syncRolePermissions();
			});
		});

		syncRolePermissions();
	})();
</script>

