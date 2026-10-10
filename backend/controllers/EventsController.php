<?php

namespace backend\controllers;

use Yii;
use backend\controllers\AdminController;
use backend\models\Badges;
use backend\models\AgcCal;
use backend\models\Events;
use backend\models\Event_Att;
use backend\models\Params;
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
                    'issue-credit' => ['POST'],
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
		$event_attendee = Event_Att::find()->where(['ea_calendar_id'=>$id,'ea_wb_serial'=>$wb])->one();
		if($event_attendee){
			$event_attendee->ea_wb_out = false;
			$event_attendee->save();
		}
		return $this->redirect(['view', 'id' => $id]);
	}

	public function actionIssueCredit($id,$auto=null) {

		$event_issue = Events::find()->where(['ea_calendar_id'=>$id])->one();
		if($event_issue){
			$eventDate = strtotime($event_issue->event_date);
			$today = strtotime(date('Y-m-d', strtotime($this->getNowTime())));
			if ($eventDate === false || $eventDate >= $today) {
				Yii::$app->session->setFlash('error', 'Credit can only be issued after the event date.');
				return $this->redirect(['view', 'id' => $event_issue->ea_calendar_id]);
			}
			$calendar = AgcCal::findOne(['calendar_id' => $id]);
			if (!$calendar) {
				Yii::$app->session->setFlash('error', 'Calendar event could not be found.');
				return $this->redirect(['view', 'id' => $event_issue->ea_calendar_id]);
			}
			if ($calendar->issued_vol) {
				Yii::$app->session->setFlash('warning', 'Credit has already been issued for this event.');
				return $this->redirect(['view', 'id' => $event_issue->ea_calendar_id]);
			}

			if($event_issue->is_volunteer==true) {
				$event_attendee = Event_Att::find()->where(['ea_calendar_id'=>$id])->all();
				if(count($event_attendee)>0) {
					foreach ($event_attendee as $person) {
						if($person->ea_wc_logged<>1) {
							$time_now = date('Y-m-d H:i:s',strtotime(yii::$app->controller->getNowTime()));

							$att_wc = New WorkCredits;
							$att_wc->badge_number 	= $person->ea_badge;
							$att_wc->work_hours 	= $event_issue->credit_hours;
							$att_wc->project_name	= $event_issue->event_name;
							$att_wc->supervisor		= yii::$app->controller->decodeBadgeName((int)$event_issue->cal_inst);
							$att_wc->remarks	= "Attended Event";
							$att_wc->status		= 2;
							$att_wc->work_date	= $event_issue->event_date;
							$att_wc->updated_at	= $time_now;
							$att_wc->created_at = $time_now;
							$att_wc->created_by	= $_SESSION['badge_number'];
							$att_wc->save();

							$app_per = Event_Att::find()->where(['ea_id'=>$person->ea_id])->one();
							$app_per->ea_wc_logged=1;
							$app_per->save();
						}
					}
				}
				$msg = "Volunteer hours issued for $event_issue->event_name, ".count($event_attendee)." attendees";
			} else {
				$msg = "Event $event_issue->event_name Closed";
			}

			if ($event_issue->save()) {
				$myRemarks = [
					'created_at'=>yii::$app->controller->getNowTime(),
					'changed'=> "Work Credits Issued",
					'data'=> $_SESSION['user']. " issued $event_issue->credit_hours work credits for ".count($event_attendee)." Attendees."
				];
				$NewRemarks = yii::$app->controller->mergeRemarks($calendar->remarks, $myRemarks);
				AgcCal::updateAll(['issued_vol' => 1,'remarks' => $NewRemarks], ['calendar_id' => $id]);
			}

			$this->createLog($this->getNowTime(), $_SESSION['user'], $msg);
			Yii::$app->session->setFlash('success', $msg);
			return $this->redirect(['view', 'id' => $event_issue->ea_calendar_id]);
		}
		Yii::$app->session->setFlash('error', "Event not found");
		return $this->redirect(['index']);
	}

	public function actionDelete($id) {
		$att_chk = Event_Att::find()->where(['ea_calendar_id'=>$id])->all();
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
	//	$Close_Events=Events::find('e_id','e_name')->where(['<','e_date',date('Y-m-d',strtotime(yii::$app->controller->getNowTime()))])->andwhere(['e_status'=>0])->all();
	//	if($Close_Events) {
	//		yii::$app->controller->createLog(true, 'System', "Closing ".count($Close_Events)." Events");
	//		foreach ($Close_Events as $c_event) {
	//			$this->actionIssueCredit($c_event->e_id,true);
	//		}
	//	}

		$searchModel = new EventsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		if(!yii::$app->controller->hasPermission('events/approve')) {
		//	$dataProvider->query->andWhere("e_poc=".$_SESSION["badge_number"]);
		}

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider ]);
    }

    public function actionReg($id,$badge=null,$f_name=null,$l_name=null,$e_wb=null) {
		$isFormPost = Yii::$app->request->isPost && !Yii::$app->request->isAjax;
		$success = false;
		$message = 'Registration failed.';

		if($badge>0){
			$params = Params::findOne('1');
			$isExpired = Badges::isExpired($badge,$params);
			if(!$isExpired) {

				$event_chk = Event_Att::find()->where(['ea_calendar_id'=>$id,'ea_badge'=>$badge])->one();
				if($event_chk) {
					$message = 'Badge already at Event.';
				} else {
					$event_attendee = new Event_Att;
					$event_attendee->ea_calendar_id=$id;
					$event_attendee->ea_badge=$badge;
					if ($event_attendee->save()) {
						$success = true;
						$message = 'Added Badge to Event.';
					} else {
						$message = 'Could not add badge to event.';
					}
				}
			} else {
				$message = 'Not an Active Member.';
			}
		} else {
			if (trim((string) $f_name) === '' || trim((string) $l_name) === '') {
				$message = 'Please provide both first and last name.';
			} else {
				$f_name = ucfirst(trim($f_name));
				$l_name = ucfirst(trim($l_name));
				$event_chk = Event_Att::find()->where(['ea_calendar_id'=>$id,'ea_f_name'=>$f_name,'ea_l_name'=>$l_name,])->one();
				if($event_chk) {
					$message = $f_name.' already at Event.';
				} else {
					$event_attendee = new Event_Att;
					$event_attendee->ea_calendar_id=$id;
					if($e_wb) { $event_attendee->ea_wb_serial = $e_wb; }
					$event_attendee->ea_f_name=$f_name;
					$event_attendee->ea_l_name=$l_name;
					if ($event_attendee->save()) {
						$success = true;
						$message = $f_name.' added to Event.';
					} else {
						$message = 'Could not add '.$f_name.' to event.';
					}
				}
			}
		}

		if ($isFormPost) {
			Yii::$app->getSession()->setFlash($success ? 'success' : 'error', $message);
			return $this->redirect(['/events/index']);
		}

		return json_encode(['success' => $success, 'msg' => $message], true);
	}

	public function actionRemoveAtt($id,$ea_id) {
		Event_Att::deleteall(['ea_calendar_id'=>$id,'ea_id'=>$ea_id]);
		Yii::$app->response->data = json_encode(['success'=>true]);
	}

    public function actionView($id) {
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
    }

    protected function findModel($id) {
        if (($model = Events::findOne(['ea_calendar_id'=>$id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
