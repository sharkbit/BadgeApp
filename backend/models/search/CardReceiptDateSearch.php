<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\CardReceiptDate;

/**
 * CardReceiptSearch represents the model behind the search form about `backend\models\Clubs`.
 */

class CardReceiptDateSearch extends CardReceiptDate {
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
           [['cart','tx_type','tx_date','id','name','cashier_badge'], 'safe'],
		   [['badge_number'], 'integer'],
           [['amount'], 'number'],
       ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {

       // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = CardReceiptDate::find()
		->select(['badge_subscriptions_date.created_at','badge_subscriptions_date.transaction_type','badge_subscriptions_date.badge_number','cc_receipts_date.*'])
		->from('cc_receipts_date')
		->joinWith('badges', true, 'LEFT JOIN')
		->joinWith('badge_subscriptions_date', true, 'LEFT JOIN')
		;

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

		if(!isset($params['sort'])) { $query->orderBy( ['tx_date' => SORT_DESC] ); }

		if(!yii::$app->controller->hasPermission('sales/all')) {
			$query->andFilterWhere(['cc_receipts_date.badge_number'=>$_SESSION["badge_number"]]);
		}

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        if((isset($this->tx_date)) && (strpos(trim($this->tx_date)," ")>0)) {
			$txDate = explode(" ", trim($this->tx_date));
			$query->andFilterWhere(['>=','tx_date' , $txDate[0].' 00:00:00'	]);
			$query->andFilterWhere(['<=','tx_date', $txDate[2].' 23:59:59' ]);
		} else {
			$this->tx_date = date('Y-m-d', strtotime('-30 days'));
			$query->andFilterWhere(['>=','tx_date' , date('Y-m-d ', strtotime('-30 days')).' 00:00:00'	]);
		}

		//if(isset($this->id)) { $query->andFilterWhere(['id' => $this->id ]); }
		if(isset($this->badge_number)) { $query->andFilterWhere(['like', 'cc_receipts.badge_number', $this->badge_number]); }
        if(isset($this->cart)) { $query->andFilterWhere(['like', 'cart', $this->cart]); }
		if(isset($this->name)) { $query->andFilterWhere(['like', 'name', $this->name]); }
		if(isset($this->amount)) { $query->andFilterWhere(['like', 'amount', $this->amount]); }
		if(isset($this->tx_type)) { $query->andFilterWhere(['like', 'tx_type', $this->tx_type]); }
		if((isset($this->cashier_badge)) && ($this->cashier_badge != '')) {
			if(strpos(trim($this->cashier_badge),",")>0) {
				$cashier_badge = explode(",", trim($this->cashier_badge));
				$sqlWhere='';
				foreach($cashier_badge as $them) {
					$sqlWhere .= " CONCAT(badges.first_name,' ',badges.last_name) like '%". $them."%' or ";
				}
				$query->andWhere("(".rtrim($sqlWhere," or ").")");
			} else { $query->andWhere(" CONCAT(badges.first_name,' ',badges.last_name) like '%". $this->cashier_badge."%'"); }
		}

//yii::$app->controller->createLog(true, 'trex-b-m-s-crs', 'Raw Sql: '.var_export($query->createCommand()->getRawSql(),true));
        return $dataProvider;
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
