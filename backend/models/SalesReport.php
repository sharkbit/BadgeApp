<?php

namespace backend\models;

use backend\models\clubs;
use Yii;

clASs SalesReport extends \yii\db\ActiveRecord {
	public $created_at;

	public function rules() {
		return [
			//[['barcode', 'barcode_c', 'barcode_t', 'barcode_b', 'barcode_pw', 'badge'], 'required'],
			[['created_at'], 'safe'],
		];
	}

	public function attributeLabels() {
		return [ ];
	}

	public function getSRdata($mydate,$sumary=true) {
		if ($sumary) {
			$query = $this->getWhere($mydate,'created_at');
			$sql="SELECT (SELECT count(*) FROM badge_subscriptions WHERE transaction_type='NEW' AND ".$query.") AS num_new, ".
						"(SELECT count(*) FROM badge_subscriptions WHERE transaction_type='RENEW' AND ".$query.") AS num_renew, ".
						"(SELECT count(*) FROM badge_certification WHERE ".$query.") AS num_cert";
			$mydata = Yii::$app->db->createCommand($sql)->queryall();

			$myNums = "";
			if(isset($mydata[0]['num_new']))  {  $myNums .= "[ New Badges: ".$mydata[0]['num_new']." ]"; }
			if(isset($mydata[0]['num_renew'])) { $myNums .= "[ Renewed Badges: ".$mydata[0]['num_renew']." ]"; }
			if(isset($mydata[0]['num_cert'])) {  $myNums .= "[ Certs: ".$mydata[0]['num_cert']." ]"; }
			return $myNums;

		} else {
			$query = $this->getWhere($mydate,'created_at');
			$guest_query = $this->getWhere($mydate,'time_in');
			$count=Yii::$app->db->createCommand('SELECT COUNT(*) FROM clubs c WHERE c.`status`=0')->queryScalar();
			$sql="SELECT c.club_name,c.short_name,c.club_id,c.is_club, ".
				"(SELECT count(*) FROM BadgeDB.badge_subscriptions WHERE transaction_type='NEW' AND ".$query."AND badge_number IN ".
					"(SELECT badge_number FROM badge_to_club btc WHERE btc.club_id=c.club_id)) AS `new`, ".
				"(SELECT count(*) FROM BadgeDB.badge_subscriptions WHERE transaction_type='RENEW' AND ".$query." AND badge_number IN ".
					"(SELECT badge_number FROM badge_to_club btc WHERE btc.club_id=c.club_id)) AS renew, ".
				"(SELECT count(*) FROM BadgeDB.badge_certification WHERE ".$query." AND badge_number IN ".
					"(SELECT badge_number FROM badge_to_club btc WHERE btc.club_id=c.club_id)) AS certs, ".
				"(SELECT count(*) FROM BadgeDB.guest where ".$guest_query." AND badge_number in ".
					"(select badge_number from badge_to_club btc where btc.club_id=c.club_id)) as guests ".
				"FROM clubs c WHERE c.`status`=0 ORDER BY c.is_club desc,c.club_name;";

			$dataProvider = new \yii\data\SqlDataProvider([
				'sql' => $sql,
				'totalCount' => $count,
			]);		
			return $dataProvider;
		}
	}
	
	private function getWhere($mydate,$field) {
		$two=false;
		$query='';
		if(isset($mydate) && $mydate!='') {
			$two=strpos($mydate,'-');
			if($two) {
				if(strtotime(substr($mydate,0,10)) == strtotime(substr($mydate,13,23))) {
					$query=" ".$field." like '".date('Y-m-d',strtotime(substr($mydate,0,10)))."%'";
				} else {
					$query=" ".$field." >= '".date('Y-m-d',strtotime(substr($mydate,0,10)))." 00:00' AND ".
					            "".$field." <= '".date('Y-m-d',strtotime(substr($mydate,13,23)))." 23:59'";
				}
			} else {
				$query=" ".$field." like '".date('Y-m-d',strtotime($mydate))."%'";
			}
		} else {
			$query=" ".$field." like '".date('Y-m-d',strtotime(yii::$app->controller->getNowTime()))."%'";
		}
		return $query;
	}
}