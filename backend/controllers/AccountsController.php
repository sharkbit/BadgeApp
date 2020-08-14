<?php

namespace backend\controllers;

use Yii;
use backend\models\User;
use backend\models\search\UserSearch;
use backend\models\ResetPasswordForm;
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
        return $this->render('view', [
            'model' => $this->findModel($id),
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
                $this->createLog($this->getNowTime(), $this->getActiveUser()->username, 'New Authorized User Created : '.$user->id);
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

        if ($model->load(Yii::$app->request->post()) ) {
			yii::$app->controller->createLog(false, 'trex-reeeqq', var_export($_REQUEST,true));
			$model->clubs=json_encode($model->clubs);
			$model->privilege= str_replace('"',"", json_encode($model->privilege));
			$model->clubs = str_replace("\\","", str_replace('"',"", json_encode($model->clubs)));
            $model->updated_at = strtotime($this->getNowTime());
            $model->save(false);
            $this->createLog($this->getNowTime(), $this->getActiveUser()->username, 'Authorized User Updated: '.$model->id);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
			$model->clubs=json_decode($model->clubs);
            return $this->render('update', [
                'model' => $model,
            ]);
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
        if($this->findModel($id)->delete()) {
            $this->createLog($this->getNowTime(), $this->getActiveUser()->username, 'Authorized User Deleted : '.$id);
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
