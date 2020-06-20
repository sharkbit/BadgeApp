<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\PostPrintTransactions;

/**
 * BadgeSubscriptionsSearch represents the model behind the search form about `backend\models\BadgeSubscriptions`.
 */
class PostPrintTransactionsSearch extends PostPrintTransactions {
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'badge_number'], 'integer'],
            [['valid_from', 'valid_true','created_at'], 'safe'],
            [['fee', 'paid_amount', 'discount'], 'number'],
        ];
    }

    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params) {
        $query = PostPrintTransactions::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		
		$this->load($params);
		
		if(!isset($params['sort'])) { $query->orderBy( ['created_at' => SORT_ASC] ); }

		$two=false;
		if(isset($this->created_at) && $this->created_at!='') {
			$two=strpos($this->created_at,'-');
			if($two) {
				if(strtotime(substr($this->created_at,0,10)) == strtotime(substr($this->created_at,13,23))) {
					$query->andFilterWhere(['like','created_at',date('Y-m-d',strtotime(substr($this->created_at,0,10)))]);
				} else {
					$query->andFilterWhere(['>=','created_at',date('Y-m-d',strtotime(substr($this->created_at,0,10)))]);
					//  +1 Days is needed because of times not being checked +1 = 23:59:00
					$query->andFilterWhere(['<=','created_at',date('Y-m-d',strtotime(substr($this->created_at,13,23)." +1 days"))]);
				}
			} else {
				$query->andFilterWhere(['like','created_at',date('Y-m-d',strtotime($this->created_at))]);
			}
		} else {
			$query->andFilterWhere(['like','created_at',date('Y-m-d',strtotime(yii::$app->controller->getNowTime()))]);
		}

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        return $dataProvider;
    }
}
