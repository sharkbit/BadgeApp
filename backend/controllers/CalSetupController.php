<?php

namespace backend\controllers;

use Yii;
use backend\controllers\AdminController;
use backend\models\agcFacility;
use backend\models\agcRangeStatus;
use backend\models\agcEventStatus;
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
	 
	public $myFilters = ['active','available_lanes','is_cio','display_in_administration','display_in_badges_administration','name'];

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
		$this->RestoreSession($searchModel,'agcFacility',$this->myFilters);
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('facility', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionRangestatus() {
		$searchModel = new agcRangeStatusSearch();
		$this->RestoreSession($searchModel,'agcRangeStatus',$this->myFilters);
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('RangeStatus', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionEventstatus() {
		$searchModel = new agcEventStatusSearch();
		$this->RestoreSession($searchModel,'agcEventStatus',$this->myFilters);
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('EventStatus', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
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
}
