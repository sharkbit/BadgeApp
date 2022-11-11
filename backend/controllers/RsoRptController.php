<?php

namespace backend\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\widgets\ActiveForm;
use backend\controllers\AdminController;
use backend\models\Params;
use backend\models\RsoReports;
use backend\models\Stickers;
use backend\models\search\RsoReportsSearch;
use backend\models\search\StickersSearch;

/**
 * ParamsController implements the CRUD actions for RsoReports model.
 */
class RsoRptController extends AdminController {
	/**
	 * @inheritdoc
	 */

	public function actionCurrent() {
		if (Yii::$app->request->isAjax){
			$model = $this->findModel($_POST['RsoReports']['id']);
			if(!$model->load(Yii::$app->request->post())) {
				yii::$app->controller->createLog(true, 'trex-notload', 'not loaded?');
			}
			$model->remarks=$this->AddRemarks($model,'AutoSave for '.$_SESSION['user']);
			$model->save();
			Yii::$app->response->format = Response::FORMAT_JSON;
			return ActiveForm::validate($model);
		}
		elseif(Yii::$app->request->post()) {
			$model = $this->findModel($_POST['RsoReports']['id']);
			if(!$model) {$model = new RsoReports;}
			$model->load(Yii::$app->request->post());
			$model->remarks=$this->AddRemarks($model,'Updated by '.$_SESSION['user']);
			if($model->save()) {
				if($model->closed==1) {
					return $this->redirect(['index']);
				}
			} else {
				Yii::$app->getSession()->setFlash('error', json_encode($model->errors));
				yii::$app->controller->createLog(false, 'trex-c-RSO-rpt:33 NOT VALID', var_export($model->errors,true));
				return $this->render('current', [
					'model' => $model,
				]);
			}
		}
		elseif(Yii::$app->request->get()) {
			if($_GET['close']==1) {
				$model = $this->findModel($_GET['id']);
				$model->closed = 1;
				$model->date_close = $this->getNowTime();
				$model->remarks=$this->AddRemarks($model,'Closed By '.$_SESSION['user']);
				if ($model->save()) {
					$this->createLog($this->getNowTime(), $this->getActiveUser()->username, 'Closed RSO Report: '.$model->id);
					$this->SendNotification($model);
					return $this->redirect(['index']);
				} else {
					Yii::$app->getSession()->setFlash('error', json_encode($model->errors));
					yii::$app->controller->createLog(false, 'trex-c-RSO-rpt:50 NOT VALID', var_export($model->errors,true));
					return $this->render('current', [
						'model' => $model,
					]);
				}
			}
		}
		$model =  (new RsoReports)->find()->where(['closed'=>0])->orderBy(['date_open'=>SORT_DESC])->one();

		if((!$model) && (array_intersect([3,6],$_SESSION['privilege']))) {
			$model = new RsoReports;
			$model->date_open = $this->getNowTime();
			$model->remarks=$this->AddRemarks($model,'Opened By '.$_SESSION['user']);
			$model->save(false);
			$this->createLog($this->getNowTime(), $this->getActiveUser()->username, 'Opened RSO Report: '.$model->id);
			return $this->render('current', [ 'model' => $model, ]);
		} elseif (!$model) {
			Yii::$app->getSession()->setFlash('error', "RSOs Must Open Report");
			return $this->redirect('index');
		} elseif (array_intersect([1,3,6],$_SESSION['privilege'])) {
			return $this->render('current', [ 'model' => $model, ]);
		} else {
			Yii::$app->getSession()->setFlash('error', "RSOs Only");
			return $this->redirect('index');
		}
	}

	public static function OpenReport($from=false) {
		if(array_intersect([3,6],$_SESSION['privilege'])) {
			$model =  (new RsoReports)->find()->where(['closed'=>0])->orderBy(['date_open'=>SORT_DESC])->one();
			if(!$model) {
				$model = new RsoReports;
				$model->date_open = yii::$app->controller->getNowTime();
				$model->remarks = RsoRptController::AddRemarks($model,'Opened By '.$_SESSION['user']);
				$model->save(false);
				yii::$app->controller->createLog(yii::$app->controller->getNowTime(), yii::$app->controller->getActiveUser()->username, 'Opened RSO Report: '.$model->id);
				yii::$app->getSession()->setFlash('info', "RSO Report Opened");
			}
		}
	}

