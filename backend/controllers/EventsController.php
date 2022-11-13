<?php

namespace backend\controllers;

use Yii;
use backend\controllers\AdminController;
use backend\models\Badges;
use backend\models\Events;
use backend\models\Event_Att;
use backend\models\search\EventsSearch;
use backend\models\WorkCredits;

use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * ParamsController implements the CRUD actions for Params model.
 */
class EventsController extends AdminController {
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

	public function actionApprove($id,$auto=null) {

		$event_approve = Events::find()->where(['e_id'=>$id])->one();
		if($event_approve){
			$event_approve->e_rso=$_SESSION['badge_number'].'|'.date('Y-m-d H:i:s',strtotime(yii::$app->controller->getNowTime()));
			$event_approve->save();
			$responce = ['status'=>true,];
			return json_encode($responce,true);
		}
	}

	public function actionReturn($id,$wb) {
		$event_attendee = Event_Att::find()->where(['ea_event_id'=>$id,'ea_wb_serial'=>$wb])->one();
		if($event_attendee){
			$event_attendee->ea_wb_out = false;
			$event_attendee->save();
		}
		return $this->redirect(['view', 'id' => $id]);
	}

	public function actionClose($id,$auto=null) {

		$event_close = Events::find()->where(['e_id'=>$id])->one();
		if($event_close){
			If($auto) { $c_user="System"; $c_badge=0;
			} else { $c_user=$_SESSION['user']; $c_badge=$_SESSION['badge_number']; }

			if($event_close->e_type=='vol'){
				$event_attendee = Event_Att::find()->where(['ea_event_id'=>$id])->all();
				if(count($event_attendee)>0) {
					foreach ($event_attendee as $person) {
						if($person->ea_wc_logged<>1) {
							$time_now = date('Y-m-d H:i:s',strtotime(yii::$app->controller->getNowTime()));

							$att_wc = New WorkCredits;
							$att_wc->badge_number 	= $person->ea_badge;
							$att_wc->work_hours 	= $event_close->e_hours;
							$att_wc->project_name	= $event_close->e_name;
							$att_wc->supervisor		= yii::$app->controller->decodeBadgeName((int)$event_close->e_poc);
							$att_wc->remarks	= "Attended Event";
							$att_wc->status		= 2;
							$att_wc->work_date	= $event_close->e_date;
							$att_wc->updated_at	= $time_now;
							$att_wc->created_at = $time_now;
							$att_wc->created_by	= $c_badge;
							$att_wc->save();

							$app_per = Event_Att::find()->where(['ea_id'=>$person->ea_id])->one();
							$app_per->ea_wc_logged=1;
							$app_per->save();
						}
					}
				}
				$msg = "Event $event_close->e_name Closed, ".count($event_attendee)." attendees";
			} else {
				$msg = "Event $event_close->e_name Closed";
			}
			if($event_close->e_rso) {
				$event_close->e_rso .="+".$c_badge.'|'.date('Y-m-d H:i:s',strtotime(yii::$app->controller->getNowTime()));
			} else {
				$adm_close = $c_badge.'|'.date('Y-m-d H:i:s',strtotime(yii::$app->controller->getNowTime()));
				$event_close->e_rso = $adm_close .'+'.$adm_close;
			}
			$event_close->e_status=1;
			$event_close->save();

			$this->createLog($this->getNowTime(), $c_user, $msg);
		}
		If($auto) {} else {
			return $this->redirect(['view', 'id' => $event_close->e_id]);}
	}

    public function actionCreate() {
		$model = new Events();
        if ($model->load(Yii::$app->request->post())){

			$model->e_date = date('Y-m-d',strtotime($model->e_date));
			$model->e_name = trim($model->e_name);
			if ($model->save()) {
				$this->createLog($this->getNowTime(), $_SESSION['user'], 'Event Created: '.$model->e_name);
				Yii::$app->getSession()->setFlash('success', 'Event '.$model->e_name.' has been created');
				if(yii::$app->controller->hasPermission('events/update')) {
					return $this->redirect(['update', 'id' => $model->e_id]);
				} else {
					return $this->redirect(['index']);
				}
			} else {
				return $this->render('create', [
					'model' => $model,
				]);
			}
		} else {
			return $this->render('create', [
				'model' => $model,
			]);
		}
    }

	public function actionDelete($id) {
		$att_chk = Event_Att::find()->where(['ea_event_id'=>$id])->all();
		if($att_chk) {
			Yii::$app->getSession()->setFlash('error', "Can not delete Event with Attendees.");
		} else {
			$del_event = Events::find()->where(['e_id'=>$id])->one();
			$this->createLog($this->getNowTime(), $_SESSION['user'], 'Event Deleted: '.$del_event->e_name);
			Yii::$app->getSession()->setFlash('success', "Deleted Event: ".$del_event->e_name);
			$del_event->delete();
		}
		return $this->redirect('index');
	}

