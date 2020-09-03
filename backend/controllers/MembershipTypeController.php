<?php

namespace backend\controllers;

use Yii;
use backend\models\MembershipType;
use backend\models\search\MembershipTypeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\controllers\AdminController;

/**
 * FeeStructureController implements the CRUD actions for MembershipType model.
 */
class MembershipTypeController extends AdminController {

    public function actionAjaxmoneyConvert($value) {
        $responce = [
            'request'=>$value,
            'responce' => money_format('$%i',$value),
        ];
        return json_encode($responce);
    }

    public function actionFeesByType($type,$id) {
        $feeArray =  MembershipType::find()->where(['id'=>$id])->one();
		$Full_Price = $feeArray->fullprice->price;
		
		if ((date('Y-m-d', strtotime(yii::$app->controller->getNowTime())) >= date('Y-07-01', strtotime(yii::$app->controller->getNowTime()))) && ($type=='n')) {
			//discount
			$Half_Price = $feeArray->halfprice->price;
			$discount = $feeArray->fullprice->price - $feeArray->halfprice->price;
		} else {
			$Half_Price = $Full_Price;
			$discount = 0;
		}
		
		$feeOffer = [
            'badgeFee'=>$Full_Price,
            'badgeSpecialFee' =>$Half_Price,
            'discount'=>$discount,
        ];
		
        $responce = json_encode($feeOffer,true);
        Yii::$app->response->data = $responce;
    }

    public function actionIndex() {
        $searchModel = new MembershipTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MembershipType model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new MembershipType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new MembershipType();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->createLog($this->getNowTime(), $this->getActiveUser()->username, 'Fee Shedule Created : '.$model->id);
            Yii::$app->getSession()->setFlash('success', 'Fee Shedules has been created');
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->createLog($this->getNowTime(), $this->getActiveUser()->username, 'Membership Type Updated : '.$model->id);
            Yii::$app->getSession()->setFlash('success', 'Membership Type has been updated');
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionDelete($id) {
        if($this->findModel($id)->delete()) {
            $this->createLog($this->getNowTime(), $this->getActiveUser()->username, 'Fee Shedule Deleted : '.$id);
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id) {
        if (($model = MembershipType::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
