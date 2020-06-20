<?php

namespace backend\controllers;

use Yii;
use backend\models\FeesStructure;
use backend\models\search\FeesStructureSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\controllers\AdminController;

/**
 * FeeStructureController implements the CRUD actions for FeesStructure model.
 */
class FeeStructureController extends AdminController {
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

    /**
     * Lists all FeesStructure models.
     * @return mixed
     */
    public function actionAjaxmoneyConvert($value) {
        $responce = [
            'request'=>$value,
            'responce' => money_format('$%i',$value),
        ];
        return json_encode($responce);
    }

    public function actionFeesByType($id) {
        $feeArray =  FeesStructure::find()->where(['membership_id'=>$id])->one();
        $feeOffer = $this->getOfferFee($feeArray);
        $responce = json_encode($feeOffer,true);
        Yii::$app->response->data = $responce;
    }

    public function actionIndex() {
        $searchModel = new FeesStructureSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FeesStructure model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new FeesStructure model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new FeesStructure();

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

    /**
     * Updates an existing FeesStructure model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->createLog($this->getNowTime(), $this->getActiveUser()->username, 'Fee Shedule Updated : '.$model->id);
            Yii::$app->getSession()->setFlash('success', 'Fee Shedules has been updated');
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing FeesStructure model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        if($this->findModel($id)->delete()) {
            $this->createLog($this->getNowTime(), $this->getActiveUser()->username, 'Fee Shedule Deleted : '.$id);
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the FeesStructure model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FeesStructure the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = FeesStructure::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function getOfferFee($feeArray) {
    /*    $now = $this->getNowTime();
        $nowMonthOnly = date('m',strtotime($now));
        $staticLimit = date('Y-10-23',strtotime($this->getNowTime()));
        
        if($nowMonthOnly>=7 && strtotime($now) < strtotime($staticLimit)) {
            $persontage = yii::$app->params['conf']['offer'];
            $fee = ($feeArray->fee / 100) * $persontage;
               
        } else {  */
            $fee = $feeArray->fee;
    //    }

        $discount = $feeArray->fee - $fee;
        $responce = [
            'badgeFee'=>$feeArray->fee,
            'badgeSpecialFee' =>$fee,
            'discount'=>$discount,
        ];

        return $responce;
    }
}
