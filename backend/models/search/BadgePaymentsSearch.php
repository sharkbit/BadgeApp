<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\BadgePayments;

/**
 * BadgePaymentsSearch represents the model behind the search form about `backend\models\BadgePayments`.
 */
class BadgePaymentsSearch extends BadgePayments {
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'badge_number'], 'integer'],
            [['badge_year', 'payment_type', 'status', 'created_at'], 'safe'],
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
        $query = BadgePayments::find();
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'badge_number' => $this->badge_number,
            'badge_year' => $this->badge_year,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'payment_type', $this->payment_type])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}