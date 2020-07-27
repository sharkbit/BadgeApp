<?php

namespace backend\controllers;

use Yii;
use backend\models\WorkCredits;
use backend\models\search\WorkCreditsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\controllers\SiteController;
use backend\models\Badges;

class WorkCreditsController extends SiteController {

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

	public function actionApprove($id) {
		$model = $this->findModel($id);
		$model->status=1;
		$model->authorized_by=$_SESSION['user'];
		$model->updated_at=$this->getNowTime();
		//$model->remarks=$model->remarks.". Approved By ".$_SESSION['user'];
		$model->save();
		if($_SERVER['HTTP_REFERER']) {
			$this->redirect($_SERVER['HTTP_REFERER']);
		} else {
			return $this->redirect(['index', 'type' => 'pen']); 
		}

	}

    public function actionCreate() {
		$model = new WorkCredits();
        if(isset($_GET['badge_number'])) {
            $model->badge_number = $_GET['badge_number'];
        }

        if ($model->load(Yii::$app->request->post())) {
            $session = Yii::$app->session;
            $sticky = $session->get('stickyForm');
            if($sticky=='true') {
                $stickyData = [
                    'work_date'=> date('Y-m-d',strtotime($model->work_date)),
                    'authorized_by' => $model->authorized_by,
                    'work_hours' => $model->work_hours,
                    'project_name' => $model->project_name,
                    'remarks' => $model->remarks,
					'supervisor'=> $model->supervisor
                ];
                $session->set('stickyData',$stickyData);
            }

            else if($sticky=='false') {
               $stickyData = [];
               $session->set('stickyData',$stickyData);
            }

            $model->work_date = date('Y-m-d',strtotime($model->work_date));
            $model->status = '0';
            $model->created_at = $this->getNowTime();
            $model->updated_at = $this->getNowTime();
            $model->created_by = $_SESSION['badge_number'];

			if($model->save()) {
				$badge = Badges::find()->where(['badge_number'=>$model->badge_number])->one();
				$badge->work_credits = $badge->work_credits + $model->work_hours;
				if($badge->save(false)) {
					$model->status = '2';
					$model->save();
				}
				$this->createLog($this->getNowTime(), $_SESSION['user'], 'Created Work Credits : '.$model->id);
				Yii::$app->getSession()->setFlash('success', 'Work Credit has been added');
				$sticky = $session->get('stickyForm');
				if($sticky=='true') {
					return $this->redirect(['/work-credits/create']);
				}
				else if($sticky=='false') {
					return $this->redirect(['view', 'id' => $model->id]);
				}
				return $this->redirect(['view', 'id' => $model->id]);
			} else {
				yii::$app->controller->createLog(true, 'trex_C_WCC', var_export($model->getErrors()));
				var_export($model->errors);
			}
        }
        else {
            if(isset($_GET['badge_number'])) {
				if(!yii::$app->controller->hasPermission('work-credits/approve') && $_GET['badge_number']!=$_SESSION['badge_number']) {
					return $this->redirect(['create', 'badge_number' => $_SESSION['badge_number']]);
				}
                $badgeArray = Badges::find()->where(['badge_number'=> $_GET['badge_number']])->one();
            }
            else {
                $badgeArray = null;
            }

            return $this->render('create', [
                'model' => $model,
                'badgeArray' =>$badgeArray,
            ]);
        }
    }

    public function actionDelete($id) {
        $deleteModel = WorkCredits::findOne($id);
		if($deleteModel->delete()) {
			$this->createLog($this->getNowTime(), $_SESSION['user'], 'Work Credit Deleted: '.$deleteModel->work_hours.' hours from '.$deleteModel->badge_number);
			Yii::$app->getSession()->setFlash('success', 'Work Credit has been deleted');
			return $this->redirect(['index']);
		}
    }

