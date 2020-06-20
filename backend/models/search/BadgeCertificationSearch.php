<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\BadgeCertification;

/**
 * BadgeCertificationSearch represents the model behind the search form about `backend\models\BadgeCertification`.
 */
class BadgeCertificationSearch extends BadgeCertification
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'badge_number', 'certification_type'], 'integer'],
            [['created_at', 'updated_at', 'sticker', 'status'], 'safe'],
            [['fee', 'discount', 'amount_due'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
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
    public function search($params)
    {
        $query = BadgeCertification::find();

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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'certification_type' => $this->certification_type,
            'fee' => $this->fee,
            'discount' => $this->discount,
            'amount_due' => $this->amount_due,
        ]);

        $query->andFilterWhere(['like', 'sticker', $this->sticker])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
