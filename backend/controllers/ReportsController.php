<?php

namespace backend\controllers;

use yii\data\SqlDataProvider;
use yii\filters\VerbFilter;

/**
 * Displays report data from the reporting database view.
 */
class ReportsController extends AdminController {
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					//'delete' => ['POST'],
				],
			],
		];
	}

	public function actionIndex() {
		$dataProvider = new SqlDataProvider([
			'sql' => 'SELECT tx_month, tx_type, item_count FROM view_rpt_1',
			'pagination' => false,
			'sort' => [
				'attributes' => [
					'tx_month',
					'tx_type',
					'item_count',
				],
				'defaultOrder' => [
					'tx_month' => SORT_ASC,
					'tx_type' => SORT_ASC,
				],
			],
		]);

		return $this->render('index', [
			'dataProvider' => $dataProvider,
		]);
	}
}
