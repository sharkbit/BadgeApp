<?php

namespace backend\controllers;

use Yii;
use backend\models\BadgeToClubs;
use backend\models\User;
use backend\models\search\UserSearch;
use backend\models\ResetPasswordForm;
use backend\models\Params;
use backend\models\PasswordResetRequestForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\controllers\SiteController;
use frontend\models\SignupForm;

/**
 * AccountsController implements the CRUD actions for User model.
 */
class AccountsController extends SiteController {
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

    public function actionIndex() {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id) {
		$model = $this->findModel($id);
		if ((!in_array(1, json_decode(yii::$app->user->identity->privilege))) && (in_array(1,json_decode($model->privilege)))) {
			$this->redirect('index'); }
			
        return $this->render('view', [
            'model' => $model
        ]);
    }

    public function actionCreate() {
		$model = new SignupForm();
		
		if ($model->load(Yii::$app->request->post())) {
			$model->full_name= $model->f_name." ".$model->l_name;
			$model->privilege = str_replace('"',"", json_encode($model->privilege));
			 str_replace("\\","", str_replace('"',"", json_encode($model->clubs)));

			$co_name=$model->auth_key;

            if ($user=$model->signup()) {
				$user_fix = User::find()->where(['id'=>$user->id])->one();
				if($user_fix) {
					if(in_array(8,json_decode($model->privilege))) { $user_fix->company = trim($co_name); }
					$user_fix->badge_number = (int)$model->badge_number;
					$user_fix->save(false);
				}
                $this->createLog($this->getNowTime(), $this->getActiveUser()->username, "New Authorized User Created: $user->id: $user->username");
                Yii::$app->getSession()->setFlash('success', 'Authorized User has been added');
                return $this->redirect(['/accounts/view','id'=>$user->id]);
            } else {
				yii::$app->controller->createLog(false, 'trex C_AC-bn', 'error :67');
			}
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);
		if(isset($model->r_user)) { $old_r_user=$model->r_user;}
		$old_priv = json_decode($model->privilege);

		if ((!in_array(1, json_decode(yii::$app->user->identity->privilege))) && (array_intersect([1,2],json_decode($model->privilege)))) {
			$this->redirect('index'); }
        if ($model->load(Yii::$app->request->post()) ) {
	
			if ($model->privilege=='') {
				if(intval($model->badge_number)>0) {
					$this->RemoveClub($model->badge_number);
				}
				$this->createLog($this->getNowTime(), $this->getActiveUser()->username, "Authorized User Deleted: $model->id: $model->username");
				Yii::$app->getSession()->setFlash('success', $model->username.' has been deleted');
				User::deleteAll("id = ".$model->id);
				if(isset($old_r_user)) { $this->removeRemoteUser($old_r_user); }
				if(isset($model->r_user)) { $this->removeRemoteUser($model->r_user); }
                return $this->redirect(['/accounts/index']);
			} else {
				if ((!in_array(14,$model->privilege)) && (isset($old_r_user)) && (!is_null($old_r_user))){
					$this->removeRemoteUser($old_r_user);
					$model->r_user = null;
				}
				if (array_intersect([3,6],$old_priv)) {
					if(!array_intersect([3,6],$model->privilege)) {
						$this->RemoveClub($model->badge_number);
					}
				}
				if(array_intersect([3,6],$model->privilege)) {
					$BtC = (new BadgeToClubs)->find()->where(['badge_number'=>$model->badge_number,'club_id'=>33])->one();
					if(!$BtC) {
						$BtC = new BadgeToClubs;
						$BtC->badge_number=$model->badge_number;
						$BtC->club_id=33;  // Add AGC Staff
						$BtC->save();
					}
				}
				
				$model->clubs=json_encode($model->clubs);
				$model->privilege= str_replace('"',"", json_encode($model->privilege));
				$model->clubs = str_replace("\\","", str_replace('"',"", json_encode($model->clubs)));
				$model->updated_at = strtotime($this->getNowTime());
				$model->save(false);
				$this->createLog($this->getNowTime(), $this->getActiveUser()->username, "Authorized User Updated: $model->id: $model->username");
				return $this->redirect(['view', 'id' => $model->id]);
			}
        } else {
			$model->clubs=json_decode($model->clubs);
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

	private function RemoveClub($badge_number) {
		$BtC = (new BadgeToClubs)->find()->where(['badge_number'=>$badge_number,'club_id'=>33])->one();
		if($BtC) { $BtC->delete(); 
			$BtC = (new BadgeToClubs)->find()->where(['badge_number'=>$badge_number])->all();
			if(!$BtC) {
				$BtC = new BadgeToClubs;
				$BtC->badge_number=$badge_number;
				$BtC->club_id=35;  // Add Z Old Data
				$BtC->save();
			}
		}
	}
					
	private function removeRemoteUser($r_user){
		$param = Params::find()->one();
		$pwd_file = Yii::getAlias('@webroot').'/'.$param->remote_users;
		$newPasswd='';
		if (file_exists($param->remote_users)) { 
			$txt = explode(PHP_EOL,file_get_contents($param->remote_users));
			foreach($txt as $item) {
				if (strlen($item)<3) { continue 1; }
				$name=explode(":",$item);
				if($name[0]==$r_user) { continue 1; }
				$newPasswd .= implode(":",$name).PHP_EOL;
			}
			file_put_contents($param->remote_users, $newPasswd);
		}
	}

    public function actionRequestPasswordReset($id) {
        $model = new PasswordResetRequestForm();
        $resetToken = $model->tokenGenerate($id);
        if($resetToken!=null) {
            return $this->redirect(['/accounts/reset-password','token'=>$resetToken]);
        }
        else {
            Yii::$app->session->setFlash('error', 'Could not complete your request please try again later.!');
            return $this->redirect([Yii::$app->request->referrer ?: Yii::$app->homeUrl]);
        }
    }

    public function actionResetPassword($token) {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');
            return $this->redirect(['accounts/index']);
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id) {
		$model = $this->findModel($id);
		if(isset($model->r_user)) { $this->removeRemoteUser($model->r_user); }
        if($model->delete()) {
            $this->createLog($this->getNowTime(), $this->getActiveUser()->username, 'Authorized User Deleted: '.$id);
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id) {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
