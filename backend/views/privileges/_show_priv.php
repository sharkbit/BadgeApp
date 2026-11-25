<?php

use backend\models\Params;
use yii\helpers\Html;

$params = Params::getParams();
$configuredPermissions = $params === null || trim((string)$params->privs) === ''
	? []
	: json_decode((string)$params->privs, true);
$groupedPermissions = [];
if (is_array($configuredPermissions) && array_values($configuredPermissions) === $configuredPermissions) {
	foreach ($configuredPermissions as $permission) {
		$section = is_string($permission) && strpos($permission, '/') !== false
			? explode('/', $permission, 2)[0]
			: 'Invalid';
		$groupedPermissions[$section][] = $permission;
	}
	ksort($groupedPermissions, SORT_STRING);
}
?>

<div class="panel panel-default privilege-permission-status">
	<div class="panel-heading"><strong>Configured Permissions</strong></div>
	<div class="panel-body">
		<?php if (!is_array($configuredPermissions) || array_values($configuredPermissions) !== $configuredPermissions) { ?>
			<div class="alert alert-danger">The application permission list in params.privs is not a valid JSON array.</div>
		<?php } elseif (empty($configuredPermissions)) { ?>
			<p>No permissions are configured in params.privs.</p>
		<?php } else { ?>
			<?php foreach ($groupedPermissions as $section => $permissions) { ?>
				<h3><?= ucfirst(Html::encode($section)) ?></h3>
				<div class="row">
					<?php foreach ($permissions as $permission) {
						$permissionLabel = is_string($permission) ? $permission : json_encode($permission);
						if ($model->id === null || $model->id === '') {
							$statusClass = 'text-muted';
							$statusTitle = 'Save this privilege to test';
						} elseif (is_string($permission) && Yii::$app->controller->hasPermission($permission, (int)$model->id)) {
							$statusClass = 'text-success';
							$statusTitle = 'Matched';
						} else {
							$statusClass = 'text-danger';
							$statusTitle = 'Missing from user permission mappings';
						}
					?>
						<div class="col-xs-6 col-sm-3 col-md-2">
							<p><span class="<?= $statusClass ?>" title="<?= Html::encode($statusTitle) ?>"><?= Html::encode($permissionLabel) ?></span></p>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
		<?php } ?>
	</div>
</div>
