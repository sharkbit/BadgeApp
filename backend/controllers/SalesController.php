<?php

namespace backend\controllers;

use Yii;
use backend\controllers\AdminController;
use backend\controllers\BadgesController;
use backend\controllers\PaymentController;
use backend\models\Badges;
use backend\models\CardReceipt;
use backend\models\search\CardReceiptSearch;
use backend\models\Sales;
use backend\models\search\StoreItemsSearch;
use backend\models\StoreItems;
use backend\models\Params;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ParamsController implements the CRUD actions for StoreItems model.
 */
class SalesController extends AdminController {
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

    public function actionCreate() {
		$model = new StoreItems();
        if ($model->load(Yii::$app->request->post())){
			
			if ($model->save()) {
				$this->createLog($this->getNowTime(), $_SESSION['user'], 'New Store Item Created: '.$model->item);
				Yii::$app->getSession()->setFlash('success', $model->item.' has been created');
				return $this->redirect(['update', 'id' => $model->item_id]);
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

	public function actionDelete($id) {
		$del_item = StoreItems::find()->where(['item_id'=>$id])->one();
		$this->createLog($this->getNowTime(), $_SESSION['user'], "Store Item Deleted: ($id) ".$del_item->item);
		Yii::$app->getSession()->setFlash('success', "Store Item Deleted: ($id) ".$del_item->item);
		$del_item->delete();
		return $this->redirect('index');
	}

    public function actionIndex() {
		$model = new Sales;

		if ($model->load(Yii::$app->request->post())) {

			$badge = Badges::find()->where(['badge_number'=>$model->badge_number])->one();
			if($badge) {
				if($model->address<>'') { $badge->address = trim($model->address);}
				if($model->city <>'')   { $badge->city = $model->city;}
				if($model->state<>'')  { $badge->state = $model->state;}
				if($model->zip<>'')    { $badge->zip = $model->zip;}
				if($model->email<>'')  { $badge->email = trim($model->email);}

				$badge->remarks_temp='';
				$badge = BadgesController::cleanBadgeData($badge,true);
				if($badge->save(false)) {
					Yii::$app->response->data .= "Saved";
				} else { Yii::$app->response->data .= "no save"; }
			} else { Yii::$app->response->data .= "failed"; }

			if ($model->badge_number=='99999') {
				yii::$app->controller->createLog(false, 'trex_C_SC Guest Checkout', var_export($_REQUEST,true));
			}
			if($model->payment_method <> 'creditnow') {
				$savercpt = new CardReceipt();
				$model->cc_x_id = 'x'.rand(100000000,1000000000);
				$savercpt->id = $model->cc_x_id;
				$savercpt->badge_number = $model->badge_number;
				$savercpt->tx_date = $this->getNowTime();
				$savercpt->tx_type = $model->payment_method;
				$savercpt->amount = $model->total;
				$savercpt->name = $model->first_name.' '.$model->last_name;
				$savercpt->cart = $model->cart;
				$savercpt->cashier = $_SESSION['user'];
				if($savercpt->save()) {
					yii::$app->controller->createLog(true, $_SESSION['user'], "Saved Rcpt','".$model->badge_number);
					if($this->CheckGuest($model)) {return $this->redirect(['/guest']);}
					else {return $this->redirect(['purchases']);}
					exit;
				} else {
					yii::$app->controller->createLog(false, 'trex_C_SC savercpt', var_export($savercpt->errors,true));
				}
			} elseif($model->payment_method == 'creditnow') {
				if($this->CheckGuest($model)) {return $this->redirect(['/guest']);}
				else {return $this->redirect(['purchases']);}
			}
			Yii::$app->response->data .= "errerrrer!";
			yii::$app->controller->createLog(true, 'trex_C_SC', var_export($model,true));
			exit;

		} 
		else {
			return $this->render('index', [
                'model' => $model,
            ]);
		}
	}

	function CheckGuest($model){
		$confParams = Params::findOne('1');
		$tst = (string)$confParams->guest_sku;
		if (strpos($model->cart,  $tst)) {
			$cart = json_decode($model->cart);
			foreach($cart as $item){
				if($item->sku == $confParams->guest_sku) {
					$sql="UPDATE guest set g_paid=1 WHERE badge_number=".$model->badge_number." AND g_paid ='0' or g_paid='a' or g_paid ='h'; LIMIT ".$item->qty;
					Yii::$app->db->createCommand($sql)->execute();
					return true;
				}
			}
		}
		return false;
	}

	public function actionInventory () {
		$dataService = PaymentController::OAuth();
		$confParams = Params::findOne('1');
		
		return $this->render('inventory', [
			'dataService' => $dataService,
			'confParams' => $confParams
		]);
	}

	public function actionPrintRcpt ($x_id, $badge_number=null) {  //Reciept Email or Print
		$MyRcpt = CardReceipt::findOne($x_id,$badge_number);
		if($MyRcpt) {
			 return $this->render('print-rcpt',[ 'MyRcpt' => $MyRcpt ] ); 
		} else {
			Yii::$app->getSession()->setFlash('error', 'Reciept Not Found.');
			return $this->redirect($_SERVER['HTTP_REFERER']);
		}
	}

	public function actionPurchases() {
		$searchModel = new CardReceiptSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		//$dataProvider->query->andWhere("active=1");

		return $this->render('purchases', [
				'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]);
	}

    public function actionStock() {
		$searchModel = new StoreItemsSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		//$dataProvider->query->andWhere("active=1");

		return $this->render('stock', [
				'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]);
	}

	public function actionUpdate($id=1) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
        	if($model->save()) {
				Yii::$app->getSession()->setFlash('success', 'Item has been updated');
			} else { Yii::$app->getSession()->setFlash('success', 'Item update Failed'); }
				return $this->render('update',['model' => $model]);
		} else {
            return $this->render('update', [
                'model' => $model
            ]);
        }
    }

    protected function findModel($id) {
        if (($model = StoreItems::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
