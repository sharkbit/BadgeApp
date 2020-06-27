<?php

namespace backend\controllers;

use yii;
use backend\models\Guest;
use backend\models\search\GuestSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\controllers\SiteController;
use backend\models\Badges;

/**
 * GuestController implements the CRUD actions for Guest model.
 */
class GuestController extends SiteController {

	public $enableCsrfValidation = false;

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
		$model = new Guest();
		if ($model->load(Yii::$app->request->post())) {
			$model->g_first_name = trim($model->g_first_name);
			$model->g_last_name = trim($model->g_last_name);
			$model->g_city = trim($model->g_city);
			$model->g_state = strtoupper(trim($model->g_state));
			$model->tmp_badge = trim($model->tmp_badge);

			if($model->save()) {
				Yii::$app->getSession()->setFlash('success', 'Visitor has been added!');

				$guest = Guest::find()->where(['id'=>$model->id])->one();
				if($guest->save()) {
					$model->save();
				}

				if(($_POST['Guest']['guest_count']) && $_POST['Guest']['guest_count'] > 1 )  {
					$stickyGuest = [
						'badge_number'=>$model->badge_number,
						'g_paid'=>$model->g_paid,
						'guest_count'=> $_POST['Guest']['guest_count'] - 1,
						'payment_type'=>$model->payment_type,
						'time_in'=>$model->time_in,
					];
					$_SESSION['stickyForm'] = $stickyGuest;
					return $this->redirect(['/guest/create']);
				} else  {
					unset( $_SESSION['stickyForm'] );
					return $this->redirect(['/guest/index']);
				}

			} else {
				yii::$app->controller->createLog(true, 'trex_C_GC:70 SaVE ERROR', var_export($model->errors,true));

				Yii::$app->getSession()->setFlash('error', 'action create - no save?');
				return $this->redirect(['/guest/index']);
			}
		}
		else {
			return $this->render('create', [ 'model' => $model ]);
		}
	}

	public function actionAddcredit(){
		//$this->createLog(false, 'trex_C_GC:103', 'Add Guest Credit');
		yii::$app->controller->createLog(false, 'trex', var_export($_POST,true));
		echo json_encode(['status'=>'error','msg'=>'C_GC:102']);
		exit;

	}

    public function actionDelete($id) {
		if($this->findModel($id)->delete()) {
            $this->createLog($this->getNowTime(), $_SESSION['user'], 'Deleted Guest: '.$id);
			Yii::$app->getSession()->setFlash('success', 'Guest #'.$id.' Deleted');
        }
		return $this->redirect(['index']);
    }

	public function actionIndex() {
		$searchModel = new GuestSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		if(!yii::$app->controller->hasPermission('guest/all')) {
			$sqlwhere="badge_number=".$_SESSION["badge_number"];
			$dataProvider->query->andWhere($sqlwhere);
		}
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionOut($id) {
		$nowDate = date("Y-m-d G:i:s",strtotime(yii::$app->controller->getNowTime()));

		$sql="UPDATE guest set time_out='".$nowDate. "' WHERE id=".$id;
		$connection = Yii::$app->getDb();
		$command = $connection->createCommand($sql);
		$saveOut = $command->execute();
		Yii::warning("Rec Updated? ".$saveOut);

		if($saveOut) {
			Yii::$app->getSession()->setFlash('success', 'Visitor has been Checked out');
		} else {
			Yii::$app->getSession()->setFlash('error', 'Failed to Check out');
		}
		return $this->redirect(['/guest/index']);
	}

	public function actionStats() {
		$searchModel = new GuestSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('stats', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionStickyForm($type) {
		yii::$app->controller->createLog(false, 'trex_C_GC:163', var_export('her',true));
		if($type=='true') {
			$session = Yii::$app->session;
			$session->set('stickyForm', 'true');
			$responce = ['status'=>true,];
			return json_encode($responce,true);
		}
		else if($type=='false') {
			$session = Yii::$app->session;
			$session->set('stickyForm', 'false');
			$responce = ['status'=>false,];
			return json_encode($responce,true);
		}
	}

	public function actionUpdate($id) {
		$model = $this->findModel($id);

		if ($model->load(Yii::$app->request->post())) {
			$guest = Guest::find()->where(['id'=>$model->id])->one();
			$guest->badge_number = $model->badge_number;
			$guest->g_first_name = trim($model->g_first_name);
			$guest->g_last_name = trim($model->g_last_name);
			$guest->g_city = trim($model->g_city);
			$guest->g_state = strtoupper(trim($model->g_state));
			$guest->g_yob = $model->g_yob;
			$guest->tmp_badge = trim($model->tmp_badge);
			$guest->time_in = date('Y-m-d H:i:s',strtotime($model->time_in));
			if(!isset($guest->guest_count)) $guest->guest_count = 1;
			if(!isset($guest->payment_type)) $guest->payment_type = 'cash';

			if($guest->save()) {
				$sql = "update guest set g_paid ='$model->g_paid' where id = $model->id";
				$cmd = Yii::$app->getDb()->createCommand($sql)->execute();
				Yii::$app->getSession()->setFlash('success', 'Visitor has been updated');
				return $this->redirect(['/guest/index']);
			} else {
				yii::$app->controller->createLog(true, 'trex_C_GC:171 Save error', var_export($model->errors,true));
				Yii::$app->getSession()->setFlash('error', 'Failed to update record');
				return $this->render('update', ['model' => $model,]);
			}
		} else {
			return $this->render('update', [
				'model' => $model,
			]);
		}
	}

	public function actionView($id) {
		$model = new \backend\models\Guest();
		return $this ->render('create', [
			'model' => $this->findModel($id),
		]);
	}

	protected function findModel($id) {
		if (($model = Guest::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}