	public function actionIndex() {
		$Close_Events=Events::find('e_id','e_name')->where(['<','e_date',date('Y-m-d',strtotime(yii::$app->controller->getNowTime()))])->andwhere(['e_status'=>0])->all();
		if($Close_Events) {
			yii::$app->controller->createLog(true, 'System', "Closing ".count($Close_Events)." Events");
			foreach ($Close_Events as $c_event) {
				$this->actionClose($c_event->e_id,true);
			}
		}

		$searchModel = new EventsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		if(!yii::$app->controller->hasPermission('events/approve')) {
			$dataProvider->query->andWhere("e_poc=".$_SESSION["badge_number"]);
		}

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider ]);
    }

    public function actionReg($id,$badge=null,$f_name=null,$l_name=null,$e_wb=null) {
		if($badge>0){
			yii::$app->controller->createLog(false, 'trex_C_EC reg', 'badge ');
			$badge_chk = Badges::find()->where(['>','expires',date('Y-02-01',time())])->andwhere(['badge_number'=>$badge])->one();
			if($badge_chk){

				$event_chk = Event_Att::find()->where(['ea_event_id'=>$id,'ea_badge'=>$badge])->one();
				if($event_chk) {
					return json_encode(['success'=>true,'msg'=>'Badge already at Event.'],true);
				} else {
					$event_attendee = new Event_Att;
					$event_attendee->ea_event_id=$id;
					$event_attendee->ea_badge=$badge;
					$event_attendee->save();
					return json_encode(['success'=>true,'msg'=>'Added Badge to Event.'],true);
				}
			} else {
				return json_encode(['success'=>true,'msg'=>'Not an Active Member.'],true);
			}
		} else {
			yii::$app->controller->createLog(false, 'trex_C_EC reg', 'name ');
			$f_name = ucfirst(trim($f_name));
			$l_name = ucfirst(trim($l_name));
			$event_chk = Event_Att::find()->where(['ea_event_id'=>$id,'ea_f_name'=>$f_name,'ea_l_name'=>$l_name,])->one();
			if($event_chk) {
				return json_encode(['success'=>true,'msg'=>$f_name.' already at Event.'],true);
			} else {
				$event_attendee = new Event_Att;
				$event_attendee->ea_event_id=$id;
				if($e_wb) { $event_attendee->ea_wb_serial = $e_wb; }
				$event_attendee->ea_f_name=$f_name;
				$event_attendee->ea_l_name=$l_name;
				$event_attendee->save();

				return json_encode(['success'=>true,'msg'=>$f_name.' added to Event.'],true);
			}
		}
	}

	public function actionRemoveAtt($id,$ea_id) {
		Event_Att::deleteall(['ea_event_id'=>$id,'ea_id'=>$ea_id]);
		Yii::$app->response->data = json_encode(['success'=>true]);
	}

    public function actionUpdate($id=0) {
        $model = Events::find()->where(['e_id'=>$id])->one();

		$gotoClosed=false;
		$Status_old=$model->e_status;
        if ($model->load(Yii::$app->request->post())) {
			$model->e_date = date('Y-m-d',strtotime($model->e_date));
			$model->e_name = trim($model->e_name);

			if($Status_old <> $model->e_status) {
				if(($model->e_status==0) && (strpos($model->e_rso,'+'))) { //is now open
					$model->e_rso = substr($model->e_rso,0,strpos($model->e_rso,'+')-1);
				} elseif($model->e_status==1){ //is now closed
					$gotoClosed=true;
				}
			}

        	$model->save();
			if($gotoClosed) {
				$this->actionClose($model->e_id);
			} else {
				return $this->redirect(['update', 'id' => $model->e_id]);
			}
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionView($id) {

		if (Yii::$app->request->post()) {
			$model_ea = new Event_Att();
			$model_ea->load(Yii::$app->request->post());
			if($model_ea->ea_badge > 0) {
				//check
				$badge_chk = Badges::find()->where(['>','expires',date('Y-02-01',time())])->andwhere(['badge_number'=>$model_ea->ea_badge])->one();
				if($badge_chk){
					$model_chk = Event_Att::find()->where(['ea_event_id'=>$id,'ea_badge'=>$model_ea->ea_badge])->one();
					if($model_chk) {
						Yii::$app->getSession()->setFlash('error', 'Member Already at Event');
					} else {
						$model_ea->save();
						Yii::$app->getSession()->setFlash('success', 'Member Added to Event');
					}
				} else {
					Yii::$app->getSession()->setFlash('error', 'Not an Active Member');
				}
			}
		}
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
    }

    protected function findModel($id) {
        if (($model = Events::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

