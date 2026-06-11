<?php

namespace backend\controllers;

use Yii;
use backend\models\Discount;
use backend\models\MembershipStatus;
use backend\models\Params;
use backend\models\search\DiscountSearch;
use backend\models\search\MembershipStatusSearch;
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

    public function actionDiscount() {
		$searchModel = new DiscountSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		return $this->render('discount', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
    }

    public function actionDiscountupdate($id=1) {
        $model = Discount::findOne($id);
        if ($model->load(Yii::$app->request->post())) {
			if ($model->dis_def=1) {
				Discount::updateAll(['dis_def' => 0], []);
			}
			$model->save();
			Yii::$app->getSession()->setFlash('success', 'Discount has been updated.');
            return $this->redirect(['discountview', 'id'=>$model->dis_id]);
        } else {
            return $this->render('discountupdate', [
                'model' => $model,
            ]);
        }
    }

    public function actionDiscountview($id=1) {
        $model = Discount::findOne($id);

		return $this->render('discountview', [
			'model' => $model,
		]);
    }

    public function actionMembershipstatusview($id=1) {
        $model = MembershipStatus::findOne($id);
		return $this->render('membershipstatusview', [
			'model' => $model,
		]);
    }

	public function actionMembershipstatus(){
		$searchModel = new MembershipStatusSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		return $this->render('membershipstatus', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
    }

    public function actionMembershipstatusupdate($id=1) {
        $model = MembershipStatus::findOne($id);
        if ($model->load(Yii::$app->request->post())) {
			$model->save();
			Yii::$app->getSession()->setFlash('success', 'Membership Status has been updated.');
            return $this->redirect(['membershipstatusview', 'id'=>$model->act_id]);
        } else {
            return $this->render('membershipstatusupdate', [
                'model' => $model,
            ]);
        }
    }

    public function actionUpdate($id=1) {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
			$model->whitelist = json_encode($model->whitelist);
			$model->rso_email = json_encode($model->rso_email);
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
			$cmd = "htpasswd -bB $pwd_file $username '".$pwd."'";
			shell_exec($cmd);
			return;
		}
	}

    protected function findModel($id) {
        if (($model = Params::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