	public function actionDelete($id=1) {
		$model = $this->findModel($id);
		if($model->delete()) {
			Yii::$app->getSession()->setFlash('success', 'Report Deleted.');
			yii::$app->controller->createLog(true, $_SESSION['user'],"RSO Report','RSO Report #$id Deleted");

		} else {
			Yii::$app->getSession()->setFlash('error', 'Record not deleted!');
		}
		return $this->redirect(['index']);
	}

	public function actionHelp() {
		return $this->render('help');
	}

	public function actionIndex() {
		$searchModel = new RsoReportsSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

    public function actionSettings($id=1) {
        $model = Params::findOne($id);

        if ($model->load(Yii::$app->request->post())) {
			$model->rso_email = json_encode($model->rso_email);
			$model->save();
            return $this->redirect(['settings']);
        } else {
            return $this->render('settings', [
                'model' => $model,
            ]);
        }
    }

	public function actionSticker() {
		if (isset($_REQUEST['sticker_add']) && ($_REQUEST['sticker_add']==1)) {
			$x = (int)$_REQUEST['StickersSearch']['start'];
			$yr = (int)$_REQUEST['StickersSearch']['yr'];
			yii::$app->controller->createLog(true, $_SESSION['user'],"Sticker','Adding ".$yr.' - '.$_REQUEST['StickersSearch']['start'].' - '.$_REQUEST['StickersSearch']['end']);
			$chk = ArrayHelper::getColumn(Stickers::find()->where(['like','sticker',$yr."%",false])->all(),'sticker');
			do {
				$new_stkr = $yr.'-'.str_pad($x, 4, '0', STR_PAD_LEFT);
				if(in_array($new_stkr,$chk)) { $x++; continue; }
				$stkr = new Stickers;
				$stkr->sticker = $new_stkr;
				$stkr->status = 'adm';
				$stkr->save();
				$x++;
			} while ($x < (int)$_REQUEST['StickersSearch']['end']+1);
		}

		if (isset($_REQUEST['sticker_move']) && ($_REQUEST['sticker_move']==1)) {
			$yr = (int)$_REQUEST['StickersSearch']['yr_mv'];
			yii::$app->controller->createLog(true, $_SESSION['user'],"Sticker','Moving ".$yr.' - '.$_REQUEST['StickersSearch']['stkrs'].' to '.$_REQUEST['StickersSearch']['to']);
			$moving=explode(',',$_REQUEST['StickersSearch']['stkrs']);

			foreach($moving as $rng) {
				if(strpos($rng,'-')) {
					$rng_a = explode('-',$rng);
					$y=$rng_a[0];
					do {
						$stker = Stickers::find()->where(['sticker'=>$yr.'-'.str_pad($y, 4, '0', STR_PAD_LEFT)])->andwhere(['in','status',['rso','adm','cas']])->one();
						if($stker){
							$stker->status = $_REQUEST['StickersSearch']['to'];
							$stker->updated =  $this->getNowTime();
							$stker->save();
						}
						$y++;
					} while ($y < $rng_a[1]+1);
				} else {
					$stker = Stickers::find()->where(['sticker'=>$yr.'-'.str_pad($rng, 4, '0', STR_PAD_LEFT)])->andwhere(['in','status',['rso','adm','cas']])->one();
					if($stker) {
						$stker->status = $_REQUEST['StickersSearch']['to'];
						$stker->updated =  $this->getNowTime();
						$stker->save();
					}
				}
			}
		}

		$searchModel = new StickersSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		if ((in_array(3, json_decode(yii::$app->user->identity->privilege))) && (in_array(10, json_decode(yii::$app->user->identity->privilege)))) {
			$dataProvider->query->andWhere("status='rso' OR status='cas'");
		}
		elseif (array_intersect([3,6], json_decode(yii::$app->user->identity->privilege))) {
			$dataProvider->query->andWhere(['status'=>'rso']);
		}

		return $this->render('stickers', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionStickerUpdate($id=1) {
		$model =  (new Stickers)->find()->where(['s_id'=>$id])->one();
		if($model) {
		if ($model->load(Yii::$app->request->post())) {
			$model->updated = $this->getNowTime();
			if($model->save()) {
				yii::$app->controller->createLog(true, $_SESSION['user'],"Sticker','Updated ".$model->sticker);
				return $this->redirect(['rso-rpt/sticker']);
			}
		}
		return $this->render('/rso-rpt/sticker-update',[
				'model' => $model,
			]);
		} else  { $this->redirect(['/rso-rpt/sticker']); }
	}

	public function actionStickerDelete($id=1) {
		Yii::$app->getSession()->setFlash('error', 'Do you really want This?  Function not written yet.');
		//Verify no user has selected permission
			//delete if none
		return $this->redirect(['/rso-rpt/sticker']);
	}

	public function actionUpdate($id=1) {
		$model = $this->findModel($id);
		if($model) {
			if ($model->load(Yii::$app->request->post())) {
				if (!$model->validate()) {
					// uncomment the following line if you do not want to return any records when validation fails
					// $query->where('0=1');
					yii::$app->controller->createLog(false, 'trex-m-s-bs:112 NOT VALID', var_export($model->errors,true));
				}
				$model->remarks=$this->AddRemarks($model,'Updated by '.$_SESSION['user']);
				if($model->save()) {
					Yii::$app->getSession()->setFlash('success', 'Report has been updated.');
				 } else {
					 yii::$app->controller->createLog(false, 'trex-m-s-bs:112 NOT VALID', var_export($model->errors,true));
				 }
				return $this->redirect(['update', 'id' => $model->id]);
			} else {
				return $this->render('update', [
					'model' => $model,
				]);
			}
		} else { return $this->redirect(['index']); }
	}

	public function actionView($id=1) {
		$model = RsoReports::find()->where(['id'=>$id])->one();
		if ($model) {
			return $this->render('view', [
				'model' => $model,
			]);
		} else {
			Yii::$app->getSession()->setFlash('error', 'Report not found.');
			return $this->redirect(['index']);
		}
	}

	private static function CleanModel(&$model) {
		$model->closed=(int)$model->closed;
		$model->rso = str_replace('"',"", json_encode($model->rso));
		$model->wb_trap_cases = (int)$model->wb_trap_cases;
		$model->cash_bos = number_format((float)$model->cash_bos,2,'.','');
		$model->cash_eos = number_format((float)$model->cash_eos,2,'.','');
		$model->cash_drop = number_format((float)$model->cash_drop,2,'.','');
		$model->cash = trim($model->cash);
		$model->checks = trim($model->checks);
		$model->notes = trim($model->notes);
		$model->shift_anom = trim($model->shift_anom);
		$model->closing = trim($model->closing);
	}

	protected static function AddRemarks($model, $comment) {
		RsoRptController::CleanModel($model);

		$items=$model->getDirtyAttributes();
		$obejectWithkeys = [
			'mics'=>'MICs Status',
			'rso' => "RSO's",
			'shift_anom'=> 'Shift Anomalies',
			'notes'=>'Notes',
			'cash_bos'=>'Cash BOS',
			'cash_drop'=>'Cash Dropped',
			'cash_eos'=>'Cash EOS',
			'closing'=>'Closing Notes',
			'closed'=>'Closed',
			'wb_trap_cases'=>' Wobble Trap Cases',
			'wb_color'=> 'Wristband Color',
		];

		$responce = [];
		foreach($items as $key => $item) {
			if(array_key_exists($key,$obejectWithkeys)) {
				$responce[] = $obejectWithkeys[$key];
			}
		}
		sort($responce);
		$dirty=implode(", ",$responce);

		$remarksOld = json_decode($model->remarks,true);
		if($dirty) {
			$cmnt = "Updated: ".$dirty;
			$nowRemakrs = [
				'created_at' => yii::$app->controller->getNowTime(),
				'data' => $cmnt,
				'changed' => $comment,
			];

			if($remarksOld != '') {
				array_push($remarksOld,$nowRemakrs);
			} else {
				$remarksOld = [
					$nowRemakrs,
				];
			}
		}
		return json_encode($remarksOld,true);
	}

	public function getYear() {
		$yr = date('Y');
		return [($yr-1)=>$yr-1,$yr=>$yr,$yr+1=>$yr+1];
	}

	protected function findModel($id) {
		if (($model = RsoReports::findOne($id)) !== null) {
			return $model;
		} else {
			return false;
		}
	}

	protected function SendNotification($model) {
		$param = Params::findOne('1');
		$emailz = json_decode($param->rso_email);
		if ($emailz) {
			$email = AdminController::emailSetup();
			if (!$email) {
				Yii::$app->getSession()->setFlash('error', 'Email System disabled'); echo "email-setup failed";
				yii::$app->controller->createLog(true, 'Email:', 'Disabled, cant Send RSO Report');
				return false;
			}

			$rsos=json_decode($model->rso);
			$names='';
			if($rsos) {
				foreach ($rsos as $badge) {
					$names .= yii::$app->controller->decodeBadgeName((int)$badge).', ';
				}
			}
			if($model->shift=='m') { $shift='Morning'; } else { $shift='Evening'; }
			$remarks=json_decode($model->remarks);
			$remark='';
			if($remarks) {
				foreach ($remarks as $item) {
					$remark .= $item->created_at.' - '.$item->changed.' - '.$item->data."<br /> \n";
				}
			}
			switch ($model->wb_color){
				case 'g': $wb='Green'; break;
				case 'b': $wb='Blue'; break;
				case 'r': $wb='Red'; break;
				case 'l': $wb='Lavender'; break;
				case 'k': $wb='Black'; break;
				default: $wb='';
			}
			switch ($model->mics){
				case 'o': $mics='Mics Set Out'; break;
				case 's': $mics='Mics stored in closet'; break;
				case 't': $mics='Mics in Trap 3'; break;
				default: $mics='';
			}

			foreach($emailz as $sendTo) {
				try {
					$email->setFrom(yii::$app->params['mail']['Username'], 'AGC Range');
					$email->addAddress($sendTo);
					$email->Subject = $subj = 'RSO Report: '.$model->date_open;
					$url = yii::$app->params['badge_site']."/rso-rpt/view?id=".$model->id;
					$email->Body = "<p>Hello,</p>\n".
						"<p> RSO Report has been Finalized, link below:</p>\n".
						"<p>&emsp; <a href=\"".$url."\">".$url."</a></p>\n".
						"<p>By ". $_SESSION['user']."</p><hr>".
						"<table border=1>\n<thead><tr><th>item</th><th>details</th></tr></thead>\n<tbody>\n".
						"<tr><td>RSO's </td><td>$names</td></tr>".
						"<tr><td>Date Open </td><td>$model->date_open </td></tr>".
						"<tr><td>Shift </td><td>".$shift."</td></tr>".
						"<tr><td>Date Closed </td><td>".$model->date_close."</td></tr>".
						"<tr><td>Cash BOS </td><td>".$model->cash_bos."</td></tr>".
						"<tr><td>Cash Dropped </td><td>".$model->cash_drop."</td></tr>".
						"<tr><td>Cash EOS </td><td>".$model->cash_eos."</td></tr>".
						"<tr><td>Wobble Trap Cases </td><td>".$model->wb_trap_cases."</td></tr>".
						"<tr><td>Wristband Color </td><td>".$wb."</td></tr>".
						"<tr><td>MICs Status </td><td>".$mics."</td></tr>".
						"<tr><td>Notes </td><td>".nl2br($model->notes)."</td></tr>".
						"<tr><td>Shift Anomalies </td><td>".nl2br($model->shift_anom)."</td></tr>".
						"<tr><td>Pass Down </td><td>".nl2br($model->closing)."</td></tr>".
						"<tr><td>Stickers </td><td>".$model->stickers."</td></tr>".
						"<tr><td>Cash Sales </td><td>".nl2br($model->cash)."</td></tr>".
						"<tr><td>Check Sales </td><td>".nl2br($model->checks)."</td></tr>".
						"<tr><td>Violations </td><td>".$model->violations."</td></tr>".
						"<tr><td>Participation </td><td>".
						"<table border=1 width=100%><tr><td> 50 yrd </td><td>$model->par_50</td><td> 100 yrd </td><td>$model->par_100</td><td> 200 yrd </td><td>$model->par_200</td><td> Steel </td><td>$model->par_steel</td></tr>".
						"<td> N/M Hunter Qual </td><td>$model->par_nm_hq</td><td> M Hunter Qual </td><td>$model->par_m_hq</td><td> Trap </td><td>$model->par_trap</td><td> Archery </td><td>$model->par_arch</td></tr>".
						"<td> Pellet </td><td>$model->par_pel</td><td> SG Ptrn Rnr </td><td>$model->par_spr</td><td> CIO Students </td><td>$model->par_cio_stu</td><td> Action Rng </td><td>$model->par_act</td></tr></table>".
						"</td></tr>".
						"<tr><td>Changes </td><td>".nl2br($remark)."</td></tr>".
						"</tbody>\n</table>";
					$email->send();
					$email->ClearAddresses();
					yii::$app->controller->createEmailLog(true, 'RSOreport-Email', "Sent to ".$sendTo.', '.$subj);
				} catch (Exception $e) {
					//echo 'Message could not be sent.';
					//echo 'Mailer Error: ' . $email->ErrorInfo;
					yii::$app->controller->createEmailLog(true, 'RSOreport Email Error: ', var_export($email->ErrorInfo,true));
				}
			}
		}
		return true;
	}
}
