<?php

namespace backend\controllers;

use Yii;
use backend\models\Badges;
use backend\models\MassEmail;
use backend\models\Officers;
use backend\models\User;
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
					$model->mass_to = rtrim($_REQUEST['MassEmail']['to_email'],";");
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

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	public function actionProcess($id) {
		if (!$id) {return $this->redirect('index'); }
		$model = MassEmail::findOne($id);

		if(!$model) {return $this->redirect('index'); }
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
		if (!$email) {
			Yii::$app->getSession()->setFlash('error', 'Email System disabled'); echo "email-setup failed";
			yii::$app->controller->createEmailLog(true, 'Mass-Email:', 'Disabled');
			$model->mass_running = false;
			$model->save(false);
			return;
		}
		$active_date = date('Y-m-d', strtotime($this->getNowTime()));

		$members=[];
		if(strpos(" ".$model->mass_to, '*A') && strpos(" ".$model->mass_to, '*E')) {
			$getMembers = Badges::find()->where(['not',['email'=>null]])->andWhere('email_vrfy=1')->orderBy(['badge_number' => SORT_ASC])->all();
			array_push($members,$getMembers) ;
		} elseif(strpos(" ".$model->mass_to, '*A')) {
			$getMembers = Badges::find()->where(['not',['email'=>null]])->andWhere(['>', 'expires', $active_date])->andWhere('email_vrfy=1')->orderBy(['badge_number' => SORT_ASC])->all();
			array_push($members,$getMembers) ;
		} elseif(strpos(" ".$model->mass_to, '*E')) {
			$getMembers = Badges::find()->where(['not',['email'=>null]])->andWhere(['<', 'expires', $active_date])->andWhere('email_vrfy=1')->orderBy(['badge_number' => SORT_ASC])->all();
			array_push($members,$getMembers) ;
		}

		yii::$app->controller->createEmailLog(false, 'Mass-Email:', 'process to user');
		if($model->mass_to_users) {
			echo " process user <br />";
			$where='';
			foreach($model->mass_to_users as $usr) {
				$where .= "JSON_CONTAINS(privilege,'".$usr."') OR ";
			} $where = rtrim($where," OR ");
			$myUsers = User::find()->where("email<>'' AND ($where)")->all(); //->createCommand()->sql; //echo $myUsers->sql; // exit;
			foreach($myUsers as $usr){
				$TstEmail = new Badges();
				if($usr->badge_number>0) {$TstEmail->badge_number = $usr->badge_number;} else {$TstEmail->badge_number = 0;}
				$TstEmail -> email = trim($usr->email);
				$TstEmail->first_name=$usr->full_name;
				$TstEmail->last_name='';
				$TstEmail->status = "approved";
				$TstEmail->subcat=true;
				array_push($members,$TstEmail) ;
			}
			echo '<pre>'; var_dump($members); echo '</pre><hr />';
		}

		yii::$app->controller->createEmailLog(false, 'Mass-Email:', 'process to officers');
		if($model->mass_to_co) {
			echo " process club officers <br />";
			$where='';
			foreach($model->mass_to_co as $officer) {
				$where .= "role=".$officer." OR ";
			} $where = rtrim($where," OR ");
			$myOfficers = Officers::find()->where("email_vrfy=1 AND email<>'' AND $where")->all(); //->createCommand()->sql; echo $myOfficers->sql;  exit;
			foreach($myOfficers as $officer){
				$TstEmail = new Badges();
				if($officer->badge_number>0) {$TstEmail->badge_number = $officer->badge_number;} else {$TstEmail->badge_number = 0;}
				$TstEmail -> email = trim($officer->email);
				$TstEmail->first_name=$officer->full_name;
				$TstEmail->last_name='';
				$TstEmail->status = "approved";
				$TstEmail->subcat=true;
				array_push($members,$TstEmail) ;
			}
			echo '<pre>'; var_dump($members); echo '</pre><hr />';
		}

		if(strpos(" ".$model->mass_to,'@')) {
			if(strpos($model->mass_to,';')) {
				$emails=explode(';',$model->mass_to);
			} else { $emails = array($model->mass_to); }
			foreach($emails as $anEmail) {
				if (filter_var(trim($anEmail), FILTER_VALIDATE_EMAIL)) {
					$TstEmail = new Badges();
					$TstEmail -> badge_number = 0;
					$TstEmail->first_name='Member';
					$TstEmail->last_name='';
					$TstEmail->subcat=true;
					$TstEmail -> email = trim($anEmail);
					$TstEmail->status = "approved";
					array_push($members,$TstEmail) ;
					Yii::$app->response->data .= "to addr: ".$TstEmail -> email."<br />\n";
				} else {
					Yii::$app->response->data .= "Bad Email <br />\n";
				}
			}
		}

		if (!members) {
			Yii::$app->response->data .= "No Members Found <br />\n";
			$model->mass_running = false;
			$model->save(false);
			exit;
		}

		$model->mass_start =  date('Y-m-d H:i:s', strtotime($this->getNowTime()));
		$model->mass_running = true;
		$model->save(false);

		yii::$app->controller->createEmailLog(false, 'Mass-Email:', 'ready to start sending');
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
						$mail->addCustomHeader('List-Unsubscribe', '<'.yii::$app->params['wp_site'].'/comms.php?unsubscribe='.$value['email'].'>');

						if($model->mass_reply_to) {
								if($model->mass_reply_name) {$adminName =$model->mass_reply_name;} else {$adminName = yii::$app->params['adminName']; }
							$mail->AddReplyTo($model->mass_reply_to, $adminName);
						} else { $mail->AddReplyTo(yii::$app->params['adminEmail'], 'AGC President'); }
						$mail->setFrom(yii::$app->params['mail']['Username'], 'AGC Range');

						$mail->addAddress($value['email']);

						$mail->Subject = $model->mass_subject;

						$hi = "<p>Hello ".trim($value['first_name']." ".$value['last_name']).",</p>";
						if($value['subcat']==true) {$foot='';} else {
						$foot = '<br /><br />< <a href="'.yii::$app->params['wp_site'].'/comms.php?unsubscribe='.$value['email'].'"> UnSubscribe from the AGC mailer</a> >';}
						$mail->Body = $hi.$model->mass_body.$foot;

						sleep(3);  // Default 3,  Throttled by cPanel ~ 2000 Emails per Hour
	// testing					$mail->send();
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
		if (!$model) { return $this->redirect(['index']); }
		if($resend) {
			$model->mass_running=0;
			$model->mass_lastbadge=NULL;
			$model->mass_start = NULL;
			$model->mass_runtime = NULL;
			$model->mass_finished = NULL;
		} elseif ($model->mass_running == True) {
			Yii::$app->response->data .= "Emailer Running.";
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
				$model->mass_to =  rtrim($_REQUEST['MassEmail']['to_email'],";");
			}
			$model->mass_updated = date('Y-m-d H:i:s', strtotime($this->getNowTime()));
			$model->mass_updated_by = $_SESSION['badge_number'];
			$model->save(false);
		}

		$cmd = 'wget -O/dev/null -q --no-check-certificate '. yii::$app->params['rootUrl'].'/mass-email/process?id='.$id;
		shell_exec($cmd);
		yii::$app->controller->createEmailLog(true, 'Mass-Email:send', 'Send Start!');
		return json_encode(['status'=>'Success','msg'=>'Processing was started']);
	}

    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
			$model->mass_updated = date('Y-m-d H:i:s', strtotime($this->getNowTime()));
			$model->mass_updated_by =  $_SESSION['badge_number'];
			if($_REQUEST['MassEmail']['to_users']==0) { $model->mass_to_users = null; }
			if($_REQUEST['MassEmail']['to_club_o']==0) { $model->mass_to_co = null; }
			if(($_REQUEST['MassEmail']['to_active']) || ($_REQUEST['MassEmail']['to_expired'])) {
				$model->mass_to='';
				if ($_REQUEST['MassEmail']['to_active']) {
					$model->mass_to .= '*A'; }
				if ($_REQUEST['MassEmail']['to_expired']) {
					$model->mass_to .= '*E'; }
			} else {
				$model->mass_to =  rtrim($_REQUEST['MassEmail']['to_email'],";");
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
