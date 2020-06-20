<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\BadgeSubscriptions;

/**
 * BadgeSubscriptionsSearch represents the model behind the search form about `backend\models\BadgeSubscriptions`.
 */
class BadgeSubscriptionsSearch extends BadgeSubscriptions {
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'badge_number'], 'integer'],
            [['valid_from', 'valid_true', 'payment_type', 'status', 'created_at'], 'safe'],
            [['badge_fee', 'paid_amount', 'discount'], 'number'],
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
        $query = BadgeSubscriptions::find();

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
            'valid_from' => $this->valid_from,
            'valid_true' => $this->valid_true,
            'created_at' => $this->created_at,
            'badge_fee' => $this->badge_fee,
            'paid_amount' => $this->paid_amount,
            'discount' => $this->discount,
        ]);

        $query->andFilterWhere(['like', 'payment_type', $this->payment_type])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