    public function actionIndex() {
		$searchModel = new WorkCreditsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		$session = Yii::$app->session;
		$stickyData = [];
		$session->set('stickyData',$stickyData);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

    public function actionView($id) {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionStickyForm($type) {
        if($type=='true') {
            $session = Yii::$app->session;
            $session->set('stickyForm', 'true');
            $responce = [
                'status'=>true,
            ];

            return json_encode($responce,true);
        }
        else if($type=='false') {
            $session = Yii::$app->session;
            $session->set('stickyForm', 'false');
            $responce = [
                'status'=>false,
            ];

            return json_encode($responce,true);
        }
    }

    public function actionTransferForm() {
        $model = new WorkCredits();

        if ($model->load(Yii::$app->request->post())) {
			if(isset($_POST['wc-Radio'])) {
				if($_POST['wc-Radio']=='last') {
					$credits_from = $_POST['total_credit_last'];
					$SetDate = date('Y-12-31', strtotime("-1 years",strtotime($this->getNowTime())));
				} else {
					$credits_from = $_POST['total_credit_this'];
					$SetDate = date('Y-m-d',strtotime($this->getNowTime()));
				}
				$credits_to = $_POST['to_credits'];
				if(($credits_from >= $credits_to) && ($credits_to > 0)) {
					// Process
					$model->work_hours = -$credits_to;
					$model->work_date = $SetDate;
					$model->project_name = "Transfer Credits";
					$tmpRemarks = $model->remarks;
					$model->remarks = "Gift transfer to ".$_POST['to_badge_name']." (".$_POST['to_badge_number'].") ".$tmpRemarks;
					$badge_from = $model->badge_number;

					if(yii::$app->controller->hasPermission('work-credits/approve')) {
						$auth_by=$_SESSION['user'];
						$stat = 1;
					} else {
						$auth_by='';
						$stat = 2;
					}
					$model->authorized_by = $auth_by;
					$model->supervisor = $_SESSION['user'];
					$model->status = $stat;

					$model->created_by = $_SESSION['badge_number'];
					$model->created_at = $this->getNowTime();
					$model->updated_at = $this->getNowTime();

					if($model->validate()) {
						$model->save();
						$model = new WorkCredits;

						$model->badge_number = $_POST['to_badge_number'];
						$model->work_hours = $credits_to;
						$model->work_date = $SetDate;
						$model->project_name = "Transfer Credits";
						$model->remarks = "Gift transfer from ".$_POST['badge_name']." (".$badge_from.") ".$tmpRemarks;
						$model->authorized_by = $auth_by;
						$model->supervisor = $_SESSION['user'];
						$model->status = $stat;
						$model->created_by = $_SESSION['badge_number'];
						$model->created_at = $this->getNowTime();
						$model->updated_at = $this->getNowTime();
						if($model->validate()) {
							$model->save();
							$this->createLog($this->getNowTime(), $_SESSION['user'], 'Credit Transfer Request Initiated '.$badge_from.' to '.$_POST['to_badge_number']);
							Yii::$app->getSession()->setFlash('success', 'credit transfer request has been initiated');
						} else {
							yii::$app->controller->createLog(true, 'trex-WCC', "broke at 2 ".var_export($model,true));
							return "Controller - WorkCred B ".var_export($model->getErrors());
						}
					} else {
						yii::$app->controller->createLog(true, 'trex-WCC', "broke at 1 ".var_export($model,true));
						return "Controller - WorkCred A ".var_export($model->getErrors());
					}
					return $this->redirect(['index']);

				}
				else {
					//not enough credits....  Naughty.....
					return $this->render('transfer-form',['model'=>$model,]); }
			} else { return $this->render('transfer-form',['model'=>$model,]); }
		} else { return $this->render('transfer-form',['model'=>$model,]); }
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $activeUser = $this->getActiveUser();
            $badge = Badges::find()->where(['badge_number'=>$model->badge_number])->one();

            $badge->work_credits = $badge->work_credits - $model->work_hours;
            if($badge->save(false)) {
                $model->work_hours = $model->work_hours_new;
                $model->work_date = date('Y-m-d',strtotime($model->work_date));
                $model->updated_at = $this->getNowTime();
                $model->created_by = $_SESSION['badge_number'];
                if($model->save()) {

                    $badge->work_credits = $badge->work_credits + $model->work_hours;
                    if($badge->save(false)) {
                        $model->status = '1';
                        $model->save();
                        $this->createLog($this->getNowTime(), $_SESSION['user'], 'Updated Work Credits : '.$model->id);
                        Yii::$app->getSession()->setFlash('success', 'Work Credit has been updated');
                        return $this->redirect(['view', 'id' => $model->id]);
                    }

                }
            }

        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    protected function badgeIsExist($badge_number) {
        $badges  = Badges::find()->where(['badge_number'=>$badge_number])->one();
        if(!empty($badges)) {
            return true;
        }
        else {
            return false;
        }
    }

    protected function findModel($id) {
        if (($model = WorkCredits::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
