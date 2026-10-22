<?php

namespace backend\controllers;

use backend\models\Params;
use yii;
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
		return $this->render('index');
	}

	public function actionActiveBadges($year = null) {
		if ($year === null) {
			$year = date('Y');
		}

		$selectedYear = filter_var($year, FILTER_VALIDATE_INT);
		if ($selectedYear === false || $selectedYear > (int)date('Y')+1) {
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
			$sql .= " AND badge_year = :endDate";
			$params[':endDate'] = $selectedYear;
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

		if ($dataProvider->getTotalCount() === 0) {
			return $this->returnEmpty('active-badges', $year);
		}

		$firstYear = Yii::$app->db->createCommand(
			'SELECT MIN(badge_year) FROM badge_subscriptions'
		)->queryScalar();

		$chartMeta  = (object)[
			'firstYear'=>(int)$firstYear,
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

	public function actionCioReport($year = null, $student = false) {
		if ($year === null) {
			$year = date('Y');
		}

		$selectedYear = filter_var($year, FILTER_VALIDATE_INT);
		if ($selectedYear === false || $selectedYear > (int)date('Y')+1) {
			$selectedYear = null;
		}

		$sql = "SELECT ".
				"	DATE_FORMAT(event_date, '%Y-%m') AS tx_month, ".
				"	club_name, ".
				"	COUNT(*) AS item_count ".
				"FROM view_events ".
				"WHERE event_status_id = 4 ";
		if(!empty($student)) {
			$sql .= " AND (attended_badges > 0 or attended_guests > 0 )";
		}
		$params = [];
		if ($selectedYear !== null) {
			$sql .= " AND event_date >= :yearStart AND event_date < :yearEnd";
			$params[':yearStart'] = $selectedYear . '-01-01';
			$params[':yearEnd'] = ($selectedYear + 1) . '-01-01';
		}
		$sql .= " GROUP BY DATE_FORMAT(event_date, '%Y-%m'), club_name";

		$dataProvider = new SqlDataProvider([
			'sql' => $sql,
			'params' => $params,
			'sort' => [
				'attributes' => ['tx_month', 'club_name'],
				'defaultOrder' => [
					'tx_month' => SORT_ASC,
					'club_name' => SORT_ASC,
				],
			],
			'pagination' => false,
		]);

		if ($dataProvider->getTotalCount() === 0) {
			return $this->returnEmpty('events-report', $year);
		}

		$firstYear = Yii::$app->db->createCommand(
			'SELECT MIN(badge_year) FROM badge_subscriptions'
		)->queryScalar();

		$chartMeta  = (object)[
			'firstYear'=>(int)$firstYear,
			'title' => 'CIO Report',
			'x_axis_field' => 'tx_month',
			'x_axis_title' => 'Year - Month',
			'y_axis_field' => 'club_name',
			'y_axis_title' => 'Number of Events',
			'y2_axis' => 'Cumulative Totals',
			'student' => $student,
		];
		return $this->render('_chart', [
			'dataProvider' => $dataProvider,
			'chartMeta' => $chartMeta,
		]);
	}

	public function actionEventsReport($year = null) {
		if ($year === null) {
			$year = date('Y');
		}

		$selectedYear = filter_var($year, FILTER_VALIDATE_INT);
		if ($selectedYear === false || $selectedYear > (int)date('Y')+1) {
			$selectedYear = null;
		}

		$sql = "SELECT ".
				"	DATE_FORMAT(event_date, '%Y-%m') AS tx_month, ".
				"	event_status_name, ".
				"	COUNT(*) AS item_count ".
				"FROM view_events";
		$params = [];
		if ($selectedYear !== null) {
			$sql .= " WHERE event_date >= :yearStart AND event_date < :yearEnd";
			$params[':yearStart'] = $selectedYear . '-01-01';
			$params[':yearEnd'] = ($selectedYear + 1) . '-01-01';
		}
		$sql .= " GROUP BY DATE_FORMAT(event_date, '%Y-%m'), event_status_name";
		
yii::$app->controller->createLog(true, 'trex_sql_sql', var_export($sql,true));
		$dataProvider = new SqlDataProvider([
			'sql' => $sql,
			'params' => $params,
			'sort' => [
				'attributes' => ['tx_month', 'event_status_name'],
				'defaultOrder' => [
					'tx_month' => SORT_ASC,
					'event_status_name' => SORT_ASC,
				],
			],
			'pagination' => false,
		]);

		if ($dataProvider->getTotalCount() === 0) {
			return $this->returnEmpty('events-report', $year);
		}

		$firstYear = Yii::$app->db->createCommand(
			'SELECT MIN(badge_year) FROM badge_subscriptions'
		)->queryScalar();

		$chartMeta  = (object)[
			'firstYear'=>(int)$firstYear,
			'title' => 'Events Report',
			'x_axis_field' => 'tx_month',
			'x_axis_title' => 'Year - Month',
			'y_axis_field' => 'event_status_name',
			'y_axis_title' => 'Number of Events',
			'y2_axis' => 'Cumulative Totals',
		];
		return $this->render('_chart', [
			'dataProvider' => $dataProvider,
			'chartMeta' => $chartMeta,
		]);
	}

	public function actionGuestReport($year = null) {
		if ($year === null) {
			$year = date('Y');
		}

		$selectedYear = filter_var($year, FILTER_VALIDATE_INT);
		if ($selectedYear === false || $selectedYear > (int)date('Y')+1) {
			$selectedYear = null;
		}

		$sql = "SELECT
					DATE_FORMAT(time_in, '%Y-%m') AS tx_month,
					gt_name,
					COUNT(*) AS item_count
				FROM guest
				LEFT JOIN guest_type on (gt_code = g_paid)
				WHERE time_in >= :startDate";
		$params = [':startDate' => '2016-11-01'];
		if ($selectedYear !== null) {
			$sql .= " AND time_in >= :yearStart AND time_in < :yearEnd";
			$params[':yearStart'] = $selectedYear . '-01-01';
			$params[':yearEnd'] = ($selectedYear + 1) . '-01-01';
		}
		$sql .= " GROUP BY DATE_FORMAT(time_in, '%Y-%m'), gt_name";

		$dataProvider = new SqlDataProvider([
			'sql' => $sql,
			'params' => $params,
			'sort' => [
				'attributes' => ['tx_month', 'gt_name'],
				'defaultOrder' => [
					'tx_month' => SORT_ASC,
					'gt_name' => SORT_ASC,
				],
			],
			'pagination' => false,
		]);

		if ($dataProvider->getTotalCount() === 0) {
			return $this->returnEmpty('guest-report', $year);
		}

		$firstYear = Yii::$app->db->createCommand(
			'SELECT time_in FROM BadgeDB.guest order by time_in asc'
		)->queryScalar();

		$chartMeta  = (object)[
			'firstYear'=>(int)date('Y', strtotime($firstYear)),
			'title' => 'Guest Report',
			'x_axis_field' => 'tx_month',
			'x_axis_title' => 'Year - Month',
			'y_axis_field' => 'gt_name',
			'y_axis_title' => 'Guest Type',
			'y2_axis' => 'Cumulative Totals',
		];

		return $this->render('_chart', [
			'dataProvider' => $dataProvider,
			'chartMeta' => $chartMeta,
		]);
	}

	private function returnEmpty($from,$year){
		Yii::$app->getSession()->setFlash('error', 'No Data Found!');
		if ($year == date('Y')) {
			return $this->redirect('index');
		} else  {
			return $this->redirect($from.'?year='.date('Y'));
		}
	}

}
