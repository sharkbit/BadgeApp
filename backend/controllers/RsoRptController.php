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
			if(!$model) {$model = new RsoReports;}
			$model->load(Yii::$app->request->post());
			$model->rso = str_replace('"',"", json_encode($model->rso));
			$model->closed=(int)$model->closed;
			$model->remarks=$this->AddRemarks($model,'Updated by '.$_SESSION['user']);
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
				$model->remarks=$this->AddRemarks($model,'Updated by '.$_SESSION['user']);
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

	public function actionSticker() {
		return $this->render('stickers', [
				//'model' => $model,
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
			$model->rso = str_replace('"',"", json_encode($model->rso));
			$model->remarks=$this->AddRemarks($model,'Updated by '.$_SESSION['user']);
			$model->closed=(int)$model->closed;
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

	protected function AddRemarks($model, $comment) {
		
		$items=$model->getDirtyAttributes();
		$obejectWithkeys = [
            'rso' => "RSO's",
			'shift_anom'=> 'Shift Anomalies',
			'notes'=>'Notes',
			'cash_bos'=>'Cash BOS',
			'cash_eos'=>'Cash EOS',
			'closing'=>'Closing Notes',
			'closed'=>'Closed',
		];

		$responce = [];
		foreach($items as $key => $item) {
			if(array_key_exists($key,$obejectWithkeys)) {
				$responce[] = $obejectWithkeys[$key];
			}
		}
		sort($responce);
		$dirty=implode(", ",$responce);
			
		$remarksOld = json_decode($model->remarks,true);
		if($dirty) {
			$cmnt = "Updated: ".$dirty;
			$nowRemakrs = [
				'created_at' => $this->getNowTime(),
				'data' => $cmnt,
				'changed' => $comment,
			];
			
			if($remarksOld != '') {
				array_push($remarksOld,$nowRemakrs);
			} else {
				$remarksOld = [
					$nowRemakrs,
				];
			}
		}		
		return json_encode($remarksOld,true);
	}

	protected function findModel($id) {
		if (($model = RsoReports::findOne($id)) !== null) {
			return $model;
		} else {
			return false;
		}
	}
}
