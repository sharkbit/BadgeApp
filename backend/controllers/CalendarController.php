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
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * CalendarController implements the CRUD actions for Calendar model.
 */
class CalendarController extends AdminController {
	/**
	  * @inheritdoc
	 */

	public $myFilters = ['SearchTime','club_id','event_name','event_date','event_status_id','range_status_id','facility_id','recur_week_days','key_words','showed_up'];

	public function behaviors() {
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'delete' => ['POST'],
				//	'recheck-future-conflicts' => ['POST'],
				],
			],
		];
	}

	public function actionApprove($id,$redir='index') {
		$model = $this->findModel($id);
		$model->approved = 1;
		$myRemarks = ['created_at'=>yii::$app->controller->getNowTime(),'changed'=>'Approved by '.$_SESSION['user'],'data'=>""];

		$model->remarks = yii::$app->controller->mergeRemarks($model->remarks, $myRemarks);
		if($model->save(false)) {
			Yii::$app->getSession()->setFlash('success', 'Approved event id: '.$id);
		} else {Yii::$app->getSession()->setFlash('error', 'it didnt save :(');}
		return $this->redirect([$redir]);
	}

	public function actionCreate() {
		$model = new AgcCal();
		if ($model->load(Yii::$app->request->post())) {
			//yii::$app->controller->createLog(true, 'trex_B_C_CalC:52 post', var_export($_POST,true));
			// Save
			$model->cal_start_time 	= date('H:i:s', strtotime($model->cal_start_time)) ;
			$model->cal_end_time	= date('H:i:s', strtotime($model->cal_end_time)) ;
			$model->event_date .= ' 00:00:00';
			if(!$model->rollover) { $model->rollover = 0; } else { $model->rollover = $model->rollover; }
			$model->facility_id = str_replace('"', '',json_encode($model->facility_id));
			$model->conflict = 0;
			$model->recurrent_calendar_id = 0;
			$myRemarks = [
				'created_at'=>yii::$app->controller->getNowTime(),
				'changed'=> "Event Created by ".$_SESSION['user'],
				'data'=>'Facility ('.(new AgcCal)->getAgcFacility_Names($model->facility_id).')',
			];
			$model->remarks = yii::$app->controller->mergeRemarks($model->remarks, $myRemarks);

			$Req_Lanes = (new agcFacility)->getFacilRequiresLanes();
			foreach ($Req_Lanes as $chk_rng) {
				if (!empty($_POST['agccal-lanes_' . $chk_rng['facility_id']])) {
					$req_num = (int)($_POST['agccal-lanes_' . $chk_rng['facility_id']]);
					if($req_num > 0) { $reqLanesArray[$chk_rng['facility_id']] = $req_num; }
				}
			}
			if (!empty($reqLanesArray)) {$model->lanes_req = stripslashes(json_encode($reqLanesArray, JSON_UNESCAPED_SLASHES));}

			if($model->recur_every) {
				if(isset($model->recurrent_start_date)) {
					$model->recurrent_start_date = date('Y-m-d H:i:s',strtotime(date('Y')." ".$model->recurrent_start_date));
					$model->recurrent_end_date = date('Y-m-d H:i:s',strtotime(date('Y')." ".$model->recurrent_end_date));
				}
				$model->recur_week_days = $this->GetPattern($_POST);
				if(($model->recur_week_days==null) && ((int)$model->deleted != 1 )) {
					Yii::$app->getSession()->setFlash('error', 'No Recurring range set');
					return $this->redirect(['create','recur'=>1,'model'=>$model]);
				}
				$myEventDates = $this->getEvents($model->recurrent_start_date,$model->recurrent_end_date,$model->recur_week_days,false,false);

				if (is_array($myEventDates) && sizeof($myEventDates) >0) {

					$model->save();
					$model->recurrent_calendar_id = $model->calendar_id;
					$model->event_date = $myEventDates[0];
					if ($this->actionOpenRange($model->event_date,$model->cal_start_time,$model->cal_end_time,$model->facility_id,$model->lanes_req,$model->calendar_id,$model->recur_week_days,$model->event_status_id,true))
						{ $model->conflict = 0; } else { $model->conflict = 1; }
					$model->save();
					$model = $this->createRecCalEvent($model,$myEventDates,false,true);
				} else {
					Yii::$app->getSession()->setFlash('error', 'No events will be created,  Check your dates!');
					return $this->redirect(['create','recur'=>1,'model'=>$model]);
				}
			} else {
				$model->recurrent_start_date = $model->recurrent_end_date = null;

				$model->recur_every=0;
				$model->save();
			}

			yii::$app->controller->createCalLog(true,  $_SESSION['user'], "Created New Calendar item: ','".$model->calendar_id.'->'.$model->event_name);
			return $this->redirect(['update', 'id' => $model->calendar_id]);
		} else {
			$model->deleted = 0;

			$model->event_status_id=2;
			$model->facility_id='[3]';
			if(($_SESSION['badge_number']>0) && ($model->poc_badge==0)) { $model->poc_badge=$_SESSION['badge_number']; }
			$model->range_status_id = 1; //Open
		}
		return $this->render('create', [
			'model' => $model,
			]);
	}

	public function actionConflict($redir='conflict') {

		if ((Yii::$app->request->post()) && (isset($_POST['selection'])) ) {
			if (is_array($_POST["selection"])) {
				$myRemarks = ['created_at'=>yii::$app->controller->getNowTime(),'changed'=>'Deleted by '.$_SESSION['user'],'data'=>"Record marked as Deleted."];
				//update all remarks individually
				foreach($_POST["selection"] as $e_id) {
					$model = $this->findModel($e_id);
					$model->deleted=1;
					$model->remarks = yii::$app->controller->mergeRemarks($model->remarks, $myRemarks);
					$model->save();
				}
			}
			Yii::$app->getSession()->setFlash('success', 'Event(s) Deleted.');
			return $this->redirect(['/calendar/'.$redir]);
		}

		$searchModel = new AgcCalSearch();
		$searchModel->conflict = 1;
		$this->RestoreSession($searchModel,'AgcCal',$this->myFilters);
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider ]);
	}

	public function actionDelete($id,$type='s',$redir='index') {
		$model = $this->findModel($id);
		if ($model) {
			$myRemarks = ['created_at'=>yii::$app->controller->getNowTime(),'changed'=>'Deleted by '.$_SESSION['user'],'data'=>"Record marked as Deleted."];


			if ($type=='s') {
					AgcCal::UpdateAll(['deleted'=>1,'remarks'=>$model->remarks], "calendar_id = ".$model->calendar_id);
					yii::$app->controller->createCalLog(true,  $_SESSION['user'], "Deleted Calendar item (s): ','".$model->event_name.'('.$model->calendar_id.')');

			} else {
				if ((int)$model->recurrent_calendar_id > 0) {
					AgcCal::UpdateAll(['deleted'=>1,'remarks'=>$model->remarks], "recurrent_calendar_id = ".$model->calendar_id." AND event_date >= '".date('Y-m-d',strtotime($this->getNowTime()))."'");
					AgcCal::UpdateAll(['deleted'=>1,'remarks'=>$model->remarks], "calendar_id = ".$model->calendar_id);
					yii::$app->controller->createCalLog(true,  $_SESSION['user'], "Deleted Master Calendar item: ','".$model->event_name.'('.$model->calendar_id.')');

				} else {
					AgcCal::UpdateAll(['deleted'=>1,'remarks'=>$model->remarks], "calendar_id = ".$model->calendar_id);
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
				} elseif (array_intersect([11,15],$_SESSION['privilege'])) {	//if Chairmen or Shooting Bay
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
		$this->RestoreSession($searchModel,'AgcCal',$this->myFilters);
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider ]);
	}

	public function actionList() {
		$searchModel = new AgcCalSearch();
		$searchModel->deleted = 0;
		if (($_REQUEST['form_action'] ?? '') !== 'reset') {
			$this->RestoreSession($searchModel, 'AgcCal', $this->myFilters);
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		} else {
			unset($_REQUEST['key_words']);
			unset($_REQUEST['event_date']);
			Yii::$app->session->remove('AgcCal');
			//$searchModel->unsetAttributes();
			$dataProvider = $searchModel->search([]);
			yii::$app->controller->createLog(true, 'trexpageCount', var_export(Yii::$app->request->queryParams,true));
		}

		$this->view->params['hideBackButton'] = true;

		$models = $dataProvider->getModels();
		$groupedModels = ArrayHelper::index($models, null, function ($models) {
			// Returns '2026-08-27' or similar to use as the array key
			return Yii::$app->formatter->asDate($models->event_date, 'yyyy-MM-dd');
		});

		$rowCount = count($models);

		return $this->render('list', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'groupedModels' => $groupedModels]);
	}

	public function actionIndex() {
		$searchModel = new AgcCalSearch();
		$searchModel->deleted = 0;
		$this->RestoreSession($searchModel,'AgcCal',$this->myFilters);
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider ]);
	}

	public function actionOpenRange($eDate, $start, $stop, $facility, $r_lanes = '{}', $id = 0, $pattern = '', $e_status = 0, $internal = false, $force_order = false, $tst = false) {
		if ($tst) {
			Yii::$app->controller->createCalLog(
				true,
				'trex_B_C_CalC:275 OpenRange',
				'eDate: ' . $eDate
					. ', start: ' . $start
					. ', stop: ' . $stop
					. ', facility: ' . var_export($facility, true)
					. ', r_lanes: ' . var_export($r_lanes, true)
					. ', id: ' . $id
					. ', pattern: ' . $pattern
					. ', e_status: ' . $e_status
					. ', internal: ' . var_export($internal, true)
					. ', force order: ' . var_export($force_order, true)
					. ', tst: ' . var_export($tst, true)
			);
		}

		// Normalize query-string arrays, JSON arrays, and legacy comma-separated values.
		if (is_array($facility)) {
			$facilityValues = $facility;
		} else {
			$facility = trim((string)$facility);
			$decodedFacility = json_decode($facility, true);
			if (json_last_error() === JSON_ERROR_NONE) {
				$facilityValues = is_array($decodedFacility) ? $decodedFacility : [$decodedFacility];
			} else {
				$facilityValues = explode(',', trim($facility, "[] \t\n\r\0\x0B"));
			}
		}

		$facilityArray = [];
		foreach ($facilityValues as $facilityValue) {
			if (is_array($facilityValue) || is_object($facilityValue)) {
				continue;
			}

			$facilityId = filter_var(trim((string)$facilityValue, " \t\n\r\0\x0B\"'"), FILTER_VALIDATE_INT);
			if ($facilityId !== false && $facilityId > 0) {
				$facilityArray[] = $facilityId;
			}
		}
		$facilityArray = array_values(array_unique($facilityArray));

		if (empty($facilityArray)) {
			throw new \yii\web\BadRequestHttpException('At least one valid facility is required.');
		}

		$range = agcFacility::find()->where(['facility_id' => $facilityArray])->all();

		$start_m = date('H:i', strtotime($start) + 60);
		$stop_m = date('H:i', strtotime($stop) - 60);

		// Build one parameterized JSON predicate. Keeping the parameters on the
		// query avoids Yii treating nested Expression params as a separate where.
		$whereParts = [];
		$whereParams = [];
		foreach ($facilityArray as $index => $f_id) {
			$parameter = ':facility_id_' . $index;
			$whereParts[] = "JSON_CONTAINS(cal_calendar.facility_id, {$parameter})";
			$whereParams[$parameter] = json_encode($f_id);
		}
		$where_fac = '(' . implode(' OR ', $whereParts) . ')';

		// Primary ActiveQuery setup using safe arrays
		$query = AgcCal::find()
			->joinWith(['agcRangeStatus', 'agcEventStatus'])
			->leftJoin('cal_facilities', "JSON_CONTAINS(cal_calendar.facility_id, concat('\"', cal_facilities.facility_id, '\"'))")
			->where($where_fac)
			->addParams($whereParams)
			->andWhere([
				'event_date' => $eDate,
				'deleted' => 0,
			])
			->andWhere(['<>', 'cal_calendar.event_status_id', 19])
			->andWhere([
				'or',
				['between', new \yii\db\Expression('time(cal_start_time)'), $start_m, $stop_m],
				['between', new \yii\db\Expression('time(cal_end_time)'), $start_m, $stop_m],
				new \yii\db\Expression(':start_m BETWEEN time(cal_start_time) AND time(cal_end_time)', [':start_m' => $start_m]),
				new \yii\db\Expression(':stop_m BETWEEN time(cal_start_time) AND time(cal_end_time)', [':stop_m' => $stop_m])
			])
			->orderBy([
				'cal_calendar.facility_id' => SORT_ASC,
				'cal_calendar.cal_start_time' => SORT_ASC,
				'cal_calendar.cal_end_time' => SORT_ASC
			]);

		$model = $query->all();

		if ($tst) {
			Yii::$app->controller->createCalLog(true, 'trex_B_C_CalC:363', $query->createCommand()->rawSql);
		}

		// Determine current booking priority level
		$rng_pri = match(true) {
			(int)$e_status == 18 => 1,
			strpos($pattern, 'daily') !== false => 5,
			strpos($pattern, 'weekly') !== false => 4,
			strpos($pattern, 'monthly') !== false => 3,
			strpos($pattern, 'yearly') !== false => 2,
			default => 6
		};

		$inPattern = ['chkpat' => 'success'];
		if (!$internal && isset($_POST['AgcCal']['recur_every']) && $_POST['AgcCal']['recur_every'] == 1) {
			$real_pattern = $this->GetPattern($_POST);
			if ($real_pattern) {
				$myEventDates = $this->getEvents($_POST['AgcCal']['recurrent_start_date'], $_POST['AgcCal']['recurrent_end_date'], $real_pattern, $tst, $force_order);
				if (!in_array($eDate, $myEventDates)) {
					$inPattern = ['chkpat' => 'success', 'inPattern' => $eDate . ' is not in the pattern scope you specified.'];
				}
			} else {
				$inPattern = ['chkpat' => 'error', 'inPattern' => 'Please update your Pattern.'];
			}
		}

		$isAval = true;
		$Facl_Lanes_Used = 0;

		if ($model) {
			if ($tst) {
				Yii::$app->controller->createCalLog(false, 'trex_B_C_CalC:394 isAval', var_export($model, true));
			}

			$i = 0;
			$lanes_used = [];
			$found = [];

			foreach ($model as $item) {
				if ($item->calendar_id == $id || ($id > 0 && $item->recurrent_calendar_id == $id)) {
					continue;
				}

				$f_id = (int)trim($item->facility_id, '[]');

				$obj = new \stdClass();
				$obj->cal_id = $item->calendar_id;
				$obj->fac_id = $f_id;
				$obj->fac_name = (new AgcCal)->getAgcFacility_Names($item->facility_id);
				$obj->club = $item->clubs->short_name ?? $item->club_id;
				$obj->name = $item->event_name;
				$obj->start = date('h:i A', strtotime($item->cal_start_time));
				$obj->stop = date('h:i A', strtotime($item->cal_end_time));
				$obj->event_status_id = $item->event_status_id;
				$obj->eve_status_name = $item->agcEventStatus->name ?? '';
				$obj->range_status_id = $item->range_status_id;
				$obj->rng_status_name = $item->agcRangeStatus->name ?? '';
				$item_lanes_req = is_array($item->lanes_req)
					? $item->lanes_req
					: (json_decode((string)$item->lanes_req, true) ?? []);
				$obj->lanes_req = $item_lanes_req;

				$type_i = match(true) {
					$obj->event_status_id == 18 => 1,
					strpos($item->recur_week_days, 'daily') !== false => 5,
					strpos($item->recur_week_days, 'weekly') !== false => 4,
					strpos($item->recur_week_days, 'monthly') !== false => 3,
					strpos($item->recur_week_days, 'yearly') !== false => 2,
					default => 6
				};

				$type_names = [1 => 'Holiday', 2 => 'Yearly', 3 => 'Monthly', 4 => 'Weekly', 5 => 'Daily', 6 => 'Non Recurring'];
				$obj->type_i = $type_i;
				$obj->type_n = $type_names[$type_i];

				if ($force_order && ((int)$rng_pri < (int)$type_i)) {
					$obj->lanes = 0;
				} else {
					$lanes_requested = (int)($item_lanes_req[$f_id] ?? 0);
					$lanes_used[$f_id] = (int)($lanes_used[$f_id] ?? 0) + $lanes_requested;
					$obj->lanes = $lanes_requested;
				}

				$found[$f_id][] = $obj;
				$i++;
			}

			if ($tst && !empty($found)) {
				yii::$app->controller->createCalLog(true, 'trex_B_C_CalC:451 found', var_export($found,true));
				Yii::$app->controller->createCalLog(true, 'trex_B_C_CalC:452 lanes_used', var_export($lanes_used, true));
			}

			$full_msg = '';
			foreach ($range as $fas) {
				$msg = '';
				$Range_available_lanes = $fas->available_lanes;

				if ($Range_available_lanes == 0) {
					if (!empty($found[$fas->facility_id])) {
						$in_use = false;
						foreach ($found[$fas->facility_id] as $tst_rng_obj) {
							if ($tst_rng_obj->fac_id == $fas->facility_id) {
								$in_use = true;
								break;
							}
						}

						if ($in_use) {
							if ($force_order) {
								foreach ($found[$fas->facility_id] as $overwrite) {
									if ((int)$rng_pri < (int)$overwrite->type_i) {
										AgcCal::updateAll(['conflict' => 1, 'approved' => 0], ['calendar_id' => $overwrite->cal_id]);
										$msg .= '<b style="color:green;">You Have Priority on ' . Html::encode($overwrite->fac_name) . '</b>';
									} else {
										$isAval = false;
										$msg .= '<b style="color:red;">You Don\'t have Priority on ' . Html::encode($overwrite->fac_name) . '.</b>';
									}
								}
							} else {
								$isAval = false;
								$msg = '<b style="color:red;">' . Html::encode($fas->name) . ' is unavailable</b>';
							}
						} else {
							$msg = '<b style="color:green;">' . Html::encode($fas->name) . ' is open</b>';
						}
					} else {
						$msg = '<b style="color:green;">' . Html::encode($fas->name) . ' is open</b>';
					}
				} else {
					$lanes = is_array($r_lanes ?? null) ? $r_lanes : (json_decode($r_lanes, true) ?? []);
					$Facl_Lanes_Req = $lanes[$fas->facility_id] ?? 0;
					$Facl_Lanes_Used = $lanes_used[$fas->facility_id] ?? 0;

					if (empty($lanes)) {
						$isAval = false;
						$msg = '<b style="color:red;">Please Provide Requested lanes (Up to ' . $Range_available_lanes . ')</b>';
					} else if ($Facl_Lanes_Req + $Facl_Lanes_Used > $Range_available_lanes) {
if ($tst) { yii::$app->controller->createCalLog(true, 'trex_B_C_CalC:500 lanes', "$Facl_Lanes_Req + $Facl_Lanes_Used > $Range_available_lanes"); }

						$HeavyCheckResult = $this->HeavyCheck($start_m, $stop_m, $fas->facility_id, $found[$fas->facility_id] ?? [], $Facl_Lanes_Req, $Range_available_lanes);
						if ($HeavyCheckResult['status'] == 'Full') {
							if ($force_order) {
								$opened_lanes = 0;
								// Fixed: Correctly nesting iteration into the 2D array elements
								foreach ($found[$fas->facility_id] as $overwrite) {
									if ((int)$rng_pri < (int)$overwrite->type_i) {
										AgcCal::updateAll(['conflict' => 1], ['calendar_id' => $overwrite->cal_id]);
										$opened_lanes += (int)$overwrite->lanes;
									}
									if ($Facl_Lanes_Req + $Facl_Lanes_Used - $opened_lanes <= $Range_available_lanes) {
										break;
									}
								}
								if ($Facl_Lanes_Req + $Facl_Lanes_Used - $opened_lanes <= $Range_available_lanes) {
									$msg = '<b style="color:blue;">Not Enough Free Lanes! (' . $Facl_Lanes_Used . ' used!)<br> But you have Priority.</b>';
								} else {
									$isAval = false;
									$msg = '<b style="color:red;">Not Enough Free Lanes! (' . $Facl_Lanes_Used . ' used!).</b>';
								}
							} else {
								$isAval = false;
								$msg = '<b style="color:red;">' . Html::encode($fas->name) . ' Full, (' . $HeavyCheckResult['msg'] . ')</b>';
							}
						} else {
							$msg = '' . Html::encode($fas->name) . ' has space left (' . $HeavyCheckResult['msg'] . ' Lanes free)';
						}
					} else {
						$msg = '' . Html::encode($fas->name) . ' has space left, (' . ($Range_available_lanes - $Facl_Lanes_Used - $Facl_Lanes_Req) . ' Lanes free)';
					}
				}
				$full_msg .= $msg . ", ";
			}
			$returnMsg = ['status' => ($isAval) ? 'success' : 'error', 'msg' => rtrim($full_msg, ", "), 'lu' => $Facl_Lanes_Used, 'data' => (!empty($found)) ? $found : false];
		} else {
			$returnMsg = ['status' => 'success', 'msg' => 'Facility is Available', 'ln' => 447];
		}
		$returnMsg = array_merge($returnMsg, $inPattern);
		if ($internal) {
			return $isAval;
		} elseif (Yii::$app->request->isAjax) {
			Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			return $returnMsg;
		} else {
			return $this->render('test', ['pattern' => $pattern, 'returnMsg' => $returnMsg, 'rng_pri' => $rng_pri]);
		}
	}

	private function HeavyCheck($start_m, $stop_m, $facil_id, $ChkRng, $rng_requ, $rng_limit) {  // True = full! (bad)
		// Extract start and stop hours as clean integers for the loop control
		$start_hour = (int)substr($start_m, 0, 2);
		$stop_hour  = (int)substr($stop_m, 0, 2);

		// Pre-parse the absolute timestamps for comparison bounds to save CPU cycles
		$start_ts = strtotime($start_m);
		$stop_ts  = strtotime($stop_m);

		$max_used = $rng_requ;
		// Loop using integer arithmetic to completely avoid string comparison bugs
		for ($hr = $start_hour; $hr <= $stop_hour; $hr++) {
			// Guarantee a perfect 2-digit string format (e.g., 08, 09, 10)
			$chk_time = sprintf('%02d', $hr);

	//Yii::$app->controller->createCalLog(false, 'trex-Heavy_Req:565 ', $chk_time);
			foreach ([':01', ':16', ':31', ':46'] as $min) {
				$chk_lns_used = $rng_requ;
				$checking = $chk_time . $min;
				$checking_ts = strtotime($checking);

				// Skip if the checking interval falls outside your target start time
				if ($checking_ts < $start_ts) {
					continue;
				}

				// Skip if the checking interval falls outside your target stop time
				if ($checking_ts > $stop_ts) {
					continue;
				}

				// Check against reservations
				foreach ($ChkRng as $recheck) {
					$tst_start = strtotime($recheck->start);
					$tst_stop  = strtotime($recheck->stop);

					if (($tst_start < $checking_ts) && ($checking_ts < $tst_stop)) {
						$chk_lns_used += (int)$recheck->lanes_req[$facil_id];
					}
				}
	//Yii::$app->controller->createCalLog(false, 'trex-Heavy_Req:590 ', $checking . " - " . $chk_lns_used);

				if ($max_used < $chk_lns_used) {
					$max_used = $chk_lns_used;
				}

				if ($chk_lns_used > $rng_limit) {
					// Formatting time safely back from the Unix timestamp
					$display_time = date('h:i A', strtotime($checking . ' - 1 minute'));
					return [
						'status' => 'Full',
						'msg'    => ($chk_lns_used - $rng_limit) . ' lanes over at ' . $display_time
					];
				}
			}
		}
	//Yii::$app->controller->createCalLog(false, 'trex-Heavy_Req:606 ', 'Space! ' . $max_used);
		return ['status' => 'Open', 'msg' => ($rng_limit - $max_used) . ' Lanes'];
	}

	public function actionRecheckFutureConflicts() {  //  use with  /calendar/recheck-future-conflicts?year=2027&month=1&span=3
		Yii::$app->response->format = Response::FORMAT_JSON;

		$params = array_merge(Yii::$app->request->getQueryParams(), Yii::$app->request->getBodyParams());
		$year = filter_var($params['year'] ?? null, FILTER_VALIDATE_INT);
		$month = filter_var($params['month'] ?? null, FILTER_VALIDATE_INT);
		$span = filter_var($params['span'] ?? 1, FILTER_VALIDATE_INT);
		if ($year === false || $year < 1 || $year > 9999 || $month === false || !checkdate($month, 1, $year) || $span === false || $span < 1) {
			Yii::$app->response->statusCode = 400;
			return [
				'success' => false,
				'msg' => 'Provide a valid year, month (1-12), and positive span.',
			];
		}

		$monthStart = sprintf('%04d-%02d-01', $year, $month);
		$nextMonthStart = (new \DateTimeImmutable($monthStart))->modify("+{$span} months")->format('Y-m-d');
		$checked = 0;
		$newConflicts = 0;
		$clearedConflicts = 0;
		$conflictIds = [];

		$events = AgcCal::find()
			->where(['deleted' => 0])
			->andWhere(['<>', 'event_status_id', 19])
			->andWhere(['>=', 'event_date', $monthStart])
			->andWhere(['<', 'event_date', $nextMonthStart])
			->orderBy(['event_date' => SORT_ASC, 'cal_start_time' => SORT_ASC]);

		foreach ($events->each() as $event) {
			$hasConflict = !$this->actionOpenRange(
				$event->event_date,
				$event->cal_start_time,
				$event->cal_end_time,
				$event->facility_id,
				$event->lanes_req ?: '{}',
				$event->calendar_id,
				$event->recur_week_days ?: '',
				$event->event_status_id,
				true
			);
			$checked++;

			$newConflictValue = $hasConflict ? 1 : 0;
			if ((int)$event->conflict !== $newConflictValue) {
				$event->updateAttributes(['conflict' => $newConflictValue]);
				if ($hasConflict) {
					$newConflicts++;
				} else {
					$clearedConflicts++;
				}
			}

			if ($hasConflict) {
				$conflictIds[] = (int)$event->calendar_id;
			}
		}

		AgcCal::markConflicts($conflictIds);

		return [
			'success' => true,
			'Search Start' => $monthStart,
			'Search End' => $nextMonthStart,
			'span' => $span,
			'checked' => $checked,
			'newConflicts' => $newConflicts,
			'clearedConflicts' => $clearedConflicts,
			'conflictIds' => $conflictIds,
		];
	}

	public function actionRecur() {
		$searchModel = new AgcCalSearch();
		$searchModel->recur_every = true;
		$searchModel->deleted = 0;
		$this->RestoreSession($searchModel,'AgcCal',$this->myFilters);
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider ]);
	}

	public function actionRepublish($id,$force_order=false,$tst=false) {
		$model = AgcCal::find()->where(['calendar_id' => $id])->one();
		if (isset($model->recurrent_calendar_id)) {
			if (Yii::$app->request->isAjax) {
				// Why is this called via AJAX?  DO nothing...
			} else {
				if ($model->recurrent_calendar_id >0) {
					if ((int)$model->deleted == 1 ) { return json_encode(['status'=>'error','msg'=>"Event has been deleted, can't republish"]); }
					yii::$app->controller->createCalLog(true,  $_SESSION['user'], "Republishing event: ','".$model->event_name.'('.$model->calendar_id.')');

					if($model->event_status_id=19) {$model->event_status_id=1; $model->save(false);}
//					if($model->range_status_id=4) {$model->range_status_id=1; $model->save(false);}
					if ($force_order) {
						$nowTime = date('Y-01-01 00:00:00', strtotime(yii::$app->controller->getNowTime() . " + 1 year"));
					} else {
						$nowTime = yii::$app->controller->getNowTime();
					}
					$sql = "DELETE from cal_calendar where recurrent_calendar_id = ".$id." and  event_date >= '".$nowTime."'";
					$command = Yii::$app->db->createCommand($sql);
					$saveOut = $command->execute();

					$myRemarks = ['created_at'=>yii::$app->controller->getNowTime(),
						'changed'=>'Republished by '.$_SESSION['user'],
						'data'=>($force_order)? 'Forcing Priority':'Normal Priority'];
					$model->remarks = yii::$app->controller->mergeRemarks($model->remarks, $myRemarks);
					yii::$app->controller->createCalLog(true,  $_SESSION['user'], "Republishing event: ','Deleted ". var_export($saveOut,true)." Future Events");
					if($tst) {echo "Repb: rec_start: ".$model->recurrent_start_date.", Rec_end: ".$model->recurrent_end_date.", :Rec_PATTERN: ".$model->recur_week_days."<br /> /n"; }
					$myEventDates = $this->getEvents($model->recurrent_start_date,$model->recurrent_end_date,$model->recur_week_days,$tst,$force_order);

					$model = $this->createRecCalEvent($model,$myEventDates,$force_order,false,$tst);
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

	public function actionUpdate($id=1,$hideRepub=null) {
		$model = $this->findModel($id);
		if (!$model) { return $this->redirect(['index']); }

		if((!yii::$app->controller->hasPermission('calendar/all')) && (!in_array($model->club_id,json_decode(Yii::$app->user->identity->clubs)))) {
			Yii::$app->getSession()->setFlash('error', 'Not Your Event.');return $this->redirect(['/calendar/index']);
		}

		if ($model->load(Yii::$app->request->post())) {

			$model->cal_start_time 	= date('Y-m-d H:i:s', strtotime("$model->event_date $model->cal_start_time")) ;
			$model->cal_end_time	= date('Y-m-d H:i:s', strtotime("$model->event_date $model->cal_end_time")) ;

			$model->club_id = (int)$model->club_id;
			$model->event_status_id = (int)$model->event_status_id;
			$model->facility_id = str_replace('"', '',json_encode($model->facility_id));
			$model->range_status_id = (int)$model->range_status_id;

			$Req_Lanes = (new agcFacility)->getFacilRequiresLanes();
			foreach ($Req_Lanes as $chk_rng) {
				if (!empty($_POST['agccal-lanes_' . $chk_rng['facility_id']])) {
					$req_num = (int)($_POST['agccal-lanes_' . $chk_rng['facility_id']]);
					if($req_num > 0) { $reqLanesArray[$chk_rng['facility_id']] = $req_num; }
				}
			}
			if (!empty($reqLanesArray)) {$model->lanes_req = stripslashes(json_encode($reqLanesArray, JSON_UNESCAPED_SLASHES));}

			if ($this->actionOpenRange($model->event_date,$model->cal_start_time,$model->cal_end_time,$model->facility_id,$model->lanes_req,$model->calendar_id,$model->recur_week_days,$model->event_status_id,true)) {
				$model->conflict = 0; } else { $model->conflict = 1; }

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
					'changed'=> 'Updated by '.$_SESSION['user'],
					'data'=>"Updated: ".$dirty,
				];
				$model->remarks = yii::$app->controller->mergeRemarks($model->remarks, $myRemarks);
			}

			if($model->save()) {
				yii::$app->controller->createCalLog(true,  $_SESSION['user'], "Updated Calendar item: ','".$model->event_name.'('.$model->calendar_id.')');
				Yii::$app->getSession()->setFlash('success', 'Calendar Item has been updated');
				if(($model->recur_every) && ($model->recurrent_calendar_id == $model->calendar_id)) {
					// Master Record!!

					AgcCal::UpdateAll(['club_id'=>$model->club_id, 'event_name'=>$model->event_name, 'key_words'=>$model->key_words, 'recur_week_days'=>$model->recur_week_days], 'recurrent_calendar_id = '.$model->calendar_id);

					AgcCal::UpdateAll(['facility_id'=>$model->facility_id, 'lanes_req'=>$model->lanes_req, 'event_status_id'=>$model->event_status_id, 'range_status_id'=>$model->range_status_id,
						'cal_start_time'=>$model->cal_start_time, 'cal_end_time'=>$model->cal_end_time, 'deleted'=>$model->deleted, 'poc_badge'=>$model->poc_badge],
						"recurrent_calendar_id = ".$model->calendar_id." AND event_date >= '".date('Y-m-d',strtotime($this->getNowTime()))."'");

					yii::$app->controller->createCalLog(true,  $_SESSION['user'], "Updated Master Calendar item: ','".$model->event_name.'('.$model->calendar_id.')');
					if ((int)$model->event_status_id==19) { $model->range_status_id = 1; $model->save(); }

					if (isset($_POST['republish'])) {
						return $this->redirect(['republish','id' => $id]);
					}
				} else {
					if ((int)$model->event_status_id==19) { $model->range_status_id = 1; $model->save(); }
				}
			} else {
				yii::$app->controller->createCalLog(false, 'trex_B_C_CalC:749', 'save error');
				Yii::$app->getSession()->setFlash('error', 'Something Went Wrong');
			}
			yii::$app->controller->createCalLog(false, 'trex_B_C_CalC:752', 'updated');
			return $this->redirect(['update','id' => $id,'hideRepub'=>"no"]);


		} else {
			if(($_SESSION['badge_number']>0) && ($model->poc_badge==0)) { $model->poc_badge=$_SESSION['badge_number']; }

			return $this->render('update', [
				'model' => $model,
			]);
		}
	//} else { Yii::$app->getSession()->setFlash('error', 'Record Moved.');return $this->redirect(['/calendar/index']); }
	}

	public function actionView($id) {
		$model = $this->findModel($id);
		if ($model) {
			return $this->render('view', [
				'model' => $model,
			]);
		} else {
			return $this->redirect(['index']);
		}
	}

	public function actionViewitem($calendar_id) {
		$model = $this->findModel($calendar_id);
		if ($model) {
			return $this->render('viewitem', [
				'model' => $model,
			]);
		} else {
			return $this->redirect(['list']);
		}
	}

	public function loadDirtyFilds($model) {
		$model->deleted = (int)$model->deleted;
		$model->recur_every = (int)$model->recur_every;
		$model->recurrent_calendar_id = (int)$model->recurrent_calendar_id;
		$clean_json = trim($model->facility_id, "'");
		$model->facility_id = preg_replace('/\[\s*,/', '[', $clean_json);
		$items=$model->getDirtyAttributes();
		$obejectWithkeys = [
			'club_id' => 'Club',
			'conflict' => 'Conflict',
			'deleted' => 'Deleted',
			'date_requested' => 'Date Requested',
			'event_name' => 'Event Name',
			'event_date' => 'Event Date',
			'cal_start_time' => 'Start Time',
			'cal_end_time' => 'End Time',
			'event_status_id' => 'Event Status',
			'facility_id' => 'Facility',
			'key_words' => 'Key Words',
			'lanes_req' => 'lanes Requested',
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
				if($key=='facility_id'){
					$responce[] = 'Facility ('.(new AgcCal)->getAgcFacility_Names($model->facility_id).')'; }
				else { $responce[] = $obejectWithkeys[$key]; }
			}
		}
		sort($responce);
		return $responce;
	}

	private function GetPattern($post_data) {
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

	public function getEvents($eStart, $eEnd, $ePat, $eco=false, $rePub=false) {
		$whatYear= intval(date('Y'))+1;
$eco=false;
		if (strtotime($eStart) > strtotime($eEnd)) {  //start date before the end date [Nov thru Feb]
			if($rePub) {
				if($eco) { echo "Start E";}
				$myEventDatesC = $this->getEventDates($whatYear.'-01-01',$eEnd,$ePat,$whatYear,$eco);
				$myEventDatesD = $this->getEventDates($eStart,$whatYear.'-12-31',$ePat,$whatYear,$eco);
				$datesFound = array_merge($myEventDatesC,$myEventDatesD);
			}
			elseif (strtotime(yii::$app->controller->getNowTime()) > strtotime(date('Y').'-06-01 00:00:00')) {
				if($eco) { echo "Start B";}
				$myEventDatesA = $this->getEventDates(date('Y').'-01-01',$eEnd,$ePat,date('Y'),$eco);
				$myEventDatesB = $this->getEventDates($eStart,date('Y').'-12-31',$ePat,date('Y'),$eco);
				$myEventDatesC = $this->getEventDates($whatYear.'-01-01',$eEnd,$ePat,$whatYear,$eco);
				$myEventDatesD = $this->getEventDates($eStart,$whatYear.'-12-31',$ePat,$whatYear,$eco);
				$datesFound = array_merge($myEventDatesA,$myEventDatesB,$myEventDatesC,$myEventDatesD);
			} else {
				if($eco) { echo "Start A";}
				$myEventDatesA = $this->getEventDates(date('Y').'-01-01',$eEnd,$ePat,$whatYear,$eco);
				$myEventDatesB = $this->getEventDates($eStart,date('Y').'-12-31',$ePat,$whatYear,$eco);
				$datesFound = array_merge($myEventDatesA,$myEventDatesB);
			}
		} else {  // normal date run [Feb thru June]
			if ($rePub) {
				if($eco) { echo "Start F";}
				$datesFound = $this->getEventDates($eStart,$eEnd,$ePat,$whatYear,$eco);
			}
			elseif (strtotime(yii::$app->controller->getNowTime()) > strtotime(date('Y').'-06-01 00:00:00')) { // rollover
				if($eco) { echo "Start D";}
				$myEventDatesA = $this->getEventDates($eStart,$eEnd,$ePat,date('Y'),$eco);
				$myEventDatesB = $this->getEventDates($eStart,$eEnd,$ePat,$whatYear,$eco);
				$datesFound = array_merge($myEventDatesA,$myEventDatesB);
			} else {
				if($eco) { echo "Start C";}
				$datesFound = $this->getEventDates($eStart,$eEnd,$ePat,date('Y'),$eco);
			}
		}
		if($eco) {yii::$app->controller->createCalLog(true, 'trex_B_C_CalC:903', var_export($datesFound,true));}
		return $datesFound;
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
	yii::$app->controller->createCalLog(true, 'trex_B_C_CalC:920', var_export($myPat,true));
	echo " <hr> <br>"; }

		if (isset($myPat->daily)) {
 if($eco) { echo "Daily<br>"; }
			if ($myPat->daily=='wd') {
					for ($i = $Date_Start; $i <= $Date_Stop; $i = strtotime('+1 day', $i)) {
if($eco) { echo '<br>'.strtolower(date('D', $i)) ; }
						if (!in_array(strtolower(date('D', $i)),['sat','sun'])) {
if($eco) { echo "** ".date('Y-m-d',$i); }
							if ($i >=strtotime($today)) {
								array_push($myEventDates,date('Y-m-d',$i));
							}
						}
					}
			} else {
				for ($i = $Date_Start; $i <= $Date_Stop; $i = strtotime('+'.$myPat->daily.' day', $i)) {
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
			for ($i = $Date_Start; $i <= $Date_Stop; $i = strtotime('+1 day', $i)) {
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
			for ($i = date('m',$Date_Start); $i <= date('m',$Date_Stop)+1; $i++) {
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
if($eco) { echo "801: ".date("D, d-M-Y", $myMonth)." >= ".$today." <br>"; }
					if ($myMonth >= strtotime($today)) {
if($eco) { echo date("D, d-M-Y", $myMonth).":<br>"; }
						array_push($myEventDates,date('Y-m-d',$myMonth));
					} else {
if($eco) { echo "806: date passed,<br />";}
					}
				} elseif ($skip_now==true) {
if($eco) { echo "Skipping : ".date("D, d-M-Y", $myMonth).":<br>"; }
				} else {
if($eco) { echo "else 811<br>"; }
				}
				$cnt++;
			}
		}

		elseif (isset($myPat->yearly)) {
if($eco) { echo "yearly <br/>"; }
			if($myPat->yearly == 'date') {
				$myYear = $whatYear.'-'.str_pad($myPat->mon, 2, '0', STR_PAD_LEFT).'-'.str_pad($myPat->day, 2, '0', STR_PAD_LEFT);
			} else {
				$myYear = strtotime($myPat->on." ".$myPat->day.' '.$whatYear.'-'.str_pad($myPat->of, 2, '0', STR_PAD_LEFT));
				if (date('d',strtotime("first ".$myPat->day." $whatYear-".str_pad($myPat->of, 2, '0', STR_PAD_LEFT)))=='08') {
					$myYear = $myYear-(60*60*24*7); }
				$myYear = date("Y-m-d",$myYear);
			}
if($eco) { echo "827: $myYear ".strtotime($myYear)." >= ".strtotime($today)." $today <br />";}
			if (strtotime($myYear) >= strtotime($today)) {
if($eco) { echo "using $myYear<br/>"; }
				array_push($myEventDates,$myYear);
			}
		}
		else { echo "broke?? WTF???"; exit; }
		return $myEventDates;
	}

	private function createRecCalEvent($model,$myEventDates,$force_order=false,$is_new=false,$tst=false) {
		$NewID = false; $first_id=false;
if($tst) { if ($force_order) {yii::$app->controller->createCalLog(true, 'trex_B_C_CalC:1021','forcing_Order RecCalEvent');} }
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
			$model_event->key_words 		= $model->key_words;
			$model_event->cal_start_time	 	= $model->cal_start_time;
			$model_event->cal_end_time 			= $model->cal_end_time;
			$model_event->date_requested 	= $model->date_requested;
			$model_event->lanes_req		 	= $model->lanes_req;
			$model_event->recur_every 		= $model->recur_every;
			$model_event->recur_week_days 	= $model->recur_week_days;
			$model_event->recurrent_start_date = $model->recurrent_start_date;
			$model_event->recurrent_end_date = $model->recurrent_end_date;
			$model_event->event_status_id 	= $model->event_status_id;
			$model_event->range_status_id 	= $model->range_status_id;
			if ($this->actionOpenRange($eDate,$model_event->cal_start_time,$model_event->cal_end_time,$model_event->facility_id,$model_event->lanes_req,0,$model->recur_week_days,$model->event_status_id,true,$force_order,$tst)) {
				$model_event->conflict = 0;
			} else {
				$model_event->conflict = 1;
			}
			$model_event->deleted 			= $model->deleted;
			if(!$model->rollover) { $model_event->rollover = 0; } else { $model_event->rollover = $model->rollover; }
			$model_event->time_format 		= 1;
			$model_event->poc_badge 		= $model->poc_badge;
			$model_event->remarks 			= $model->remarks;
			$model_event->save();
			if(!$first_id) {$first_id=$model_event->calendar_id;}
			yii::$app->controller->createCalLog(true, $_SESSION['user'], "Created New Calendar item: ','".$model_event->calendar_id.'->'.$model_event->event_name);

			if (intval(substr($eDate,0,4)) > intval(date('Y'))){
				$NewID = $model_event->calendar_id;
			}
		}

		$CalidExist = (new AgcCal)->find()->where(['calendar_id'=>$model->calendar_id])->one();
		if (($NewID) || (!$CalidExist)) {
			if (!$NewID) {$NewID=$first_id;}
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
