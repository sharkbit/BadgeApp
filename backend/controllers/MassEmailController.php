<?php

namespace backend\controllers;

use Yii;
use backend\models\Badges;
use backend\models\MassEmail;
use backend\models\search\ParamsSearch;
use backend\models\search\MassEmailSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\controllers\AdminController;

/**
 * ParamsController implements the CRUD actions for MassEmail model.
 */
class MassEmailController extends AdminController {
    /**
     * @inheritdoc
     */
    public function behaviors()     {
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
		$model = new MassEmail();
        if ($model->load(Yii::$app->request->post())){
			$model->mass_created = date('Y-m-d H:i:s', strtotime($this->getNowTime()));
			$model->mass_created_by =  $_SESSION['badge_number'];

			if(($_REQUEST['MassEmail']['to_active']) || ($_REQUEST['MassEmail']['to_expired'])) {
				$model->mass_to='';
				if ($_REQUEST['MassEmail']['to_active']) {
					$model->mass_to .= '*A'; }
				if ($_REQUEST['MassEmail']['to_expired']) {
					$model->mass_to .= '*E'; }
			} else {
					$model->mass_to = $_REQUEST['MassEmail']['to_email'];
			}

			if ($model->save()) {
				$this->createLog($this->getNowTime(), $_SESSION['user'], 'New Mas Email Created: '.$model->mass_subject);
				Yii::$app->getSession()->setFlash('success', $model->mass_subject.' has been created');
				return $this->redirect(['update', 'id' => $model->id]);
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

	public function actionIndex() {
        $searchModel = new MassEmailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 100;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

	public function actionProcess($id) {
		if (!$id) { exit; }
		$model = MassEmail::findOne($id);

		if ($model->mass_running == True) {
			
		//	$timenow = strtotime($this->getNowTime());
		//	$timetest = strtotime('+20 minutes', strtotime($model->mass_runtime));
		
		//	if ($timenow < $timetest) {
				yii::$app->controller->createEmailLog(true, 'Mass-Email:process', 'Already Running...');
				Yii::$app->response->data .= "Emailer Running."; 
				return "Process: Emailer Running.";
		} else {
			$model->mass_running = True;
			$model->save(false);
		}
		
		yii::$app->controller->createEmailLog(true, 'Mass-Email', 'Start');
		$email = AdminController::emailSetup();

		$active_date = date('Y-m-d', strtotime($this->getNowTime()));
		if(strpos(" ".$model->mass_to,'@')) {
			if(strpos($model->mass_to,';')) {
				$emails=explode(';',$model->mass_to);
			} else { $emails = array($model->mass_to); }
			$members=[];
			foreach($emails as $anEmail) {
				if (filter_var(trim($anEmail), FILTER_VALIDATE_EMAIL)) {
					yii::$app->controller->createEmailLog(true, 'Mass-Email', 'single ex02');
					$TstEmail = new Badges();
					$TstEmail -> badge_number = 0;
					$TstEmail -> email = trim($anEmail);
					$TstEmail->status = "approved";
					array_push($members,$TstEmail) ;
					Yii::$app->response->data .= "to addr: ".$TstEmail -> email."<br />\n";
				} else {
					Yii::$app->response->data .= "Bad Email <br />\n"; 
					yii::$app->controller->createEmailLog(true, 'Mass-Email', 'single err');
				}
			}
		} elseif(strpos(" ".$model->mass_to, '*A') && strpos(" ".$model->mass_to, '*E')) {
			$members = Badges::find()->where(['not',['email'=>null]])->andWhere('email_vrfy=1')->orderBy(['badge_number' => SORT_ASC])->all();
		} elseif(strpos(" ".$model->mass_to, '*A')) {
			$members = Badges::find()->where(['not',['email'=>null]])->andWhere(['>', 'expires', $active_date])->andWhere('email_vrfy=1')->orderBy(['badge_number' => SORT_ASC])->all();
		} elseif(strpos(" ".$model->mass_to, '*E')) {
			$members = Badges::find()->where(['not',['email'=>null]])->andWhere(['<', 'expires', $active_date])->andWhere('email_vrfy=1')->orderBy(['badge_number' => SORT_ASC])->all();
		} else { Yii::$app->response->data .= "Nothing Checked <br />\n"; exit; }

		$model->mass_start =  date('Y-m-d H:i:s', strtotime($this->getNowTime()));
		$model->mass_running = true;
		$model->save(false);

		foreach ($members as $key => $value) {
			//Auto Continue (if it breaks?)
			if(($model->mass_lastbadge) && $value['badge_number'] <= $model->mass_lastbadge) { continue; }
			$model->mass_lastbadge = $value['badge_number'];
			$model->mass_runtime =  date('Y-m-d H:i:s', strtotime($this->getNowTime()));
			$model->save(false);

			$mail='';
			if (filter_var($value['email'], FILTER_VALIDATE_EMAIL)) {

				if ($value['status']=='approved' || $value['status']=='pending' || isset($_POST['email_test'])) {
					//yii::$app->controller->createEmailLog(true, 'Mass-Email', $value['email'].', '.$value['expires'].', '.$value['badge_number']);
					try {
						$mail = AdminController::emailSetup();
						$mail->addCustomHeader('List-Unsubscribe', '<https://agcrange.org/comms.php?unsubscribe='.$value['email'].'>');

						if($model->mass_reply_to) {
							$mail->AddReplyTo($model->mass_reply_to, $model->mass_reply_name);
						} else { $mail->AddReplyTo('president@agcrange.org', 'AGC President'); }
						$mail->setFrom('noreply@associatedgunclubs.org', 'AGC Range');

						$mail->addAddress($value['email']);

						$mail->Subject = $model->mass_subject;
						$mail->Body    = $model->mass_body.
						'<br /><br />< <a href="https://agcrange.org/comms.php?unsubscribe='.$value['email'].'"> UnSubscribe from the AGC mailer</a> >';
						sleep(3);  // Default 3,  Throttled by cPanel 2000 Emails per Hour
						$mail->send();
						//echo ->  Yii::$app->response->data = "Message has been sent<br />?n";
						yii::$app->controller->createEmailLog(true, 'Mass-Email', "Sent to ".$value['email'].', '.$value['expires'].', '.$value['badge_number']);
					} catch (Exception $e) {
						//echo 'Message could not be sent.';
						//echo 'Mailer Error: ' . $mail->ErrorInfo;
						yii::$app->controller->createEmailLog(true, 'Mass-Email Email Error: ', var_export($mail->ErrorInfo,true));
					}
				} else { Yii::$app->response->data .= "Duah<br>\n"; }
			} else { Yii::$app->response->data .= "not valid Email <br>\n"; }
		}
		$model->mass_running = 2;
		$model->mass_finished= date('Y-m-d H:i:s', strtotime($this->getNowTime()));
		$model->mass_lastbadge = 99999;
	if ($model->save(false)) {} else { Yii::$app->response->data .= "save error<br>\n"; var_dump($model->errors); }

		yii::$app->controller->createEmailLog(true, 'Mass-Email', 'Fin');
		Yii::$app->response->data .= "\nFin";
	}

	public function actionSend($id,$resend=false) {
		if (!$id) { return $this->redirect(['index']); }
yii::$app->controller->createEmailLog(true, 'Mass-Email:send', 'about to');

		$model = MassEmail::findOne($id);
		if($resend) {
			$model->mass_running=0;
			$model->mass_lastbadge=null;
		} elseif ($model->mass_running == True) {
			//Yii::$app->response->data .= "Emailer Running."; 
			return "Send: Emailer Running.";
		} else {
			// Run!!
		}

		if ($model->load(Yii::$app->request->post())){
			if(($_REQUEST['MassEmail']['to_active']) || ($_REQUEST['MassEmail']['to_expired'])) {
				$model->mass_to='';
				if ($_REQUEST['MassEmail']['to_active']) {
					$model->mass_to .= '*A'; }
				if ($_REQUEST['MassEmail']['to_expired']) {
					$model->mass_to .= '*E'; }
			} else {
				$model->mass_to = $_REQUEST['MassEmail']['to_email'];
			}
			$model->mass_updated = date('Y-m-d H:i:s', strtotime($this->getNowTime()));
			$model->mass_updated_by = $_SESSION['badge_number'];

			$model->save(false);
		}

		shell_exec('wget -O/dev/null -q  '. yii::$app->params['rootUrl'].'/mass-email/process?id='.$id);
		yii::$app->controller->createEmailLog(true, 'Mass-Email:send', 'Send Start!');
		return json_encode(['ststus'=>'Success','msg'=>'Processing was started']);
	}

    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

			$model->mass_updated = date('Y-m-d H:i:s', strtotime($this->getNowTime()));
			$model->mass_updated_by =  $_SESSION['badge_number'];
			if(($_REQUEST['MassEmail']['to_active']) || ($_REQUEST['MassEmail']['to_expired'])) {
				$model->mass_to='';
				if ($_REQUEST['MassEmail']['to_active']) {
					$model->mass_to .= '*A'; }
				if ($_REQUEST['MassEmail']['to_expired']) {
					$model->mass_to .= '*E'; }
			} else {
				$model->mass_to = $_REQUEST['MassEmail']['to_email'];
			}
			if ($model->save()) { } else { yii::$app->controller->createLog(false, 'trex-Cme-save error', var_export($model->errors,true));
			}
            return $this->render('update', [ 'model' => $model ]);
        } else {
            return $this->render('update', [ 'model' => $model ]);
        }
    }

    protected function findModel($id) {
        if (($model = MassEmail::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
