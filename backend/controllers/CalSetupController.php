<?php

namespace backend\controllers;

use Yii;
use backend\controllers\AdminController;
use backend\models\agcClubs;
use backend\models\agcFacility;
use backend\models\agcRangeStatus;
use backend\models\agcEventStatus;
use backend\models\search\agcClubsSearch;
use backend\models\search\agcEventStatusSearch;
use backend\models\search\agcFacilitySearch;
use backend\models\search\agcRangeStatusSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


class CalSetupController extends AdminController {
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

	public function actionClubs() {
		$searchModel = new agcClubsSearch();
		
		if(isset($_REQUEST['reset'])) {
			unset($_SESSION['CalSetupActive']);
			unset($_SESSION['CalSetupCio']);
			unset($_SESSION['CalSetupAdm']);
			unset($_SESSION['CalSetupBadm']);
			return $this->redirect(['index']);
		} else {
			if(isset($_REQUEST['agcClubsSearch']['active'])) { 
				$searchModel->active = $_REQUEST['agcClubsSearch']['active'];	
				$_SESSION['CalSetupActive'] = $_REQUEST['agcClubsSearch']['active'];
			} elseif (isset($_SESSION['CalSetupActive'])) {
				$searchModel->active = $_SESSION['CalSetupActive'];	
			} else { $searchModel->active=1; }
			
			if(isset($_REQUEST['agcClubsSearch']['display_in_administration'])) { 
				$searchModel->display_in_administration = $_REQUEST['agcClubsSearch']['display_in_administration'];	
				$_SESSION['CalSetupAdm'] = $_REQUEST['agcClubsSearch']['display_in_administration'];
			} elseif (isset($_SESSION['CalSetupAdm'])) {
				$searchModel->display_in_administration = $_SESSION['CalSetupAdm'];	
			} //else { $searchModel->display_in_administration=1; }
			
			if(isset($_REQUEST['agcClubsSearch']['display_in_badges_administration'])) { 
				$searchModel->display_in_badges_administration = $_REQUEST['agcClubsSearch']['display_in_badges_administration'];	
				$_SESSION['CalSetupBadm'] = $_REQUEST['agcClubsSearch']['display_in_badges_administration'];
			} elseif (isset($_SESSION['CalSetupBadm'])) {
				$searchModel->display_in_badges_administration = $_SESSION['CalSetupBadm'];	
			} //else { $searchModel->display_in_badges_administration=0; }
			
			if(isset($_REQUEST['agcClubsSearch']['is_cio'])) { 
				$searchModel->is_cio = $_REQUEST['agcClubsSearch']['is_cio'];	
				$_SESSION['CalSetupCio'] = $_REQUEST['agcClubsSearch']['is_cio'];
			} elseif (isset($_SESSION['CalSetupCio'])) {
				$searchModel->is_cio = $_SESSION['CalSetupCio'];	
			} //else { $searchModel->is_cio=0; }
		}

		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('Clubs', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionCreate() {
		$model = new agcFacility();

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

		return $this->redirect(['index']);
	}

	public function actionIndex() {
		return $this->redirect(['facility']);
	}

	public function actionFacility() {
		$searchModel = new agcFacilitySearch();
		$this->RestoreSession($searchModel,'agcClubsSearch');
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('facility', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionRangestatus() {
		$searchModel = new agcRangeStatusSearch();
		$this->RestoreSession($searchModel,'agcClubsSearch');
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('RangeStatus', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionEventstatus() {
		$searchModel = new agcEventStatusSearch();
		$this->RestoreSession($searchModel,'agcClubsSearch');
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('EventStatus', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}	



	public function actionUpdateclu($id) {
		$model = agcClubs::findOne($id);

		if ($model->load(Yii::$app->request->post())) {
			if($model->save()) {
				Yii::$app->getSession()->setFlash('success', 'Club has been updated.');
			} else { Yii::$app->getSession()->setFlash('error', 'Update Failed.');}
			return $this->redirect(['updateclu', 'id' => $model->club_id]);
		} else {
			return $this->render('updateclu', [
				'model' => $model,
			]);
		}
	}

	public function actionUpdateeven($id) {
		$model = agcEventStatus::findOne($id);

		if ($model->load(Yii::$app->request->post())) {
			if($model->save()) {
				Yii::$app->getSession()->setFlash('success', 'Event Status has been updated.');
			} else { Yii::$app->getSession()->setFlash('error', 'Update Failed.');}
			return $this->redirect(['updateeven', 'id' => $model->event_status_id]);
		} else {
			return $this->render('updateeven', [
				'model' => $model,
			]);
		}
	}

	public function actionUpdatefac($id) {
		$model = agcFacility::findOne($id);

		if ($model->load(Yii::$app->request->post())) {
			 if($model->save()) {
				Yii::$app->getSession()->setFlash('success', 'Facility has been updated.');
			} else { Yii::$app->getSession()->setFlash('error', 'Update Failed.');}
			return $this->redirect(['updatefac', 'id' => $model->facility_id]);
		} else {
			return $this->render('updatefac', [
				'model' => $model,
			]);
		}
	}

	public function actionUpdateran($id) {
		$model = agcRangeStatus::findOne($id);

		if ($model->load(Yii::$app->request->post())) {
			if($model->save()) {
				Yii::$app->getSession()->setFlash('success', 'Range Status has been updated.');
			} else { Yii::$app->getSession()->setFlash('error', 'Update Failed.');}
			return $this->redirect(['updateran', 'id' => $model->range_status_id]);
		} else {
			return $this->render('updateran', [
				'model' => $model,
			]);
		}
	}

	public function RestoreSession($searchModel,$search) {

		if(isset($_REQUEST['reset'])) {
			unset($_SESSION['CalSetupActive']);
			unset($_SESSION['CalSetupCio']);
			unset($_SESSION['CalSearchevent_name']);
			unset($_SESSION['CalSearchapproved']);
			return $this->redirect(['index']);
		} else {
			if(isset($_REQUEST[$search]['active'])) { 
				$searchModel->active = $_REQUEST[$search]['active'];	
				$_SESSION['CalSetupActive'] = $_REQUEST[$search]['active'];
			} elseif (isset($_SESSION['CalSetupActive'])) {
				$searchModel->active = $_SESSION['CalSetupActive'];	
			} else { $searchModel->active=1; }
		}

	}
}
