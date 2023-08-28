<?php

namespace backend\controllers;

use Yii;
use backend\models\clubs;
use backend\models\Roles;
use backend\models\Badges;
use backend\models\search\ClubsSearch;
use backend\models\search\RolesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\controllers\AdminController;
use backend\models\search\BadgesRosterSearch;
use yii\helpers\ArrayHelper;
/**
 * ClubsController implements the CRUD actions for Clubs model.
 */
class ClubsController extends AdminController {
    /**
     * @inheritdoc
     */
    public $enableCsrfValidation = true;

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

    public function actionBadgeRosters() {
        ini_set('max_execution_time', 0);

        if((yii::$app->request->isAjax) || (isset($_GET['club'])))  {
			$club_id = null;
            $returnArray = array();

            if(isset($_GET['club_id'])) {
                $club_id =  $_GET['club_id'];
            }elseif(isset($_GET['club'])) {  //for troubleshooting  ./clubs/badge-rosters?club=2
				$club_id = $_GET['club'];
			} else {
                $club_id = null;
            }

			if (!@mkdir('files/rosters/', 0755)) {
				$error = error_get_last();
				if ($error['type']<>2) { yii::$app->controller->createLog(true, 'B_C_ClubCtrl Roster Dir Error:', var_export($error,true)); }
			}

			if($club_id != null) {
				$clubData = Clubs::find()->where(['club_id'=>$club_id])->one();
				$createdDate = date('M_d_Y',strtotime($this->getNowTime()));
				$fileName = $createdDate.'_'.$clubData->club_name.'_'.$clubData->club_id.'.csv';
				$fileName = strtolower($fileName);
				$fileName = $this->stringReplace($fileName,[' ',",",'/',';',':','%','__','-','--','&']);

				$rosterForSingle = Badges::find()->where("badge_number IN (SELECT badge_number FROM badge_to_club WHERE club_id=".$club_id.")")->all();

				$forGenerateCsv  = [];
				@unlink('files/rosters/'.$fileName);
				$fileCsv = fopen('files/rosters/'.$fileName, 'w');
				fputcsv($fileCsv, array('Badge Number','Club Name','First Name','Last Name','Email','Phone','Membership Type','Date Joined','Badge Year','status','Last Renewed'));
				foreach ($rosterForSingle as $key => $badgeData) {
						if(is_null($badgeData->badgeToYear)) {$BadgeYear='2016';} else {$BadgeYear=$badgeData->badgeToYear->badge_year;}
						$userDetails = [
						'0' => $badgeData->badge_number,
						'1' => $clubData->club_name,
						'2' => $badgeData->first_name.' '.$badgeData->suffix,
						'3' => $badgeData->last_name,
						'4' => $badgeData->email,
						'5' => substr($badgeData->phone,0,3).'-'.substr($badgeData->phone,3,3).'-'.substr($badgeData->phone,6,4),
						'6' => $badgeData->membershipType->type,
						'7' => date('M d, Y',strtotime($badgeData->incep)),
						'8' => $BadgeYear,
						'9' => $badgeData->status,
						'10' => date('M d, Y',strtotime($badgeData->updated_at)),

					];
					$forGenerateCsv[] = $userDetails;
				}

				foreach ($forGenerateCsv as $row) {
					fputcsv($fileCsv, $row);
				}
				fclose($fileCsv);

				if($_GET['email']) {
					if($clubData->poc_email != '') {
						$mail = yii::$app->controller->emailSetup();
						if ($mail) {
							$mail->IsHTML(false);
							$mail->setFrom(yii::$app->params['adminEmail'], 'AGC Range');
							$mail->addAddress($clubData->poc_email);
							$mail->addBCC(yii::$app->params['adminEmail']);
							$mail->Subject = $clubData->short_name."'s active members of AGC";
							$mail->Body = $clubData->short_name.",\n\nHere is your club roster as of today (".date('M d, Y').").\n\nAGC Range";
							$mail->addAttachment(Yii::getAlias('@webroot').'/files/rosters/'.$fileName);
							$mail->send();
							$returnArray['Emailed: '.$clubData->club_name] = $fileName;
							yii::$app->controller->createEmailLog(true, 'ClubRoster-Email', $clubData->short_name." sent to ".$clubData->poc_email);
						} else { $returnArray['Email system disabled'] = $fileName; }
					} else {
						$returnArray['No Email Found: '.$clubData->club_name] = $fileName;
					}
				} else {
					$returnArray[$clubData->club_name] = $fileName;
				}

				$returnArray = json_encode($returnArray);
				Yii::$app->response->data = $returnArray;
			} else {
				$clubListArray = Clubs::find()->where(['status'=>0, 'is_club'=>1])->orderBy(['club_name' => SORT_ASC ])->all();
				$createdDate = date('M_d_Y',strtotime($this->getNowTime()));

				$fileNameAll = $createdDate.'_Full_AGC_Roster.csv';
				@unlink('files/rosters/'.$fileNameAll);
				$fileAllCsv = fopen('files/rosters/'.$fileNameAll, 'a');
				fputcsv($fileAllCsv, array('Badge Number','Club Name','First Name','Last Name','Email','Phone','Membership Type','Date Joined','Expire Date','status','Last Renewed'));

				foreach ($clubListArray as $clubData) {
					$club_id = $clubData->club_id;
					$fileName = $createdDate.'_'.$clubData->club_name.'_'.$clubData->club_id.'.csv';
					$fileName = strtolower($fileName);
					$fileName = $this->stringReplace($fileName,[' ',",",'/',';',':','%','__','-','--','&']);

					$rosterForSingle = Badges::find()->where("badge_number IN (SELECT badge_number FROM badge_to_club WHERE club_id=".$club_id.")")->all();

					$forGenerateCsv  = [];
					@unlink('files/rosters/'.$fileName);
					$fileCsv = fopen('files/rosters/'.$fileName, 'w');
					fputcsv($fileCsv, array('Badge Number','Club Name','First Name','Last Name','Email','Phone','Membership Type','Date Joined','Badge Year','status','Last Renewed'));
					foreach ($rosterForSingle as $key => $badgeData) {
						if(is_null($badgeData->badgeToYear)) {$BadgeYear='2016';} else {$BadgeYear=$badgeData->badgeToYear->badge_year;}
						$userDetails = [
						'0' => $badgeData->badge_number,
						'1' => $clubData->club_name,
						'2' => $badgeData->first_name.' '.$badgeData->suffix,
						'3' => $badgeData->last_name,
						'4' => $badgeData->email,
						'5' => substr($badgeData->phone,0,3).'-'.substr($badgeData->phone,3,3).'-'.substr($badgeData->phone,6,4),
						'6' => $badgeData->membershipType->type,
						'7' => date('M d, Y',strtotime($badgeData->incep)),
						'8' => $BadgeYear,
						'9' => $badgeData->status,
						'10' => date('M d, Y',strtotime($badgeData->updated_at)),
						];
						$forGenerateCsv[] = $userDetails;
					}

					foreach ($forGenerateCsv as $row) {
						fputcsv($fileCsv, $row);
						fputcsv($fileAllCsv, $row);
					}
					fclose($fileCsv);

					if($_GET['email']) {
						if($clubData->poc_email != '') {
							$mail = yii::$app->controller->emailSetup();
							if ($mail) {
								$mail->IsHTML(false);
								$mail->setFrom(yii::$app->params['adminEmail'], 'AGC Range');
								$mail->addAddress($clubData->poc_email);
								$mail->addBCC(yii::$app->params['adminEmail']);
								$mail->Subject = $clubData->short_name."'s active members of AGC";
								$mail->Body = $clubData->short_name.",\n\nHere is your club roster as of today (".date('M d, Y').").\n\nAGC Range";
								$mail->addAttachment(Yii::getAlias('@webroot').'/files/rosters/'.$fileName);
								$mail->send();
								$returnArray['Emailed: '.$clubData->club_name] = $fileName;
								yii::$app->controller->createEmailLog(true, 'ClubRoster-Email', $clubData->short_name." sent to ".$clubData->poc_email);
							} else { $returnArray['Email system disabled'] = $fileName; }
						} else {
							$returnArray['No Email Found: '.$clubData->club_name] = $fileName;
							yii::$app->controller->createEmailLog(true, 'ClubRoster-Email', $clubData->short_name." No Email Found");
						}
					} else {
						$returnArray[$clubData->club_name] = $fileName;
					}
				}
				$returnArray['Full_AGC_Roster'] = $fileNameAll;
				array_multisort ($returnArray);
				$returnArray = json_encode($returnArray);
				fclose($fileAllCsv);
				Yii::$app->response->data = $returnArray;
			}
		}
		else { //not Ajax request
            $clubModel = new Clubs();
            $clubListArray = Clubs::find()->all();
            $clubList = ArrayHelper::map($clubListArray,'club_id','club_name');
            return $this->render('badge-rosters',[
                'clubList' => $clubList,
                'clubModel' => $clubModel,
            ]);
        }
    }

