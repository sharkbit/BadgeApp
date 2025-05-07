<?php

namespace backend\controllers;

use yii;
use backend\controllers\SiteController;
use backend\models\Badges;
use backend\models\Violations;
use backend\models\search\ViolationsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


/**
 * ViolationsController implements the CRUD actions for Violations model.
 */
class ViolationsController extends SiteController {

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
		$model = new Violations();
		if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
			Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
			return yii\widgets\ActiveForm::validate($model);
		}
		elseif ($model->load(Yii::$app->request->post())) {
			$model->badge_reporter = $_SESSION['badge_number'];
			$model->vi_date = date('Y-m-d H:i:s',strtotime($model->vi_date));
			$model->vi_rules = implode(", ",$model->vi_rules);
			$model->vi_sum = trim($model->vi_sum);
			$model->vi_report = trim($model->vi_report);
			$model->vi_action = trim($model->vi_action);
			if(isset($model->hear_sum)) { $model->hear_sum = trim($model->hear_sum); }
			if($model->hear_date) {
				$model->hear_date = date('Y-m-d H:i:s',strtotime($model->hear_date));
			}

			if($model->save()) {
				// Increment violation count and get current status
				ViolationStatus::incrementViolation($model->badge_involved, $model->was_guest == 1);
				$currentStatus = ViolationStatus::findOne(['badge_number' => $model->badge_involved]);
				
				// Prepare notification message based on status
				$notificationMessage = '';
				if ($currentStatus) {
					switch ($currentStatus->status) {
						case ViolationStatus::STATUS_WARNING:
							$notificationMessage = $model->was_guest ? 
								'Guest has received a warning for their first violation.' :
								'Member has received a warning for their first violation.';
							break;
						case ViolationStatus::STATUS_ESCALATED:
							$notificationMessage = 'Member has received an escalated warning for their second violation. Admin contact is required.';
							break;
						case ViolationStatus::STATUS_BLOCKED:
							$notificationMessage = $model->was_guest ?
								'Guest has been blocked for 30 days due to multiple violations.' :
								'Member has been blocked for 30 days due to three violations.';
							break;
					}
				}

				// Add notification message to flash
				if ($notificationMessage) {
					Yii::$app->getSession()->setFlash('warning', $notificationMessage);
				}

				// If violation type is 4 or status is blocked, update badge status
				if ($model->vi_type == 4 || ($currentStatus && $currentStatus->status === ViolationStatus::STATUS_BLOCKED)) {
					$badge = Badges::findOne(['badge_number' => $model->badge_involved]);
					if ($badge) {
						$badge->status = 'suspended';
						$badge->remarks = $badge->remarks ? 
							$badge->remarks . "\n" . date('Y-m-d') . " - Suspended due to violation" :
							date('Y-m-d') . " - Suspended due to violation";
						$badge->save();
					}
				}

				$this->createLog($this->getNowTime(), $_SESSION['user'], 'Created Violation: '.$model->id.' For Badge: '.$model->badge_involved);
				return $this->redirect(['view', 'id' => $model->id]);
			}
		}
		return $this->render('create', [
			'model' => $model,
		]);
	}

	public function actionReport() {
		$searchModel = new ViolationsSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('report', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

    public function actionDelete($id) {
		if($this->findModel($id)->delete()) {
            $this->createLog($this->getNowTime(), $_SESSION['user'], 'Deleted Violations: '.$id);
        }
		return $this->redirect(['index']);
    }

	public function actionIndex() {
		\backend\controllers\RsoRptController::OpenReport();
		$searchModel = new ViolationsSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionStats() {
		$searchModel = new ViolationsSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('stats', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}
	
	public function actionUpdate($id) {
		$model = $this->findModel($id);

		if ($model->load(Yii::$app->request->post())) {
			$violations = Violations::find()->where(['id'=>$model->id])->one();

			$violations->badge_reporter = $model->badge_reporter;
			$violations->badge_involved = $model->badge_involved;
			$violations->badge_witness = $model->badge_witness;
			$violations->vi_loc = $model->vi_loc;
			$violations->vi_type = $model->vi_type;
			$violations->vi_override = $model->vi_override;
			$violations->vi_sum = trim($model->vi_sum);
			$violations->vi_rules = implode(", ",$model->vi_rules);
			$violations->vi_report = trim($model->vi_report);
			$violations->was_guest = $model->was_guest;
			$violations->vi_action = trim($model->vi_action);
			if(isset($model->hear_sum)) { $violations->hear_sum = trim($model->hear_sum); }
			
			//$violations->vi_date = date('Y-m-d H:i:s',strtotime($model->vi_date));
			if($model->hear_date) {
				$violations->hear_date = date('Y-m-d H:i:s',strtotime($model->hear_date));
			}
			
			if($violations->save()) {
				Yii::$app->getSession()->setFlash('success', 'Violation has been updated');
				return $this->render('view', [ 'model' => $this->findModel($id), ]);
			} else {
				Yii::$app->getSession()->setFlash('error', 'Failed to update violation'); 
				return $this->render('update', ['model' => $model,]);
			}
		} else {
			return $this->render('update', [
				'model' => $model,
			]);
		}
	}

	public function actionView($id) {
		$model = new \backend\models\Violations();
		//return $this ->render('create', [ 'model' => $this->findModel($id), ]);
		return $this ->render('view', [ 'model' => $this->findModel($id), ]);
	}

	public static function getViolationsList() {
		$sql = "SELECT rule_abrev,vi_type,rule_name from rule_list";
		$command = Yii::$app->db->createCommand($sql);
		$myViol = $command->queryAll();

		foreach($myViol as $id){
			$myViol_lst[$id['rule_abrev'].'-C'.$id['vi_type']] = $id['rule_name'];
		}
		return $myViol_lst;
	}

	public function actionGuestViolations()
	{
		$searchModel = new \backend\models\search\ViolationsSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		
		// Filter to show only guest violations
		$dataProvider->query->andWhere(['was_guest' => 1]);
		
		return $this->render('guest-violations', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	protected function findModel($id) {
		if (($model = Violations::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}
