<?php

namespace backend\controllers;

use Yii;
use backend\controllers\AdminController;
use backend\models\RsoReports;
use backend\models\search\RsoReportsSearch;
use backend\models\search\StickersSearch;

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
			$model->remarks=$this->AddRemarks($model,'Updated by '.$_SESSION['user']);
			if($model->save()) {
				if($model->closed==1) {
					return $this->redirect(['index']);
				}
			} else {
				Yii::$app->getSession()->setFlash('error', json_encode($model->errors));
				yii::$app->controller->createLog(false, 'trex-c-RSO-rpt:33 NOT VALID', var_export($model->errors,true));
				return $this->render('current', [
					'model' => $model,
				]);
			}
		}
		elseif(Yii::$app->request->get()) {
			if($_GET['close']==1) {
				$model = $this->findModel($_GET['id']);
				$model->closed = 1;
				$model->date_close = $this->getNowTime();
				$model->remarks=$this->AddRemarks($model,'Closed By '.$_SESSION['user']);
				if ($model->save()) {
					$this->createLog($this->getNowTime(), $this->getActiveUser()->username, 'Closed RSO Report: '.$model->id);
					return $this->redirect(['index']);
				} else {
					Yii::$app->getSession()->setFlash('error', json_encode($model->errors));
					yii::$app->controller->createLog(false, 'trex-c-RSO-rpt:51 NOT VALID', var_export($model->errors,true));
					return $this->render('current', [
						'model' => $model,
					]);
				}
			}
		}
		$model =  (new RsoReports)->find()->where(['closed'=>0])->orderBy(['date_open'=>SORT_DESC])->one();
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
yii::$app->controller->createLog(true, 'trex_sticker', var_export($_REQUEST,true));
		if (isset($_REQUEST['sticker_add']) && ($_REQUEST['sticker_add']==1)) {
			$x = (int)$_REQUEST['StickersSearch']['start'];
			$yr = (int)$_REQUEST['StickersSearch']['yr'];
			do {
				$stkr = new \backend\models\Stickers;
				$stkr->sticker=$yr.'-'.str_pad($x, 4, '0', STR_PAD_LEFT);
				$stkr->status='adm';
				if(!$stkr->save()) {
					yii::$app->controller->createLog(false, 'trex-c-RSO-rpt:33 NOT VALID', var_export($stkr->errors,true));
					echo 'broke';
					exit;
				}
				$x++;
			} while ($x < (int)$_REQUEST['StickersSearch']['end']);
		}

		if (isset($_REQUEST['sticker_move']) && ($_REQUEST['sticker_move']==1)) {
//yii::$app->controller->createLog(true, 'trex_sticker', var_export($_REQUEST,true));
			echo "move";
			exit;
		}

		$searchModel = new StickersSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);


		return $this->render('stickers', [
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
			$model->remarks=$this->AddRemarks($model,'Updated by '.$_SESSION['user']);
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

	public function actionView($id=1) {
		$model = RsoReports::find()->where(['id'=>$id])->one();
		if ($model) {
			return $this->render('view', [
				'model' => $model,
			]);
		} else {
			Yii::$app->getSession()->setFlash('error', 'Report not found.');
			return $this->redirect(['index']);
		}
	}

	protected function AddRemarks($model, $comment) {
		$model->closed=(int)$model->closed;
		$model->rso = str_replace('"',"", json_encode($model->rso));
		$model->wb_trap_cases = (int)$model->wb_trap_cases;
		
		$items=$model->getDirtyAttributes();
		$obejectWithkeys = [
			'mics'=>'MICs Status',
			'rso' => "RSO's",
			'shift_anom'=> 'Shift Anomalies',
			'notes'=>'Notes',
			'cash_bos'=>'Cash BOS',
			'cash_eos'=>'Cash EOS',
			'closing'=>'Closing Notes',
			'closed'=>'Closed',
			'wb_trap_cases'=>' Wobbel Trap Cases',
			'wb_color'=> 'Wristband Color',
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

	public function getYear() {
		$yr = date('Y');
		return [($yr-1)=>$yr-1,$yr=>$yr,$yr+1=>$yr+1];
	}

	protected function findModel($id) {
		if (($model = RsoReports::findOne($id)) !== null) {
			return $model;
		} else {
			return false;
		}
	}
}
