<?php

namespace backend\controllers;

use Yii;
use backend\models\MembershipType;
use backend\models\Params;
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
        $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
		$responce = [
            'request'=>$value,
			'responce' =>$formatter->formatCurrency($value, 'USD'),
        ];
        return json_encode($responce);
    }

    public function actionFeesByType($from,$id) {
        $feeArray =  MembershipType::find()->where(['id'=>$id])->one();
		
		if(isset($feeArray->sku_full)) {
			$Full_Price = $feeArray->fullprice->price;
			$confParams  = Params::findOne('1');
			$nowNumbers = strtotime(yii::$app->controller->getNowTime());
			if (isset($feeArray->sku_half) && (date('Y-m-d', $nowNumbers) >= date('Y-07-01', $nowNumbers)) && (date('Y-m-d', $nowNumbers) <= date('Y-'.$confParams->sell_date, $nowNumbers)) && ($from=='n') && ((int)$feeArray->fullprice->price < 301 )) {
				//discount
				$Full_Price = $feeArray->halfprice->price;
				$Half_Price = $feeArray->halfprice->price;
				$item_sku=$feeArray->halfprice->sku;
				$item_name=$feeArray->halfprice->item;
			} else {
				$Half_Price = $Full_Price;
				$item_sku=$feeArray->fullprice->sku;
				$item_name=$feeArray->fullprice->item;
			}
		} else {
			$Full_Price=0;
			$Half_Price = 0;
			$item_sku='Free';
			$item_name="Free $feeArray->type Badge";
		}
		
		$feeOffer = [
            'badgeFee'=>$Full_Price,
            'badgeSpecialFee' =>$Half_Price,
			'item_sku'=>$item_sku,
			'item_name'=>$item_name
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
