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
		
		if ($model->load(Yii::$app->request->post())) {
			$model->vi_rules = implode(", ",$model->vi_rules);
			if(isset($model->badge_reporter)) {$model->badge_reporter = ltrim($model->badge_reporter, '0'); }
			if(isset($model->badge_involved)) {$model->badge_involved = ltrim($model->badge_involved, '0'); }
			if(isset($model->badge_witness)) {$model->badge_witness = ltrim($model->badge_witness, '0'); }
			if(isset($model->vi_sum)) {$model->vi_sum = trim(preg_replace('/\r\n?/', " ", $model->vi_sum)); }
			if(isset($model->vi_report)) {$model->vi_report = trim(preg_replace('/\r\n?/', " ", $model->vi_report)); }
			if(isset($model->vi_action)) {$model->vi_action = trim(preg_replace('/\r\n?/', " ", $model->vi_action)); }
			if(isset($model->hear_sum)) {$model->hear_sum = trim(preg_replace('/\r\n?/', " ", $model->hear_sum)); }
			
			if($model->save()) {
				Yii::$app->getSession()->setFlash('success', 'Violation has been saved!');
				if($model->vi_type==4){
					$member = (new Badges)->findOne(['badge_number'=>$model->badge_involved]);
					$member->status='suspended';
					$nowRemakrs = ['created_at'=>yii::$app->controller->getNowTime(), 'data'=>'Badge Suspended ', 'changed'=> 'Suspender by '.$_SESSION['user'], ];
					$remarksOld = $member->remarks;
					if($remarksOld != '') {
						$remarksOld = json_decode($remarksOld);
						array_push($remarksOld,$nowRemakrs);
					} else {
						$remarksOld = [	$nowRemakrs, ];
					}
					$member->remarks = json_encode($remarksOld,true);
					
					$member->save(false);
				}
				//$violations = Violations::find()->where(['id'=>$model->id])->one();
				$this->createLog($this->getNowTime(), $_SESSION['user'], 'Logged Range Violation for: '.$model->badge_involved);

                return $this->redirect(['view', 'id' => $model->id]);   
				
			} else {
				Yii::$app->getSession()->setFlash('error', 'action create - no save?');
				return $this->redirect(['/violations/index']);
			}
		} 
		else {
			return $this->render('create', [
				'model' => $model,
				//'id' => $violationsID
			 
			]);
		}
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

	protected function findModel($id) {
		if (($model = Violations::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}
