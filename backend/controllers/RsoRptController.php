<?php

namespace backend\controllers;

use Yii;
use backend\controllers\AdminController;
use backend\models\RsoReports;
use backend\models\search\RsoReportsSearch;

/**
 * ParamsController implements the CRUD actions for RsoReports model.
 */
class RsoRptController extends AdminController {
	/**
	 * @inheritdoc
	 */

	public function actionCurrent() {
		yii::$app->controller->createLog(true, 'trex_B-C-RSO_Cur-Req', var_export($_REQUEST,true));
		$model = new RsoReports();

		if ($model->load(Yii::$app->request->post())) {
			$model->save();
	//		$this->createLog($this->getNowTime(), $this->getActiveUser()->username, 'Privilege Created : '.$model->id);
	//		Yii::$app->getSession()->setFlash('success', 'Privilege has been created');
	//		return $this->redirect(['index', 'id' => $model->id]);
	//	} else {
		}
			return $this->render('current', [
				'model' => $model,
			]);
	//	}
	}

	public function actionDelete() {
		Yii::$app->getSession()->setFlash('error', 'Do you really want This?  Function not written yet.');
		//Verify no user has selected permission
			//delete if none
		return $this->redirect(['index']);
	}

	public function actionIndex() {
		$searchModel = new RsoReportsSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionUpdate($id=1) {
		yii::$app->controller->createLog(true, 'trex_B-C-RSO_Cur-Req', var_export($_REQUEST,true));
		$model = $this->findModel($id);

		if ($model->load(Yii::$app->request->post())) {
			if (!$model->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
			yii::$app->controller->createLog(false, 'trex-m-s-bs:112 NOT VALID', var_export($model->errors,true));	
			}	

		if($model->save()) {
				Yii::$app->getSession()->setFlash('success', 'Report has been updated.');
			 } else {
				 yii::$app->controller->createLog(false, 'trex-m-s-bs:112 NOT VALID', var_export($model->errors,true));	
			 }
			return $this->redirect(['update', 'id' => $model->id]);
		} else {
			return $this->render('update', [
				'model' => $model,
			]);
		}
	}

	protected function findModel($id) {
		if (($model = RsoReports::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}
