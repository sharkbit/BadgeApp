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
	public $sort;
	public $groupby;

    public function rules() {
        return [
			[['tx_type','citem'], 'safe'],
			[['csku'],'integer'],
		];
    }

	 public function scenarios() {

       // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

	public function search($params) {
        $query = CartSummary::find();
		if($this->groupby) {
			$query->select(['sum(qty) as sqty','sum(cprice) as sprice','Cart_Summary.*']);
			$query->groupBy('cat,tx_type,csku');
		} else {
			$query->select(['qty as sqty','cprice as sprice','Cart_Summary.*']);
		}

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		$this->load($params);

		if(!isset($this->sort)) { $query->orderBy('cat,csku'); }
		if(isset($this->citem)) { $query->andFilterWhere(['like', 'citem', $this->citem]); }
		if(isset($this->tx_type)) { $this->tx_type=$this->tx_type; }

		if(isset($this->date_start)) {
			$this->date_start=$this->date_start;
		} else {
			$this->date_start = date('Y-m-d H:i', strtotime("-1 months",strtotime(yii::$app->controller->getNowTime())));
		}
		$query->andFilterWhere(['>=','tx_date',$this->date_start]);

		if (isset($this->date_stop)) {
			$date_stop = $this->date_stop;
			if($date_stop != '' ) {
				$this->date_stop = $date_stop;
				$query->andFilterWhere(['<=','tx_date',$date_stop]);
			}
		}

        // grid filtering conditions

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
		   return $dataProvider;
        }

		if($this->tx_type != null) { $query->andFilterWhere([ 'in','tx_type', $this->tx_type ]); }
		if($this->csku >0) { $query->andFilterWhere([ 'csku'=>$this->csku ]); }

		//yii::$app->controller->createLog(true, 'trex-b-m-s-bs', 'Raw Sql: '.var_export($query->createCommand()->getRawSql(),true));
        return $dataProvider;
    }
}
