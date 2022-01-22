<?php

namespace backend\controllers;

use Yii;
use backend\controllers\AdminController;
use backend\controllers\PaymentController;
use backend\models\Badges;
use backend\models\CardReceipt;
use backend\models\search\CardReceiptSearch;
use backend\models\Sales;
use backend\models\SalesReport;
use backend\models\search\StoreItemsSearch;
use backend\models\StoreItems;
use backend\models\Params;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

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
			if ($model->type=='Category') {
				$model->paren=NULL;
				$model->sku=NULL;
			}

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
		return $this->redirect('stock');
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
				$badge = Badges::cleanBadgeData($badge,true);
				if($badge->save(false)) {
					Yii::$app->response->data .= "Saved";
				} else { Yii::$app->response->data .= "no save"; }
			} else { Yii::$app->response->data .= "failed"; }

			if ($model->badge_number=='99999') {
				yii::$app->controller->createLog(false, 'trex_C_SC Guest Checkout', var_export($_REQUEST,true));
			}
			$this->processCart($model->cart);
			if($model->payment_method <> 'creditnow') {
				$savercpt = new CardReceipt();
				$model->cc_x_id = 'x'.rand(100000000,1000000000);
				$savercpt->id = $model->cc_x_id;
				$savercpt->badge_number = $model->badge_number;
				$savercpt->tx_date = $this->getNowTime();
				$savercpt->tx_type = $model->payment_method;
				$savercpt->tax = $_REQUEST['Sales']['tax'];
				$savercpt->amount = $model->total;
				$savercpt->name = $model->first_name.' '.$model->last_name;
				$savercpt->cart = $model->cart;
				$savercpt->cashier = $_SESSION['user'];
				if(is_null($_SESSION['badge_number'])) {$savercpt->cashier_badge = 0;} else {$savercpt->cashier_badge = $_SESSION['badge_number'];}
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
		if(isset($_REQUEST['reset'])) { UNSET($_REQUEST); }
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('purchases', [
				'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]);
	}

	public function actionReport() {
		$SalesReport = new SalesReport;

		if(isset($_REQUEST['SalesReport']['created_at'])) {
			$SalesReport->created_at = $_REQUEST['SalesReport']['created_at'];
		} else {
			$SalesReport->created_at = date('m/01/Y',strtotime(yii::$app->controller->getNowTime())).' - '.date('m/t/Y',strtotime(yii::$app->controller->getNowTime()));
		}
		return $this->render('report',[
			'SalesReport' => $SalesReport,
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
			if ($model->type=='Category') {
				$model->paren=NULL;
				$model->sku=NULL;
			}
			$model->kit_items = str_replace('"','', json_encode($model->kit_items));

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

	public function processCart($cart) {
		if(is_string($cart)) { $cart=json_decode($cart); }
		foreach($cart as $item) {
			$model = (New StoreItems)->find()->where(['sku'=>$item->sku])->one();
			if($model->type=='Inventory') {
				$model->stock = $model->stock - $item->qty;
				$model->save();
			} elseif($model->type=='Kits') {
				if($model->kit_items) {
					foreach(json_decode($model->kit_items) as $kititem) {
						$KitModel = (New StoreItems)->find()->where(['sku'=>$kititem])->one();
						$KitModel->stock = $KitModel->stock - $item->qty;
						$KitModel->save();
					}
				} else {
					Yii::$app->getSession()->setFlash('error', 'Kit has no Parts!');
				}
			}
		}
	}
}
