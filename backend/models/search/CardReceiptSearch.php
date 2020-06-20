<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\CardReceipt;

/**
 * CardReceiptSearch represents the model behind the search form about `backend\models\Clubs`.
 */

class CardReceiptSearch extends CardReceipt {
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
           [['authCode','cardNum','cardType','cart','status','tx_type','tx_date','id','name'], 'string'],
		   [['badge_number','expYear','expMonth'], 'integer'],
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
        $query = CardReceipt::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
		
		if(!isset($params['sort'])) { $query->orderBy( ['tx_date' => SORT_DESC] ); }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'badge_number', $this->badge_number])
            ->andFilterWhere(['like', 'tx_date', $this->tx_date])
			->andFilterWhere(['like', 'cart', $this->cart])
			->andFilterWhere(['like', 'name', $this->name])
			->andFilterWhere(['like', 'amount', $this->amount])
			->andFilterWhere(['like', 'tx_type', $this->tx_type]);

        return $dataProvider;
    }
}