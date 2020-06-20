<?php

namespace backend\controllers;

use Yii;
use backend\controllers\AdminController;
use backend\models\Badges;
use kartik\mpdf\Pdf;

class BadgeController extends AdminController {

	public function actionBadgePrintView() {
	// get your HTML raw content without any layouts or scripts
		//return $this->render('badge-print-view');
		$content = $this->renderPartial('_badge-print-view');

		// setup kartik\mpdf\Pdf component
		$pdf = new Pdf([
			// set to use core fonts only
			'mode' => Pdf::MODE_BLANK,
			// A4 paper format
			'format' => Pdf::FORMAT_A4,
			// portrait orientation
			'orientation' => Pdf::ORIENT_PORTRAIT,
			// stream to browser inline
			'destination' => Pdf::DEST_BROWSER,
			// your html content input
			'content' => $content,
			'marginTop'=>0,
			'marginLeft'=>0,
			'marginRight'=>0,
			'marginBottom'=>0,
			// format content from your own css file if needed or use the
			// enhanced bootstrap css built by Krajee for mPDF formatting
			'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
			// any css to be embedded if required
			'cssInline' => '.kv-heading-1{font-size:18px}',
			 // set mPDF properties on the fly
			'options' => ['title' => 'AGC Range Badge'],
			 // call mPDF methods on the fly
			'methods' => [
				'SetHeader'=>['Powered By itekk.us'],
				//'SetFooter'=>['{PAGENO}'],
			]
		]);

		// return the pdf output as per the destination setting
		return $pdf->render();
	}

	public function actionCreate() {
		$model = new \backend\models\Badges();
		return $this->render('_create',[
			'model' => $model
		]);
	}

	public function actionIndex() {
		return $this->render('index');
	}

	public function actionUpdate() {
	   $model = new \backend\models\Badges();
		return $this->render('_update',[
			'model' => $model
		]);
	}

	public function actionAdminFunction() {

		return $this->render('adminMenu');
	}

	public function actionClubNameLookUp() {
		return $this->render('club-name-look-up');
	}

	public function actionClubNameCreate() {
		$model = new \backend\models\Badges();
		return $this->render('create-club-name',['model'=>$model]);
	}

	public function actionClubNameEdit() {
		$model = new \backend\models\Badges();
		return $this->render('edit-club-name',['model'=>$model]);
	}

	public function actionBrowsWorkCredits() {
		return $this->render('brows-credit');
	}

	public function actionWorkCreditMenu() {
		return $this->render('work-credit-menu');
	}

	public function actionWorkCreditTransfer() {

		 $model = new \backend\models\Badges();
		return $this->render('work-credit-transfer',[
			'model' => $model
		]);


	}

	public function actionWorkCreditEntry() {
		$model = new \backend\models\Badges();
		return $this->render('work-credit-entry',[
			'model' => $model
		]);

	}

	public function actionUsersIndex() {

		return $this->render('users-index');
	}

	public function actionCreateUser() {
		$model = new \backend\models\Badges();
		return $this->render('create-user',[
			'model' => $model
			]);
	}

	public function actionEditUser() {
		$model = new \backend\models\Badges();
		return $this->render('edit-user',[
			'model' => $model
			]);
	}

	public function actionViewUser() {
		$model = new \backend\models\Badges();
		return $this->render('view-user',[
			'model' => $model
			]);
	}

	public function actionLogError($PageLoc, $ErrorData) {
		 yii::$app->controller->createLog(false, 'trex', 'C-Badge-C Log Err');
		if(isset($_SESSION['user'])) {$usr = $_SESSION['user'];} else {$usr='unk';}
		yii::$app->controller->createJavaLog($PageLoc, var_export($ErrorData,true),$usr);
	}
}
