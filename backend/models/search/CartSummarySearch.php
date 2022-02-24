<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\CartSummary;

/**
 * RuleListSearch represents the model behind the search form about `backend\models\CartSummary`.
 */
class CartSummarySearch extends CartSummary {
    /**
     * @inheritdoc
     */

    public function rules() {
        return [
		//	[['tx_type'], 'string'],
		];
    }

	 public function scenarios() {

       // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

	public function search($params) {
        $query = CartSummary::find();
		$query->select(['sum(qty) as sqty','sum(cprice) as sprice','Cart_Summary.*']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params,false);
		if(isset($params['CartSummarySearch']['date_start'])) {
			$this->date_start=$params[ 'CartSummarySearch']['date_start'];
		} else {
			$this->date_start = date('Y-m-d H:i', strtotime("-1 months",strtotime(yii::$app->controller->getNowTime())));
		}
		$query->andFilterWhere(['>','tx_date',$this->date_start]);

		
		if (isset($params['CartSummarySearch']['date_stop'])) {
			$date_stop = $params['CartSummarySearch']['date_stop'];
			if($date_stop != '' ) {
				$this->date_stop = $date_stop;
				$query->andFilterWhere(['<','tx_date',$date_stop]);
			}
		}

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

		$query->groupBy('cat,tx_type,csku');
		$query->orderBy('cat,csku');
        // grid filtering conditions

		//yii::$app->controller->createLog(true, 'trex-b-m-s-bs', 'Raw Sql: '.var_export($query->createCommand()->getRawSql(),true));
        return $dataProvider;
    }
}
