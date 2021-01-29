<?php

namespace backend\controllers;

use Yii;
use backend\models\Params;
use backend\models\search\ParamsSearch;
use yii\base\Model;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\controllers\AdminController;

class Passwd extends Model {
	public $pwd;
	public $pwd2;

	public function rules() {
		return [
			['pwd','in','range'=>['"',"'"],'not'=>true,'message' => "Do not use Quotes."],
			[['pwd'],'string', 'min' => 12],
			[['pwd','pwd2'],'safe'],
			['pwd2', 'compare', 'compareAttribute' => 'pwd', 'operator' => '==', 'message' => "Passwords don't match."],
		];
	}
	  public function attributeLabels() {
        return [
            'pwd' => 'Password',
			'pwd2' => 'Verify',
		];
	  }
}

/**
 * ParamsController implements the CRUD actions for Params model.
 */
class ParamsController extends AdminController {
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

	public function actionPassword() {
		$model = New Passwd;
		
		if ($model->load(Yii::$app->request->post())) {
			$status = " Updated password ";
			$status = $this->RemoteUser('update',$_SESSION['r_user'],$model->pwd);
		} else {
			$status = $this->RemoteUser('check',$_SESSION['r_user']);
			$status ="Exists?";
			
		}
		return $this->render('passwd',[
			'model' => $model,
			'status'=> $status,
		]);
	}

    public function actionUpdate($id=1) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
			$model->whitelist = json_encode($model->whitelist);
        	$model->save();
            return $this->redirect(['update']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

	public function RemoteUser($action, $username, $pwd=null) {
		$param = Params::find()->one();
		$pwd_file = Yii::getAlias('@webroot').'/'.$param->remote_users;
		if ($action=='check'){
		//	$txt = file_get_contents($param->remote_users);
	// yii::$app->controller->createLog(false, 'trex-r_usr', var_export($txt,true));		
	//		return $txt;
		} else {
			$cmd = "htpasswd -b5 $pwd_file $username '".$pwd."'";
			//$cmd = "htpasswd -bB $pwd_file $username '".$pwd."'";
			shell_exec($cmd); 
			return;
		}
		
		
		//'{SHA}' . base64_encode(sha1($password, TRUE))
		
		
		//file_put_contents($param->remote_users, $txt);
	
	}

    /**
     * Finds the Params model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Params the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Params::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
