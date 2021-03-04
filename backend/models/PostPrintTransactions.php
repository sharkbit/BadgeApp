<?php

namespace backend\models;

use backend\models\clubs;
use Yii;

/**
 * This is the model class for table "post_print_transactions".
 *
 * @property integer $id
 * @property string $badge_number
 * @property string $transaction_type
 * @property integer $club_id
 * @property string $created_at
 * @property double $fee
 * @property double $discount
 * @property double $paid_amount
 */
class PostPrintTransactions extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'post_print_transactions';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['badge_number', 'transaction_type', 'created_at', 'fee', 'discount', 'paid_amount'], 'required'],
            [['transaction_type'], 'string'],
            [['badge_number'], 'integer'],
            [['created_at'], 'safe'],
            [['fee', 'discount', 'paid_amount'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'badge_number' => 'Badge Number',
            'transaction_type' => 'Transaction Type',
            'created_at' => 'Created At',
            'fee' => 'Fee',
            'discount' => 'Discount',
            'paid_amount' => 'Paid Amount',
        ];
    }

    public function getClubDetails() {
        return $this->hasOne(Clubs::className(),['club_id'=>'club_id']);
    }

	public function getPPTSum($mydate) {
		$two=false;
		$query='';
		if(isset($mydate) && $mydate!='') {
			$two=strpos($mydate,'-');
			if($two) {
				if(strtotime(substr($mydate,0,10)) == strtotime(substr($mydate,13,23))) {
					$query=" AND created_at like '".date('Y-m-d',strtotime(substr($mydate,0,10)))."%'";
				} else {
					$query=" AND created_at >= '".date('Y-m-d',strtotime(substr($mydate,0,10)))." 00:00' AND ".
					            "created_at <= '".date('Y-m-d',strtotime(substr($mydate,13,23)))." 23:59'";
				}
			} else {
				$query=" AND created_at like '".date('Y-m-d',strtotime($mydate))."%'";
			}
		} else {
			$query=" AND created_at like '".date('Y-m-d',strtotime(yii::$app->controller->getNowTime()))."%'";
		}

		$sql="SELECT (SELECT count(*) FROM post_print_transactions WHERE transaction_type='NEW'".$query.") as num_new, ".
					"(SELECT count(*) FROM post_print_transactions WHERE transaction_type='RENEW'".$query.") as num_renew, ".
					"(SELECT count(*) FROM post_print_transactions WHERE transaction_type='CERT'".$query.") as num_cert";
		$command = Yii::$app->db->createCommand($sql);
		$mydata = $command->queryAll();

		$myNums = "";
		if(isset($mydata[0]['num_new']))  {  $myNums .= "[ New Badges: ".$mydata[0]['num_new']." ]"; }
		if(isset($mydata[0]['num_renew'])) { $myNums .= "[ Renewed Badges: ".$mydata[0]['num_renew']." ]"; }
		if(isset($mydata[0]['num_cert'])) {  $myNums .= "[ Certs: ".$mydata[0]['num_cert']." ]"; }
		return $myNums;
	}
}
