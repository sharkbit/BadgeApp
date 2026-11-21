<?php

namespace backend\controllers;

use Yii;
use backend\controllers\AdminController;
use backend\models\Privileges;
use backend\models\Params;
use backend\models\Roles;
use backend\models\search\PrivilegesSearch;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ParamsController implements the CRUD actions for Privileges model.
 */
class PrivilegesController extends AdminController {
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'delete' => ['POST'],
					'raw' => ['GET', 'POST'],
				],
			],
		];
	}

	public function actionCreate() {
		$model = new Privileges();

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$this->createLog($this->getNowTime(), $this->getActiveUser()->username, 'Privilege Created : '.$model->id);
			Yii::$app->getSession()->setFlash('success', 'Privilege has been created');
			return $this->redirect(['index', 'id' => $model->id]);
		} else {
			return $this->render('create', [
				'model' => $model,
			]);
		}
	}

	public function actionDelete() {
		Yii::$app->getSession()->setFlash('error', 'Do you really want This?  Function not written yet.');
		//Verify no user has selected permission
			//delete if none
		return $this->redirect(['index']);
	}

	public function actionIndex() {
		$searchModel = new PrivilegesSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionUpdate($id=1) {
		$model = $this->findModel($id);

		if ($model->load(Yii::$app->request->post())) {
			 if($model->save()) {
				Yii::$app->getSession()->setFlash('success', 'Privilege has been updated.');
			 ;}
			return $this->redirect(['update', 'id' => $model->id]);
		} else {
			return $this->render('update', [
				'model' => $model,
			]);
		}
	}

	protected function findModel($id) {
		if (($model = Privileges::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	public function actionRaw() {
		$model = Params::getParams();
		if ($model === null) {
			throw new NotFoundHttpException('The application settings record does not exist.');
		}

		if (Yii::$app->request->isPost) {
			$deletePermission = Yii::$app->request->post('deletePermission');
			if ($deletePermission !== null) {
				if (!$this->hasPermission('privileges/delete')) {
					throw new ForbiddenHttpException('You are not allowed to delete permissions.');
				}

				$deletePermission = (string)$deletePermission;
				$permissions = json_decode((string)$model->privs, true);
				if ($model->privs === null || trim((string)$model->privs) === '') {
					$permissions = [];
				}
				if (!is_array($permissions) || array_values($permissions) !== $permissions) {
					throw new \UnexpectedValueException('The stored permission list must be a JSON array.');
				}

				$rolePermissions = [];
				$permissionExists = in_array($deletePermission, $permissions, true);
				foreach (Roles::find()->all() as $role) {
					if ($role->r_privs === null || trim((string)$role->r_privs) === '') {
						continue;
					}
					$permissionsForRole = json_decode((string)$role->r_privs, true);
					if (!is_array($permissionsForRole) || array_values($permissionsForRole) !== $permissionsForRole) {
						throw new \UnexpectedValueException('Role privileges must be stored as JSON arrays.');
					}
					foreach ($permissionsForRole as $permission) {
						if (!is_string($permission)) {
							throw new \UnexpectedValueException('Role privileges must contain permission strings.');
						}
					}
					if (!in_array($deletePermission, $permissionsForRole, true)) {
						continue;
					}

					$permissionsForRole = array_values(array_filter(
						$permissionsForRole,
						static function ($permission) use ($deletePermission) {
							return $permission !== $deletePermission;
						}
					));
					sort($permissionsForRole, SORT_STRING);
					$rolePermissions[$role->role_id] = json_encode($permissionsForRole, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
				}

				$permissions = array_values(array_filter(
					$permissions,
					static function ($permission) use ($deletePermission) {
						return $permission !== $deletePermission;
					}
				));
				sort($permissions, SORT_STRING);
				$updatedParams = clone $model;
				$updatedParams->privs = json_encode($permissions, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

				$transaction = Yii::$app->db->beginTransaction();
				$deleted = false;
				try {
					if (!$updatedParams->save(false, ['privs'])) {
						$transaction->rollBack();
						if (Yii::$app->request->isAjax) {
							Yii::$app->response->statusCode = 500;
							Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
							return ['success' => false, 'message' => 'The permission could not be deleted.'];
						}
						Yii::$app->session->setFlash('error', 'The permission could not be deleted.');
					} else {
						foreach ($rolePermissions as $roleId => $rolePrivs) {
							Roles::updateAll(['r_privs' => $rolePrivs], ['role_id' => $roleId]);
						}
						$transaction->commit();
						$model = $updatedParams;
						$deleted = true;
					}
				} catch (\Throwable $exception) {
					if ($transaction->isActive) {
						$transaction->rollBack();
					}
					Yii::$app->session->remove('paramsRecord');
					throw $exception;
				}

				if ($deleted) {
					if ($permissionExists) {
						$affectedRoleCount = count($rolePermissions);
						$this->createLog(
							$this->getNowTime(),
							$_SESSION['user'] ?? 'Unknown',
							'Permission Deleted : '.$deletePermission.' (removed from '.$affectedRoleCount
								.' club role'.($affectedRoleCount === 1 ? '' : 's').')'
						);
					}
					if (Yii::$app->request->isAjax) {
						Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
						return ['success' => true];
					}
					Yii::$app->session->setFlash('success', 'Permission deleted from permissions and club roles.');
					return $this->redirect(['raw']);
				}
			}

			if ($deletePermission === null) {
				$permission = trim((string)Yii::$app->request->post('permission', ''));
				if (!preg_match('#^[a-z0-9_-]+/[a-z0-9_-]+$#i', $permission)) {
					Yii::$app->session->setFlash('error', 'Enter one permission in the format section/action, such as params/update.');
				} else {
					$permissions = json_decode((string)$model->privs, true);
					if ($model->privs === null || trim((string)$model->privs) === '') {
						$permissions = [];
					}

					if (!is_array($permissions) || array_values($permissions) !== $permissions) {
						throw new \UnexpectedValueException('The stored permission list must be a JSON array.');
					}

					$permissionAdded = !in_array($permission, $permissions, true);
					if ($permissionAdded) {
						$permissions[] = $permission;
					}
					sort($permissions, SORT_STRING);
					$model->privs = json_encode($permissions, JSON_UNESCAPED_SLASHES);
					if (!$model->save(false, ['privs'])) {
						Yii::$app->session->setFlash('error', 'The permission could not be saved.');
					} else {
						if ($permissionAdded) {
							$this->createLog(
								$this->getNowTime(),
								$_SESSION['user'] ?? 'Unknown',
								'Permission Created : '.$permission
							);
						}
						Yii::$app->session->setFlash('success', 'Permission list updated.');
						return $this->redirect(['raw']);
					}
				}
			}
		}

		$permissions = json_decode((string)$model->privs, true);
		if ($model->privs === null || trim((string)$model->privs) === '') {
			$permissions = [];
		}
		if (!is_array($permissions) || array_values($permissions) !== $permissions) {
			throw new \UnexpectedValueException('The stored permission list must be a JSON array.');
		}
		sort($permissions, SORT_STRING);

		$groupedPermissions = [];
		foreach ($permissions as $permission) {
			if (!is_string($permission) || strpos($permission, '/') === false) {
				throw new \UnexpectedValueException('Each stored permission must use the section/action format.');
			}
			[$section] = explode('/', $permission, 2);
			$groupedPermissions[$section][] = $permission;
		}
		ksort($groupedPermissions, SORT_STRING);

		return $this->render('raw', [
			'model' => $model,
			'groupedPermissions' => $groupedPermissions,
		]);
	}
}
