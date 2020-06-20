<?php

namespace backend\controllers;

use Yii;
use backend\controllers\AdminController;
use backend\models\Privileges;
use backend\models\search\PrivilegesSearch;
use yii\web\Controller;
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
}
