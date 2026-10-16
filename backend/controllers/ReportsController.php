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

	public function actionIndex($year = null) {
		if ($year === null) {
			$year = date('Y');
		}

	/*	$dataProvider = new SqlDataProvider([
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
		$chartMeta  = (object)[
			'title' => 'Monthly Sales count by transaction type',
			'x_axis_field' => 'tx_month',
			'x_axis_title' => 'Year - Month',
			'y_axis_field' => 'tx_type',
			'y_axis_title' => 'Number of Sales',
			//'y2_axis' => 'Cumulative Totals',
		];*/



		$selectedYear = filter_var($year, FILTER_VALIDATE_INT);
		if ($selectedYear === false || $selectedYear < 2017 || $selectedYear > (int)date('Y')) {
			$selectedYear = null;
		}

		$sql = "SELECT
					DATE_FORMAT(created_at, '%Y-%m') AS tx_month,
					transaction_type,
					COUNT(*) AS item_count
				FROM badge_subscriptions
				WHERE created_at >= :startDate";
		$params = [':startDate' => '2016-11-01'];
		if ($selectedYear !== null) {
			$sql .= " AND created_at < :endDate";
			$params[':startDate'] = ($selectedYear - 1) . '-11-01';
			$params[':endDate'] = $selectedYear . '-11-01';
		}
		$sql .= " GROUP BY DATE_FORMAT(created_at, '%Y-%m'), transaction_type";

		$dataProvider = new SqlDataProvider([
			'sql' => $sql,
			'params' => $params,
			'sort' => [
				'attributes' => ['tx_month', 'transaction_type'],
				'defaultOrder' => [
					'tx_month' => SORT_ASC,
					'transaction_type' => SORT_ASC,
				],
			],
			'pagination' => false,
		]);
		$chartMeta  = (object)[
			'title' => 'Active Range Badges',
			'x_axis_field' => 'tx_month',
			'x_axis_title' => 'Year - Month',
			'y_axis_field' => 'transaction_type',
			'y_axis_title' => 'Number of Renewals',
			'y2_axis' => 'Cumulative Totals',
		];

		return $this->render('_chart', [
			'dataProvider' => $dataProvider,
			'chartMeta' => $chartMeta,
		]);
	}
}
