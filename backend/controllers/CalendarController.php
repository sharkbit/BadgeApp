<?php

namespace backend\controllers;

use Yii;
use backend\controllers\AdminController;
use backend\models\AgcCal;
use backend\models\agcEventStatus;
use backend\models\agcFacility;
use backend\models\clubs;
use backend\models\search\AgcCalSearch;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * CalendarController implements the CRUD actions for Calendar model.
 */
class CalendarController extends AdminController {
    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                //	'delete' => ['POST'],
                ],
            ],
        ];
    }

	public function actionApprove($id,$redir='index') {
		$model = $this->findModel($id);
		$model->approved = 1;
		$myRemarks = ['created_at'=>yii::$app->controller->getNowTime(),'data'=>$_SESSION['user']." Approved Event"];

		$model->remarks = yii::$app->controller->mergeRemarks($model->remarks, $myRemarks);
		if($model->save(false)) {
			Yii::$app->getSession()->setFlash('success', 'Approved event id: '.$id);
		} else {Yii::$app->getSession()->setFlash('error', 'it didnt save :(');}
		return $this->redirect([$redir]);
	}

	public function actionCreate() {
		$model = new AgcCal();
		if ($model->load(Yii::$app->request->post())) {
			//yii::$app->controller->createLog(true, 'trex-B_C_CC:50 post', var_export($_POST,true));
			// Save
			$model->start_time 	= date('Y-m-d H:i:s', strtotime("$model->event_date $model->start_time")) ;
			$model->end_time	= date('Y-m-d H:i:s', strtotime("$model->event_date $model->end_time")) ;
			$model->event_date .= ' 00:00:00';
			if(!$model->rollover) { $model->rollover = 0; } else { $model->rollover = $model->rollover; }

			$model->conflict = 0;
			$model->recurrent_calendar_id = 0;
			$model->pattern_type = 0;
			$myRemarks = [
				'created_at'=>yii::$app->controller->getNowTime(),
				'data'=>"Event Created",
				'changed'=> 'Updated by '.$_SESSION['user'],
			];
			$model->remarks = yii::$app->controller->mergeRemarks($model->remarks, $myRemarks);

			if($model->recur_every) {
				if(isset($model->recurrent_start_date)) {
					//echo "Why <br>";
					//exit;
					$model->recurrent_start_date = date('Y-m-d H:i:s',strtotime(date('Y')." ".$model->recurrent_start_date));
					$model->recurrent_end_date = date('Y-m-d H:i:s',strtotime(date('Y')." ".$model->recurrent_end_date));
				}
				$model->recur_week_days = $this->GetPattern($_POST);
				if(($model->recur_week_days==null) && ((int)$model->deleted != 1 )) {
					Yii::$app->getSession()->setFlash('error', 'No Recurring range set');
					return $this->redirect(['create','recur'=>1,'model'=>$model]);
				}
				if (strtotime(yii::$app->controller->getNowTime()) > strtotime(date('Y').'-07-01 00:00:00')) {$myYr= intval(date('Y'))+1;} else {$myYr= date('Y');}
				$myEventDates = $this->getEvents($model->recurrent_start_date,$model->recurrent_end_date,$model->recur_week_days,$myYr);

				if (is_array($myEventDates) && sizeof($myEventDates) >0) {

					// Only pass Future dates when creating
					$nowTS = strtotime($model->event_date); $cdate=0;
					foreach($myEventDates as $DateCheck){
						if (strtotime($DateCheck) <= $nowTS) { unset($myEventDates[$cdate]); }
						$cdate++;
					}
					sort($myEventDates);

					$model->save();
					$model->recurrent_calendar_id = $model->calendar_id;
					$model->event_date = $myEventDates[0];
					if ($this->actionOpenRange($model->event_date,$model->start_time,$model->end_time,$model->facility_id,$model->lanes_requested,$model->calendar_id,$model->recur_week_days,$model->event_status_id,true))
						{ $model->conflict = 0;  $model->approved=1; } else { $model->conflict = 1; $model->approved=1; }
					$model->save();
					$model = $this->createRecCalEvent($model,$myEventDates,false,true);
				} else {
					Yii::$app->getSession()->setFlash('error', 'No events will be created,  Check your dates!');
					return $this->redirect(['create','recur'=>1,'model'=>$model]);
				}
			} else {
				$model->recurrent_start_date = $model->recurrent_end_date = null;

				$model->approved=1;
				$model->recur_every=0;
				$model->save();
			}

			yii::$app->controller->createLog(true, $_SESSION['user'], "Created New Calendar item: ','".$model->calendar_id.'->'.$model->event_name);
			yii::$app->controller->createCalLog(true,  $_SESSION['user'], "Created New Calendar item: ','".$model->calendar_id.'->'.$model->event_name);
			return $this->redirect(['update', 'id' => $model->calendar_id]);
		} else {
			$model->approved = 0;
			$model->active = 0;
			$model->deleted = 0;

			$model->event_status_id=2;
			$model->facility_id=3;
			if(($_SESSION['badge_number']>0) && ($model->poc_badge==0)) { $model->poc_badge=$_SESSION['badge_number']; }
			$model->range_status_id = 1; //Open
		}
		return $this->render('create', [
			'model' => $model,
			]);
	}

	public function actionConflict() {
		$searchModel = new AgcCalSearch();
		$searchModel->conflict = 1;
		$this->RestoreSession($searchModel);
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider ]);
	}

	public function actionDelete($id,$type='s',$redir='index') {
		$model = $this->findModel($id);
		if ($model) {
			if ($type=='s') {
					AgcCal::UpdateAll(['deleted'=>1], "calendar_id = ".$model->calendar_id);
					yii::$app->controller->createCalLog(true,  $_SESSION['user'], "Deleted Calendar item (s): ','".$model->event_name.'('.$model->calendar_id.')');

			} else {
				if ((int)$model->recurrent_calendar_id > 0) {
					AgcCal::UpdateAll(['deleted'=>1], "recurrent_calendar_id = ".$model->calendar_id." AND event_date >= '".date('Y-m-d',strtotime($this->getNowTime()))."'");
					AgcCal::UpdateAll(['deleted'=>1], "calendar_id = ".$model->calendar_id);
					yii::$app->controller->createCalLog(true,  $_SESSION['user'], "Deleted Master Calendar item: ','".$model->event_name.'('.$model->calendar_id.')');

				} else {
					AgcCal::UpdateAll(['deleted'=>1], "calendar_id = ".$model->calendar_id);
					yii::$app->controller->createCalLog(true,  $_SESSION['user'], "Deleted Calendar item: ','".$model->event_name.'('.$model->calendar_id.')');
				}
			}
			Yii::$app->getSession()->setFlash('success', 'Event Deleted.');
			return $this->redirect(['/calendar/'.$redir]);
		}
	}

	public function actionGetEventTypes($event_club_id,$internal=false,$is_sel=false,$is_new_rec=false) {
		$EventClub = (new clubs)::find()->where(['club_id'=>$event_club_id])->one();
		if($EventClub) {
			if ($internal) {$coll_a='event_status_id';$coll_b='name';} else {$coll_a='name';$coll_b='event_status_id';}
			if(($EventClub->is_club=='1') || ($EventClub->is_club=='2')) {
				if (array_intersect([1,2],$_SESSION['privilege'])) {	//if Root or admin
					$ary_event = ArrayHelper::map(agcEventStatus::find()->where(['active'=>1])->orderBy(['name'=>SORT_ASC])->asArray()->all(), $coll_a, $coll_b);
				} elseif (in_array(11,$_SESSION['privilege'])) {		//if Chairmen
					$ary_event = ArrayHelper::map(agcEventStatus::find()->where(['active'=>1])->andwhere('event_status_id not in (4,11,12,18)')->orderBy(['name'=>SORT_ASC])->asArray()->all(), $coll_a, $coll_b);
				} else {
					$ary_event = ArrayHelper::map(agcEventStatus::find()->where(['active'=>1])->andwhere('event_status_id in (1,2,6,13,14,16,19)')->orderBy(['name'=>SORT_ASC])->asArray()->all(), $coll_a, $coll_b);
				}
			} else { // is CIO
				$ary_event = ArrayHelper::map(agcEventStatus::find()->where(['active'=>1])->andwhere(['event_status_id'=>4])->orwhere(['event_status_id'=>19])->orderBy(['name'=>SORT_ASC])->asArray()->all(), $coll_a, $coll_b);
			}
		} else {
			$ary_event=array(['-- no Sponsor --'=>0]);
		}

		$only_CIO=false; $checked_cnt=0;
		foreach ($_SESSION['privilege'] as $priv_chk){
			if (($priv_chk==8) && ($checked_cnt==0)) {$only_CIO=true; } else {$only_CIO=false; }
			$checked_cnt++;
		}

		if ($internal) {
			return $ary_event;
		} else {
			$myOpt='';
			if ((!$is_sel) && ($is_new_rec)) {
				if($only_CIO) {$is_sel = 4;} else {$is_sel=2;}
			}
			foreach($ary_event as $item => $key) {
				if($key==$is_sel) {$isSel='Selected';} else {$isSel='';}
				$myOpt .= "<option value=$key $isSel >$item</option>";
			}
			return json_encode($myOpt);
		}
	}

	public function actionInactive() {
		$searchModel = new AgcCalSearch();
		$searchModel->deleted = 1;
		$this->RestoreSession($searchModel);
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider ]);
	}

    public function actionIndex() {
		$searchModel = new AgcCalSearch();
		$searchModel->deleted = 0;
		$this->RestoreSession($searchModel);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider ]);
    }

	public function actionOpenRange($eDate,$start,$stop,$facility,$lanes=0,$id=0,$pattern,$e_status,$internal=false,$force_order=false,$tst=false) {
		$range = agcFacility::find()->where(['facility_id'=>$facility])->one();
		$start= date('H:i', strtotime($start)+60);
		$stop = date('H:i',strtotime($stop)-60);

		$model = AgcCal::find()->joinWith(['agcRangeStatus'])->joinWith(['agcEventStatus'])
			->where("facility_id=$facility AND event_date='$eDate' AND deleted=0 AND `associat_agcnew`.`agc_calendar`.active=1 and approved=1 AND `associat_agcnew`.`agc_calendar`.`event_status_id` <> 19 AND (".
				"( '$start' BETWEEN time(start_time) AND time(end_time) or '$stop' BETWEEN time(start_time) AND time(end_time) ) OR ".
				"( time(start_time) BETWEEN '$start' AND '$stop' or time(end_time) BETWEEN '$start' AND '$stop'))")
			->all();

		//$model_sql = AgcCal::find()->joinWith(['agcRangeStatus'])->joinWith(['agcEventStatus'])
		//	->where("facility_id=$facility AND event_date='$eDate' AND deleted=0 AND (".
		//		"( '$start' BETWEEN time(start_time) AND time(end_time) or '$stop' BETWEEN time(start_time) AND time(end_time) ) OR ".
		//		"( time(start_time) BETWEEN '$start' AND '$stop' or time(end_time) BETWEEN '$start' AND '$stop'))")
		//	->createCommand()->sql; // echo $model_sql->sql; // exit;
		//yii::$app->controller->createLog(true, 'trex_C_CC:248', $model_sql);
		if ((int)$e_status==18) {$rng_pri=1; }
		else if (($pattern=='daily') || (strpos($pattern,'daily'))) {$rng_pri=5; }
		else if (($pattern=='weekly') || (strpos($pattern,'weekly')))  {$rng_pri=4; }
		else if (($pattern=='monthly') || (strpos($pattern,'monthly'))) {$rng_pri=3; }
		else if (($pattern=='yearly') || (strpos($pattern,'yearly')))  {$rng_pri=2; }
		else {$rng_pri=6; }

		$inPattern=array('chkpat'=>'success');
		if ((!$internal) && (isset($_POST['AgcCal']['recur_every'])) && ($_POST['AgcCal']['recur_every']==1)){
			$real_pattern = $this->GetPattern($_POST);
			if($real_pattern) {
				$myEventDates = $this->getEvents($_POST['AgcCal']['recurrent_start_date'],$_POST['AgcCal']['recurrent_end_date'],$real_pattern,date('Y',strtotime($eDate)));
				if(!in_array($eDate,$myEventDates)) {
					$inPattern=array('chkpat'=>'success','inPattern'=>$eDate.' is not in the pattern scope you specified.');
				}
			} else {$inPattern=array('chkpat'=>'error','inPattern'=>'Please update your Pattern.');}
		}

		$isAval = true;
		if($model) {
			$i=0;$lanes_used=0;
			foreach($model as $key => $item) {
				if (($item->calendar_id == $id) || (($id >0 ) && ($item->recurrent_calendar_id == $id))) { continue; }
				$found[$i] = new \stdClass();
				$found[$i]->cal_id = $item->calendar_id;
				$found[$i]->club = $item->clubs->short_name;
				$found[$i]->name = $item->event_name;
				$found[$i]->start =  date('h:i A',strtotime($item->start_time));
				$found[$i]->stop = date('h:i A',strtotime($item->end_time));
				$found[$i]->event_status_id = $item->event_status_id;
				$found[$i]->eve_status_name = $item->agcEventStatus->name;
				$found[$i]->range_status_id = $item->range_status_id;
				$found[$i]->rng_status_name = $item->agcRangeStatus->name;
				$found[$i]->req_lanes = $item->lanes_requested;
				if ($found[$i]->event_status_id==18) 			   {$type_i=1; $type_n='Holiday';}
				else if (strpos($item->recur_week_days,'daily'))   {$type_i=5; $type_n='Daily';}
				else if (strpos($item->recur_week_days,'weekly'))  {$type_i=4; $type_n='Weekly';}
				else if (strpos($item->recur_week_days,'monthly')) {$type_i=3; $type_n='Monthly';}
				else if (strpos($item->recur_week_days,'yearly'))  {$type_i=2; $type_n='Yearly';}
				else 											   {$type_i=6; $type_n='Non Recurring';}
				$found[$i]->type_i = $type_i;
				$found[$i]->type_n = $type_n;
				if ($force_order) {
					if ((int)$rng_pri < (int)$type_i) {
						$found[$i]->lanes = 0;
					} else {
						$lanes_used  += $item->lanes_requested;
						$found[$i]->lanes = $item->lanes_requested;
					}
				} else {
					$lanes_used  += $item->lanes_requested;
					$found[$i]->lanes = $item->lanes_requested;
				}
				$i++;
			}

	//yii::$app->controller->createLog(true, 'trex_C_CC:294', var_export($isAval,true));

			if ($isAval==false){
				 $returnMsg=['status'=>'error','msg'=>'Range is Closed due to other event','lu'=>$lanes_used, 'data'=>$found];
			} else if ($range->available_lanes==0) {
				if (isset($found)) {
					if (($isAval) && ($force_order)) {
						foreach ($found as $overwrite) {
							if ((int)$rng_pri < (int)$type_i) {
								AgcCal::UpdateAll(['conflict'=>1,'approved'=>0],'calendar_id = '.$overwrite->cal_id);
								yii::$app->controller->createLog(true, 'trex_C_CC:304', "*** Conflict found $eDate, overwriting: ".$overwrite->cal_id." - $rng_pri < $type_i");
							} else { $isAval=false; }
							if ($isAval==true) {
								$returnMsg=['status'=>'success','msg'=>'You Have Priority.','data'=>$found];
							} else {
								$returnMsg=['status'=>'error','msg'=>"You Don't have Priority.",'data'=>$found];
							}
						}
					} else {
						$isAval = false;
						$returnMsg=['status'=>'error','msg'=>'Facility is unavailable','data'=>$found];
					}
				} else {
					$isAval=true;
					$returnMsg=['status'=>'success','msg'=>'Facility is available','ln'=>312];
				}
			} else {
				if ($lanes < 1) {
					$isAval = false;
					$returnMsg=['status'=>'error','msg'=>"Please Provide Requested lanes (Up to ".$range->available_lanes.")"];
				} else if ($lanes+$lanes_used > $range->available_lanes ) {
					if ($force_order) {
						foreach ($found as $overwrite) {
							//yii::$app->controller->createLog(true, 'trex_C_CC:321', "** $rng_pri < ".$overwrite->type_i);
							if ((int)$rng_pri < (int)$overwrite->type_i) {
								AgcCal::UpdateAll(['conflict'=>1],'calendar_id = '.$overwrite->cal_id);
								yii::$app->controller->createLog(true, 'trex_C_CC:324', "** Conflict found $eDate, overwriting: ".$overwrite->cal_id);
							}
						}
						$returnMsg=['status'=>'success','msg'=>"Not Enough Free Lanes or All lanes have been reserved! (".($lanes_used)." used!)<br> But you have Priority.", 'data'=>$found];
					} else {
						$isAval = false;
						$returnMsg=['status'=>'error','msg'=>"Not Enough Free Lanes or All lanes have been reserved! (".($lanes_used)." used!)", 'data'=>$found];
					}
				} else {
					$isAval=true;
					if(isset($found)) {
						$returnMsg=['status'=>'success','msg'=>'Range has space left: ' .($range->available_lanes-$lanes_used) .' Lanes','lu'=>$lanes_used, 'data'=>$found];
					} else {
						$returnMsg=['status'=>'success','msg'=>'No one else reserved range ','lu'=>$lanes_used];
					}
				}
			}
		} else {
			$returnMsg=['status'=>'success','msg'=>'Facility is Available','ln'=>342];
		}

		if ($inPattern) { $returnMsg = array_merge($returnMsg,$inPattern); }
		//yii::$app->controller->createLog(false, 'trex_C_CC:345 isAval', var_export($returnMsg,true));
		if (($tst) || (Yii::$app->request->isAjax)) {
			return json_encode($returnMsg);
		} elseif ($internal){
			return $isAval;
		} else {
			Yii::$app->getSession()->setFlash($returnMsg['status'], $returnMsg['msg']);
			return $this->render('test');
		}
	}

	public function actionRecur() {
		$searchModel = new AgcCalSearch();
		$searchModel->recur_every = true;
		$searchModel->deleted = 0;
		$this->RestoreSession($searchModel);
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider ]);
	}

	public function actionRepublish($id,$force_order=false) {
		$model = AgcCal::find()->where(['calendar_id' => $id])->one();
		if (isset($model->recurrent_calendar_id)) {
			if (Yii::$app->request->isAjax) {
				// Why is this called via AJAX?  DO nothing...
			} else {
				if ($model->recurrent_calendar_id >0) {
					if ((int)$model->deleted == 1 ) { return json_encode(['status'=>'error','msg'=>"Event has been deleted, can't republish"]); }
					yii::$app->controller->createCalLog(true,  $_SESSION['user'], "Republishing event: ','".$model->event_name.'('.$model->calendar_id.')');

					$nowTime = yii::$app->controller->getNowTime();
					$sql = "DELETE from associat_agcnew.agc_calendar where recurrent_calendar_id = ".$id." and  event_date >= '".$nowTime."'";
					$command = Yii::$app->db->createCommand($sql);
					$saveOut = $command->execute();

					yii::$app->controller->createCalLog(true,  $_SESSION['user'], "Republishing event: ','Deleted ". var_export($saveOut,true)." Futuer Events");
					if (strtotime($nowTime) > strtotime(date('Y').'-07-01 00:00:00')) {$myYr= intval(date('Y'))+1;} else {$myYr= date('Y');}
		//		echo "Start: $model->recurrent_start_date, End: $model->recurrent_end_date <br />";
					$myEventDates = $this->getEvents($model->recurrent_start_date,$model->recurrent_end_date,$model->recur_week_days,$myYr);

					$model = $this->createRecCalEvent($model,$myEventDates,$force_order);
					if($force_order) { return $this->redirect(['recur']); } else {
					return $this->redirect(['update', 'id' => $model->recurrent_calendar_id]);}

				} else {
					echo " Not a Recurring Event";
				}
			}
		} else {
			echo "Nothing Found";
		}
	}

	public function actionShowed($id,$showed){
		$model = $this->findModel($id);
		$model->showed_up=$showed;
		$model->save();
		if($showed==1) {$showed_up='Yes';} else {$showed_up='No';}
		yii::$app->controller->createCalLog(true,  $_SESSION['user'], "Event Attendance:','".$model->club_id.' '.$model->event_name.'('.$model->calendar_id.') '.$showed_up);
		return $this->redirect(['index']);
	}

    public function actionUpdate($id=1) {
		$model = $this->findModel($id);
		if (!$model) { return $this->redirect(['index']); }

		if((!yii::$app->controller->hasPermission('calendar/all')) && (!in_array($model->club_id,json_decode(Yii::$app->user->identity->clubs)))) {
			Yii::$app->getSession()->setFlash('error', 'Not Your Event.');return $this->redirect(['/calendar/index']);
		}

        if ($model->load(Yii::$app->request->post())) {

			$model->start_time 	= date('Y-m-d H:i:s', strtotime("$model->event_date $model->start_time")) ;
			$model->end_time	= date('Y-m-d H:i:s', strtotime("$model->event_date $model->end_time")) ;

			$model->club_id = (int)$model->club_id;
			$model->event_status_id = (int)$model->event_status_id;
			$model->facility_id = (int)$model->facility_id;
			$model->lanes_requested = (int)$model->lanes_requested;
			$model->range_status_id = (int)$model->range_status_id;

			if ($this->actionOpenRange($model->event_date,$model->start_time,$model->end_time,$model->facility_id,$model->lanes_requested,$model->calendar_id,$model->recur_week_days,$model->event_status_id,true)) {
				$model->conflict = 0;  $model->approved = 1; } else { $model->conflict = 1; }

			if(isset($model->recurrent_start_date)) {
				$model->recurrent_start_date = date('Y-m-d H:i:s',strtotime('2000 '.$model->recurrent_start_date));
				$model->recurrent_end_date = date('Y-m-d H:i:s',strtotime('2000 '.$model->recurrent_end_date));
			}

			if($model->recur_every) {
				$model->recur_week_days = $this->GetPattern($_POST);
				if(($model->recur_week_days==null) && ((int)$model->deleted != 1 )) {
					Yii::$app->getSession()->setFlash('error', 'No Recurring range set');
					return $this->redirect(['update','id' => $id]);
				}
			}

			$dirty = $this->loadDirtyFilds($model);
			$dirty = implode(", ",$dirty);
			if($dirty) {
				$myRemarks = [
					'created_at'=>yii::$app->controller->getNowTime(),
					'data'=>"Updated: ".$dirty,
					'changed'=> 'Updated by '.$_SESSION['user'],
				];
				$model->remarks = yii::$app->controller->mergeRemarks($model->remarks, $myRemarks);
			}

        	if($model->save()) {
				yii::$app->controller->createCalLog(true,  $_SESSION['user'], "Updated Calendar item: ','".$model->event_name.'('.$model->calendar_id.')');
				Yii::$app->getSession()->setFlash('success', 'Calendar Item has been updated');
				if(($model->recur_every) && ($model->recurrent_calendar_id == $model->calendar_id)) {
					// Master Record!!

					AgcCal::UpdateAll(['club_id'=>$model->club_id, 'event_name'=>$model->event_name, 'keywords'=>$model->keywords, 'recur_week_days'=>$model->recur_week_days], 'recurrent_calendar_id = '.$model->calendar_id);

					AgcCal::UpdateAll(['facility_id'=>$model->facility_id, 'lanes_requested'=>$model->lanes_requested, 'event_status_id'=>$model->event_status_id, 'range_status_id'=>$model->range_status_id,
						'start_time'=>$model->start_time, 'end_time'=>$model->end_time, 'deleted'=>$model->deleted, 'poc_badge'=>$model->poc_badge, 'poc_name'=>$model->poc_name, 'poc_phone'=>$model->poc_phone, 'poc_email'=>$model->poc_email],
						"recurrent_calendar_id = ".$model->calendar_id." AND event_date >= '".date('Y-m-d',strtotime($this->getNowTime()))."'");

					yii::$app->controller->createCalLog(true,  $_SESSION['user'], "Updated Master Calendar item: ','".$model->event_name.'('.$model->calendar_id.')');
					//yii::$app->controller->createCalLog(false, 'trex-B_C_CC:369', var_export($cmd,true));
					if ((int)$model->event_status_id==19) { $model->range_status_id = 1; $model->save(); }

					if (isset($_POST['republish'])) {
						return $this->redirect(['republish','id' => $id]);
					}
				} else {
					if ((int)$model->event_status_id==19) { $model->range_status_id = 1; $model->save(); }
				}
			} else {
				yii::$app->controller->createCalLog(false, 'trex-B_C_CC:501', 'save error');
				Yii::$app->getSession()->setFlash('error', 'Something Went Wrong');
			}
			yii::$app->controller->createCalLog(false, 'trex-B_C_CC:504', 'updated');
			return $this->redirect(['update','id' => $id]);


        } else {
			if(($_SESSION['badge_number']>0) && ($model->poc_badge==0)) { $model->poc_badge=$_SESSION['badge_number']; }

            return $this->render('update', [
                'model' => $model,
            ]);
        }
	//} else { Yii::$app->getSession()->setFlash('error', 'Record Moved.');return $this->redirect(['/calendar/index']); }
    }

	public function loadDirtyFilds($model) {
		$model->active	= (int)$model->active;
		$model->approved= (int)$model->approved;
		$model->deleted = (int)$model->deleted;
		$model->recur_every = (int)$model->recur_every;
		$model->recurrent_calendar_id = (int)$model->recurrent_calendar_id;
		$model->pattern_type = 0;
		$model->lanes_requested= (int)$model->lanes_requested;
		$items=$model->getDirtyAttributes();
		$obejectWithkeys = [
			'active' => 'Active',
			'approved' => 'Approved',
			'club_id' => 'Club',
			'conflict' => 'Conflict',
			'deleted' => 'Deleted',
			'date_requested' => 'Date Requested',
			'event_name' => 'Event Name',
			'event_date' => 'Event Date',
			'start_time' => 'Start Time',
			'end_time' => 'End Time',
			'event_status_id' => 'Event Status',
			'facility_id' => 'Facility',
			'keywords' => 'Key Words',
			'lanes_requested' => 'lanes Requested',
			'pattern_type' => 'Patern Type',
			'poc_email' => 'POC Email',
			'poc_name' => 'POC Name',
			'poc_phone'=>'POC Phone',
			'range_status_id' => 'Range Status',
			'recur_every' => 'Recure every',
			'recurrent_calendar_id' => 'Recurrent Parrent ID',
			'recurrent_start_date' => 'Recure Start Date',
			'recurrent_end_date' => 'Recure End Date',
			'recur_week_days'=>'recur_week_days',
		];

		$responce = [];

		foreach($items as $key => $item) {
			if(array_key_exists($key,$obejectWithkeys)) {
				$responce[] = $obejectWithkeys[$key];
			}
		}
		sort($responce);
		return $responce;
	}

	public function RestoreSession($searchModel) {
		if(isset($_REQUEST['reset'])) {
			unset($_SESSION['CalSearchTime']);
			unset($_SESSION['CalSearchclub_id']);
			unset($_SESSION['CalSearchevent_name']);
			unset($_SESSION['CalSearchapproved']);
			return $this->redirect(['index']);
		} else {
			if(isset($_REQUEST['AgcCalSearch']['SearchTime'])) {
				$searchModel->SearchTime = $_REQUEST['AgcCalSearch']['SearchTime'];
				$_SESSION['CalSearchTime'] = $_REQUEST['AgcCalSearch']['SearchTime'];
			} elseif (isset($_SESSION['CalSearchTime'])) {
				$searchModel->SearchTime = $_SESSION['CalSearchTime'];
			}
			if(isset($_REQUEST['AgcCalSearch']['club_id'])) {
				$searchModel->club_id = $_REQUEST['AgcCalSearch']['club_id'];
				$_SESSION['CalSearchclub_id'] = $_REQUEST['AgcCalSearch']['club_id'];
			} elseif (isset($_SESSION['CalSearchclub_id'])) {
				$searchModel->club_id = $_SESSION['CalSearchclub_id'];
			}
			if(isset($_REQUEST['AgcCalSearch']['event_name'])) {
				$searchModel->event_name = $_REQUEST['AgcCalSearch']['event_name'];
				$_SESSION['CalSearchevent_name'] = $_REQUEST['AgcCalSearch']['event_name'];
			} elseif (isset($_SESSION['CalSearchevent_name'])) {
				$searchModel->event_name = $_SESSION['CalSearchevent_name'];
			}
			if(isset($_REQUEST['AgcCalSearch']['approved'])) {
				$searchModel->approved = $_REQUEST['AgcCalSearch']['approved'];
				$_SESSION['CalSearchapproved'] = $_REQUEST['AgcCalSearch']['approved'];
			} elseif (isset($_SESSION['CalSearchapproved'])) {
				$searchModel->approved = $_SESSION['CalSearchapproved'];
			}
			if(isset($_REQUEST['AgcCalSearch']['facility_id'])) {
				$searchModel->facility_id = $_REQUEST['AgcCalSearch']['facility_id'];
				$_SESSION['CalSearchfacility_id'] = $_REQUEST['AgcCalSearch']['facility_id'];
			} elseif (isset($_SESSION['CalSearchfacility_id'])) {
				$searchModel->facility_id = $_SESSION['CalSearchfacility_id'];
			}
		}
		return $searchModel;
	}

	private function GetPattern($post_data) {
		//yii::$app->controller->createLog(false, 'trex-B_C_CC:593', var_export($post_data,true));
		$myPat=null;
		if(isset($post_data['pat_type'])){
			if($post_data['pat_type']=='daily'){
				if((isset($post_data['pat_daily'])) && ($post_data['pat_daily']<>'')) {$myDaily=$post_data['pat_daily'];} else {$myDaily='wd';}
				$myPat='{"daily":"'.$myDaily.'"}';
			} elseif ($post_data['pat_type']=='weekly'){
				$days='';
				if(isset($post_data['pat_da_mon'])) $days.='"mon",';
				if(isset($post_data['pat_da_tue'])) $days.='"tue",';
				if(isset($post_data['pat_da_wed'])) $days.='"wed",';
				if(isset($post_data['pat_da_thu'])) $days.='"thu",';
				if(isset($post_data['pat_da_fri'])) $days.='"fri",';
				if(isset($post_data['pat_da_sat'])) $days.='"sat",';
				if(isset($post_data['pat_da_sun'])) $days.='"sun",';

				$myPat='{"weekly":"'.$post_data['pat_week_n'].'","days":['.rtrim($days, ',').']}';
			} elseif ($post_data['pat_type']=='monthly'){
				if ($post_data['pat_mon_by']=='date'){
					$myPat='{"monthly":"'.$post_data['pat_mon_by'].'","day":"'.$post_data['pat_mon_x'].'","every":"'.$post_data['pat_mon_m'].'"}';
				} elseif ($post_data['pat_mon_by']=='day'){
					$myPat='{"monthly":"'.$post_data['pat_mon_by'].'","when":"'.$post_data['pat_mon_wk'].'","day":"'.$post_data['pat_mon_day'].'","every":"'.$post_data['pat_mon_n'].'"}';
				}
			} else { //if($post_data['pat_type']=='yearly'){
				if(isset($post_data['pat_yearly'])) {
					$myPat='{"yearly":"'.$post_data['pat_yearly'].'","every":"'.$post_data['pat_yr_n'].'",';
					if($post_data['pat_yearly']=="date"){
						$myPat.='"mon":"'.$post_data['pat_yr_mon'].'","day":"'.$post_data['pat_yr_mon_d'].'"}';
					} elseif($post_data['pat_yearly']=='day'){
						$myPat.='"on":"'.$post_data['pat_yr_num'].'","day":"'.$post_data['pat_yr_day'].'","of":"'.$post_data['pat_yr_mon_a'].'"}';
					}
				}
			}
		}
		return $myPat;
	}

	public function getEvents($eStart,$eEnd,$ePat,$whatYear, $eco=false) {

		if (strtotime($eStart) > strtotime($eEnd)) {
			if (date('Y') == $whatYear) {
				if($eco) { echo "Start A";}
				$myEventDatesA = $this->getEventDates(date('Y').'-01-01',$eEnd,$ePat,$whatYear,$eco);
				$myEventDatesB = $this->getEventDates($eStart,date('Y').'-12-31',$ePat,$whatYear,$eco);
				return array_merge($myEventDatesA,$myEventDatesB);
			} else {
				if($eco) { echo "Start B";}
				$myEventDatesA = $this->getEventDates(date('Y').'-01-01',$eEnd,$ePat,date('Y'),$eco);
				$myEventDatesB = $this->getEventDates($eStart,date('Y').'-12-31',$ePat,date('Y'),$eco);
				$myEventDatesC = $this->getEventDates($whatYear.'-01-01',$eEnd,$ePat,$whatYear,$eco);
				$myEventDatesD = $this->getEventDates($eStart,$whatYear.'-12-31',$ePat,$whatYear,$eco);
				return array_merge($myEventDatesA,$myEventDatesB,$myEventDatesC,$myEventDatesD);
			}
		} else {
			if (date('Y') == $whatYear) {
				if($eco) { echo "Start C";}
				return $this->getEventDates($eStart,$eEnd,$ePat,$whatYear,$eco);
			} else {
				if($eco) { echo "Start D";}
				$myEventDatesA = $this->getEventDates($eStart,$eEnd,$ePat,date('Y'),$eco);
				$myEventDatesB = $this->getEventDates($eStart,$eEnd,$ePat,$whatYear,$eco);
				return array_merge($myEventDatesA,$myEventDatesB);
			}
		}
	}

	private function getEventDates($eStart,$eEnd,$ePat,$whatYear,$eco) {
if($eco) { echo "<hr />GetEventDates: Start: $eStart, End: $eEnd, Pat: $ePat, yr:  $whatYear <br />"; }
		$today = date('Y-m-d',strtotime(yii::$app->controller->getNowTime()));
		$Date_Start = strtotime(strval($whatYear.'-'.date('m-d',strtotime($eStart))));
		$Date_Stop  = strtotime(strval($whatYear).'-'.date('m-d',strtotime($eEnd)));
		if ($Date_Start < $Date_Stop) {$dayCnt='N';} else {$dayCnt='R';}

		$myEventDates=[];
		$myPat = json_decode($ePat);

if($eco) {
	echo "Yr: $whatYear <br> Start: ". date('Y-m-d',$Date_Start)." = $Date_Start,<br> Stop: ".date('Y-m-d',$Date_Stop)." = $Date_Stop, <br>Direction: $dayCnt. <hr> Pattern: $ePat <br />";
	print_r( $myPat);
	yii::$app->controller->createLog(true, 'trex-B_C_CC:671', var_export($myPat,true));
	echo " <hr> <br>"; }

		if (isset($myPat->daily)) {
 if($eco) { echo "Daily<br>"; }
			if ($myPat->daily=='wd') {
					for ($i = $Date_Start; $i < $Date_Stop; $i = strtotime('+1 day', $i)) {
if($eco) { echo '<br>'.strtolower(date('D', $i)) ; }
						if (!in_array(strtolower(date('D', $i)),['sat','sun'])) {
if($eco) { echo "** ".date('Y-m-d',$i); }
							if ($i >=strtotime($today)) {
								array_push($myEventDates,date('Y-m-d',$i));
							}
						}
					}
			} else {
				for ($i = $Date_Start; $i < $Date_Stop; $i = strtotime('+'.$myPat->daily.' day', $i)) {
					array_push($myEventDates,date('Y-m-d',$i));
				}
			}
		}

		elseif (isset($myPat->weekly)) {
if($eco) { echo "Weekly<br>"; }
			if($myPat->weekly > 1) {
				$skip = ($myPat->weekly -1) * 7;
			} else {$skip=0;}

			$cnt=0; $skip_cnt=0; $skip_now=false;
			for ($i = $Date_Start; $i < $Date_Stop; $i = strtotime('+1 day', $i)) {
				$cnt++; if (($cnt==8) && ($skip>0)) {$skip_now=true;}
				if ((in_array(strtolower(date('D', $i)),$myPat->days)) && ($skip_now==false)) {
					if ($i >=strtotime($today)) {
						array_push($myEventDates,date('Y-m-d',$i));
					}
				} elseif ($skip_now==true) {
					$skip_cnt++; if ($skip_cnt==$skip) {$skip_now=false; $cnt=0; $skip_cnt=0;}
				}
			}
		}

		elseif (isset($myPat->monthly)) {
if($eco) { echo "Monthly<br>"; }
			if($myPat->every > 1) { $skip = ($myPat->every); }
			else { $skip = 0; }
			$cnt=1;

if($eco) { echo  "s: ".date('m',$Date_Start). ' e:'. (date('m',$Date_Stop)+1).'<br>'; }
			for ($i = date('m',$Date_Start); $i < date('m',$Date_Stop)+1; $i++) {
				$skip_now=true;
				if ($cnt == 1) {
					$skip_now=false;
					if($skip==0) {	$cnt=0; }
				} elseif ($cnt==$skip){ $cnt=0;}

				if($myPat->monthly == 'day') {	// by Day
					$myMonth = strtotime($myPat->when." ".$myPat->day." $whatYear-".str_pad($i, 2, '0', STR_PAD_LEFT));
					if (date('d',strtotime("first ".$myPat->day." $whatYear-".str_pad($i, 2, '0', STR_PAD_LEFT)))=='08') {
						$myMonth = $myMonth-(60*60*24*7); }
				} else {	// by Date
					$myMonth = strtotime($i."/".$myPat->day."/$whatYear");
				}
				if (($myMonth >= $Date_Start ) && ($myMonth <= $Date_Stop)  && ($skip_now==false)) {
if($eco) { echo "737: ".date("D, d-M-Y", $myMonth)." >= ".$today." <br>"; }
					if ($myMonth >= strtotime($today)) {
if($eco) { echo date("D, d-M-Y", $myMonth).":<br>"; }
						array_push($myEventDates,date('Y-m-d',$myMonth));
					} else {
if($eco) { echo "742: date passed,<br />";}
					}
				} elseif ($skip_now==true) {
if($eco) { echo "Skipping : ".date("D, d-M-Y", $myMonth).":<br>"; }
				} else {
if($eco) { echo "else 583<br>"; }
				}
				$cnt++;
			}
		}

		elseif (isset($myPat->yearly)) {
if($eco) { echo "yearly"; }
			if($myPat->yearly == 'date') {
				$myYear = $whatYear.'-'.str_pad($myPat->mon, 2, '0', STR_PAD_LEFT).'-'.str_pad($myPat->day, 2, '0', STR_PAD_LEFT);
			} else {
				$myYear = strtotime($myPat->on." ".$myPat->day.' '.$whatYear.'-'.str_pad($myPat->of, 2, '0', STR_PAD_LEFT));
				if (date('d',strtotime("first ".$myPat->day." $whatYear-".str_pad($myPat->of, 2, '0', STR_PAD_LEFT)))=='08') {
					$myYear = $myYear-(60*60*24*7); }
				$myYear = date("Y-m-d",$myYear);
			}
			if (strtotime($myYear) >=strtotime($today)) {
if($eco) { echo "<br>$myYear"; }
				array_push($myEventDates,$myYear);
			}
		}
		else { echo "broke?? WTF???"; exit; }

//	yii::$app->controller->createLog(true, 'trex-B_C_CC:766', var_export($myEventDates,true));
//exit;
		return $myEventDates;
	}

	private function createRecCalEvent($model,$myEventDates,$force_order=false,$is_new=false) {
		$NewID = false; $first_id=false;
//if ($force_order) {yii::$app->controller->createLog(true, 'trex_C_CC:773','forcing_Order RecCalEvent');}
		$model_event = new AgcCal();
		foreach($myEventDates as $eDate) {
			if (((strtotime(yii::$app->controller->getNowTime()) > strtotime($model->event_date)) && ($eDate == $model->event_date)) ||
				(($is_new) && ($eDate == $model->event_date))) { continue; }
			$model_event->setIsNewRecord(true);
			$model_event->calendar_id = null;
			$model_event->recurrent_calendar_id = $model->calendar_id;
			$model_event->event_date 		= $eDate;
			$model_event->club_id			= $model->club_id;
			$model_event->facility_id 		= $model->facility_id;
			$model_event->event_name 		= $model->event_name;
			$model_event->keywords 			= $model->keywords;
			$model_event->start_time	 	= $model->start_time;
			$model_event->end_time 			= $model->end_time;
			$model_event->date_requested 	= $model->date_requested;
			$model_event->lanes_requested 	= $model->lanes_requested;
			$model_event->recur_every 		= $model->recur_every;
			$model_event->pattern_type 		= $model->pattern_type;
			$model_event->recur_week_days 	= $model->recur_week_days;
			$model_event->recurrent_start_date = $model->recurrent_start_date;
			$model_event->recurrent_end_date = $model->recurrent_end_date;
			$model_event->event_status_id 	= $model->event_status_id;
			$model_event->range_status_id 	= $model->range_status_id;
			if ($this->actionOpenRange($eDate,$model_event->start_time,$model_event->end_time,$model_event->facility_id,$model_event->lanes_requested,0,$model->recur_week_days,$model->event_status_id,true,$force_order)) {
				$model_event->conflict = 0; $model_event->approved =1;
			} else {
				$model_event->conflict = 1; $model_event->approved =0;
			}
			//$model_event->approved 			= $model->approved;
			$model_event->deleted 			= $model->deleted;
			$model_event->active 			= $model->active;
			if(!$model->rollover) { $model_event->rollover = 0; } else { $model_event->rollover = $model->rollover; }
			$model_event->time_format 		= 1;
			$model_event->poc_badge 		= $model->poc_badge;
			$model_event->remarks 			= $model->remarks;
			$model_event->save();
			if(!$first_id) {$first_id=$model->calendar_id;}
			yii::$app->controller->createCalLog(true, $_SESSION['user'], "Created New Calendar item: ','".$model_event->calendar_id.'->'.$model_event->event_name);

			if (intval(substr($eDate,0,4)) > intval(date('Y'))){
				if ($NewID == false) {
					$NewID = $model_event->calendar_id;
				}
			}
		}

		if(!in_array($model->event_date,$myEventDates)) {
			yii::$app->controller->createLog(true, 'trex_C_CC:814','date: '.$model->event_date.' not in array');
			yii::$app->controller->createLog(false, 'trex', var_export($myEventDates,true));
		}

		if ($NewID) {
			AgcCal::UpdateAll(['recurrent_calendar_id'=>$NewID],"recurrent_calendar_id = ".$model->recurrent_calendar_id);
			$model->recurrent_calendar_id=$NewID;
			$model->save();
		}
		return $model;
	}

    protected function findModel($id) {
        if (($model = AgcCal::findOne($id)) !== null) {
            return $model;
        } else {
            Yii::$app->getSession()->setFlash('error', 'Record Moved.');
			return false;
        }
    }
}
