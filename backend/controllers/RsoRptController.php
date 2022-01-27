<?php

namespace backend\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use backend\controllers\AdminController;
use backend\models\RsoReports;
use backend\models\Stickers;
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
					yii::$app->controller->createLog(false, 'trex-c-RSO-rpt:50 NOT VALID', var_export($model->errors,true));
					return $this->render('current', [
						'model' => $model,
					]);
				}
			}
		}
		$model =  (new RsoReports)->find()->where(['closed'=>0])->orderBy(['date_open'=>SORT_DESC])->one();
		
		if((!$model) && (array_intersect([3,6],$_SESSION['privilege']))) {
			$model = new RsoReports;
			$model->date_open = $this->getNowTime();
			$model->save(false);
			$this->createLog($this->getNowTime(), $this->getActiveUser()->username, 'Opened RSO Report: '.$model->id);
		} elseif (!$model) {
			Yii::$app->getSession()->setFlash('error', "RSOs Must Open Report");
			return $this->redirect('index');
		} elseif (array_intersect([1,3,6],$_SESSION['privilege'])) {
			return $this->render('current', [ 'model' => $model, ]);
		} else {
			Yii::$app->getSession()->setFlash('error', "RSOs Only");
			return $this->redirect('index');
		}
	}

	public function actionDelete($id=1) {
		$model = $this->findModel($id);
		if($model->delete()) {
			Yii::$app->getSession()->setFlash('success', 'Report Deleted.');
			yii::$app->controller->createLog(true, $_SESSION['user'],"RSO Report','RSO Report #$id Deleted");
			
		} else {
			Yii::$app->getSession()->setFlash('error', 'Record not deleted!');
		}
		return $this->redirect(['index']);
	}

	public function actionHelp() {
		return $this->render('help');
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
		if (isset($_REQUEST['sticker_add']) && ($_REQUEST['sticker_add']==1)) {
			$x = (int)$_REQUEST['StickersSearch']['start'];
			$yr = (int)$_REQUEST['StickersSearch']['yr'];
			yii::$app->controller->createLog(true, $_SESSION['user'],"Sticker','Adding ".$yr.' - '.$_REQUEST['StickersSearch']['start'].' - '.$_REQUEST['StickersSearch']['end']);
			$chk = ArrayHelper::getColumn(Stickers::find()->where(['like','sticker',$yr."%",false])->all(),'sticker');
			do {
				$new_stkr = $yr.'-'.str_pad($x, 4, '0', STR_PAD_LEFT);
				if(in_array($new_stkr,$chk)) { $x++; continue; }
				$stkr = new Stickers;
				$stkr->sticker = $new_stkr;
				$stkr->status = 'adm';
				$stkr->save();
				$x++;
			} while ($x < (int)$_REQUEST['StickersSearch']['end']+1);
		}

		if (isset($_REQUEST['sticker_move']) && ($_REQUEST['sticker_move']==1)) {
			$yr = (int)$_REQUEST['StickersSearch']['yr_mv'];
			yii::$app->controller->createLog(true, $_SESSION['user'],"Sticker','Moving ".$yr.' - '.$_REQUEST['StickersSearch']['stkrs'].' to '.$_REQUEST['StickersSearch']['to']);
			$moving=explode(',',$_REQUEST['StickersSearch']['stkrs']);

			foreach($moving as $rng) {
				if(strpos($rng,'-')) {
					$rng_a = explode('-',$rng);
					$y=$rng_a[0];
					do {
						$stker = Stickers::find()->where(['sticker'=>$yr.'-'.str_pad($y, 4, '0', STR_PAD_LEFT)])->andwhere(['in','status',['rso','adm']])->one();
						if($stker){
							$stker->status = $_REQUEST['StickersSearch']['to'];
							$stker->updated =  $this->getNowTime();
							$stker->save();
						}
						$y++;
					} while ($y < $rng_a[1]+1);
				} else {
					$stker = Stickers::find()->where(['sticker'=>$yr.'-'.str_pad($rng, 4, '0', STR_PAD_LEFT)])->andwhere(['in','status',['rso','adm']])->one();
					if($stker) {
						$stker->status = $_REQUEST['StickersSearch']['to'];
						$stker->updated =  $this->getNowTime();
						$stker->save();
					}
				}
			}
		}

		$searchModel = new StickersSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		if ((in_array(3, json_decode(yii::$app->user->identity->privilege))) && (in_array(10, json_decode(yii::$app->user->identity->privilege)))) {
			$dataProvider->query->andWhere("status='rso' OR status='cas'");
		}
		elseif (array_intersect([3,6], json_decode(yii::$app->user->identity->privilege))) {
			$dataProvider->query->andWhere(['status'=>'rso']);
		}

		return $this->render('stickers', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionStickerUpdate($id=1) {
		$model =  (new Stickers)->find()->where(['s_id'=>$id])->one();
		if($model) {
		if ($model->load(Yii::$app->request->post())) {
			$model->updated = $this->getNowTime();
			if($model->save()) {
				yii::$app->controller->createLog(true, $_SESSION['user'],"Sticker','Updated ".$model->sticker);
				return $this->redirect(['rso-rpt/sticker']);
			}
		}
		return $this->render('/rso-rpt/sticker-update',[
				'model' => $model,
			]);
		} else  { $this->redirect(['/rso-rpt/sticker']); }
	}

	public function actionStickerDelete($id=1) {
		Yii::$app->getSession()->setFlash('error', 'Do you really want This?  Function not written yet.');
		//Verify no user has selected permission
			//delete if none
		return $this->redirect(['/rso-rpt/sticker']);
	}

	public function actionUpdate($id=1) {
		$model = $this->findModel($id);
		if($model) {
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
		} else { return $this->redirect(['index']); }
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
