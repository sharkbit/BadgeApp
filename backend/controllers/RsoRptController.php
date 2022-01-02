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
		if(Yii::$app->request->post()) {
			$model = $this->findModel($_POST['RsoReports']['id']);
			$model->load(Yii::$app->request->post());
			$model->rso = str_replace('"',"", json_encode($model->rso));
			$model->save();
			if($model->closed==1) {
				$this->createLog($this->getNowTime(), $this->getActiveUser()->username, 'Closed RSO Report: '.$model->id);
				return $this->redirect(['index']);
			}
		}

		elseif(Yii::$app->request->get()) {
			if($_GET['close']==1) {
				$model = $this->findModel($_GET['id']);
				$model->closed = 1;
				$model->rso = str_replace('"',"", json_encode($model->rso));
				$model->save();
				$this->createLog($this->getNowTime(), $this->getActiveUser()->username, 'Closed RSO Report: '.$model->id);
				return $this->redirect(['index']);
			}
		}
		$model =  (new RsoReports)->find()->where(['closed'=>0])->orderBy(['date'=>SORT_DESC])->one();
		if(!$model) {$model = new RsoReports;}

		return $this->render('current', [
			'model' => $model,
		]);
	}

	public function actionDelete($id=1) {
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