    public function actionIndex() {
        $searchModel = new ClubsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 100;

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

    public function actionCreate() {
		$model = new Clubs();
        if ($model->load(Yii::$app->request->post())){
			$sql='SELECT t.club_id + 1 AS FirstAvailableId FROM clubs t LEFT JOIN clubs t1 ON t1.club_id = t.club_id + 1 WHERE t1.club_id IS NULL ORDER BY t.club_id LIMIT 0, 1';
			$connection = Yii::$app->getDb();
			$command = $connection->createCommand($sql);
			$NewId = $command->queryAll();
			$model->club_id = $NewId[0]['FirstAvailableId'];
			$model->status = 0;

			if ($model->save()) {
				$this->createLog($this->getNowTime(), $_SESSION['user'], 'New Club Created : '.$model->club_id);
				Yii::$app->getSession()->setFlash('success', 'Club '.$model->short_name.' has been created');
				return $this->redirect(['view', 'id' => $model->club_id]);
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


 function actionCreaterole() {
		$model = new Roles();
        if ($model->load(Yii::$app->request->post())){
			$sql='SELECT t.club_id + 1 AS FirstAvailableId FROM clubs t LEFT JOIN clubs t1 ON t1.club_id = t.club_id + 1 WHERE t1.club_id IS NULL ORDER BY t.club_id LIMIT 0, 1';
			$connection = Yii::$app->getDb();
			$command = $connection->createCommand($sql);
			$NewId = $command->queryAll();
			$model->club_id = $NewId[0]['FirstAvailableId'];
			$model->status = 0;

			if ($model->save()) {
				$this->createLog($this->getNowTime(), $_SESSION['user'], 'New Club Created : '.$model->club_id);
				Yii::$app->getSession()->setFlash('success', 'Club '.$model->short_name.' has been created');
				return $this->redirect(['view', 'id' => $model->club_id]);
			} else {
				return $this->render('createrole', [
					'model' => $model,
				]);
			}
		} else {
			return $this->render('createrole', [
				'model' => $model,
			]);
		}
    }

    public function actionRoles() {
        $searchModel = new RolesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
       // $dataProvider->pagination->pageSize = 100;

        return $this->render('roles', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->createLog($this->getNowTime(), $_SESSION['user'], 'Club Updated : '.$model->club_id);
            Yii::$app->getSession()->setFlash('success', 'Club '.$model->short_name.' has been updated');
            return $this->redirect(['view', 'id' => $model->club_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionDelete($id) {
        $deleteModel = Clubs::findOne($id);
        $this->findModel($id)->delete();
        $this->createLog($this->getNowTime(), $this->getActiveUser()->username, 'Club Deleted : '.$deleteModel->club_id);
        Yii::$app->getSession()->setFlash('success', 'Club '.$deleteModel->short_name.' has been updated');

        return $this->redirect(['index']);
    }

    protected function findModel($id) {
        if (($model = Clubs::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
