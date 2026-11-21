<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

$this->title = 'Full Raw Privileges';
$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/privileges/index']];

echo $this->render('_view-tab-menu').PHP_EOL;
?>

<div class="privileges-raw">
    <div class="row">
	   <div class="col-xs-12">
		  <h2><?= Html::encode($this->title) ?></h2>
		</div>
	</div>

<?php if(yii::$app->controller->hasPermission('privileges/create')) { ?>
	<div class="row">
		<div class="col-xs-8">
			<?php $form = ActiveForm::begin(['method' => 'post']); ?>
			<div class="form-group">
				<label for="permission">Add Permission</label>
				<input type="text" id="permission" name="permission" class="form-control"
					placeholder="page/action" required>
			</div>
		</div>
		<div class="col-xs-4">
			<br /><?= Html::submitButton('Add Permission', ['class' => 'btn btn-primary']) ?>
			<?php ActiveForm::end(); ?>
		</div>
	</div>
<?php } ?>

	<div class="row">
		<div class="col-xs-12">
			<?php if (empty($groupedPermissions)) { ?>
				<p>No permissions found.</p>
			<?php } else { ?>
				<?php foreach ($groupedPermissions as $section => $permissions) { ?>
					<h3><?= ucfirst(Html::encode($section)) ?></h3>
					<div class="row">
						<?php foreach ($permissions as $permission) { ?>
							<div class="col-xs-6 col-sm-3 col-md-2">
								<p>
									<?= Html::encode($permission) ?>
									<?php if (yii::$app->controller->hasPermission('privileges/delete')) { ?>
										<?= Html::a('&times;', '#', [
											'class' => 'del',
											'aria-label' => 'Delete ' . $permission,
											'title' => 'Delete permission',
											'encode' => false,
											'onclick' => 'jsRemovePermission(' . Json::htmlEncode($permission) . '); return false;',
										]) ?>
									<?php } ?>
								</p>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
			<?php } ?>
		</div>
	</div>

	<style>
	.del {
		color: red;
		font-weight: bold;
	}

	.del:hover,
	.del:focus {
		color: #000;
		text-decoration: none;
		cursor: pointer;
	}
	</style>

	<script>
		function jsRemovePermission(permission) {
			if (!window.confirm('Are you sure you want to remove ' + permission + '?')) {
				return;
			}

			const data = {deletePermission: permission};
			data[<?= Json::htmlEncode(Yii::$app->request->csrfParam) ?>] = <?= Json::htmlEncode(Yii::$app->request->getCsrfToken()) ?>;
			jQuery.ajax({
				method: 'POST',
				url: <?= Json::htmlEncode(Yii::$app->params['rootUrl'] . '/privileges/raw') ?>,
				data: data,
				dataType: 'json'
			}).done(function(response) {
				if (response && response.success) {
					window.location.reload();
				} else {
					window.alert((response && response.message) || 'The permission could not be deleted.');
				}
			}).fail(function() {
				window.alert('The permission could not be deleted.');
			});
		}
	</script>
</div>