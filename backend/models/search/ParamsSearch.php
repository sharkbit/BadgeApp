<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Params;

/**
 * ParamsSearch represents the model behind the search form about `backend\models\Params`.
 */
class ParamsSearch extends Params
{
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id'], 'integer'],
            [['sell_date'], 'safe'],
			[['qb_env','qb_oauth_cust_key','qb_oauth_cust_sec','qb_realmId','qb_token','qb_token_date'], 'safe'],
			[['qb_oa2_id','qb_oa2_sec','qb_oa2_realmId','qb_oa2_access_token','qb_oa2_access_date','qb_oa2_refresh_token','qb_oa2_refresh_date'], 'safe'],
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
        $query = Params::find();

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
        ]);

        return $dataProvider;
    }
}
