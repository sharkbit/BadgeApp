<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\agcRangeStatus;

/**
 * ClubsSearch represents the model behind the search form about `backend\models\Clubs`.
 */

class agcRangeStatusSearch extends agcRangeStatus {
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name','active','restricted'], 'safe'],
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
		$query = agcRangeStatus::find();

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if(!isset($params['sort'])) { $query->orderBy( ['display_order' => SORT_ASC] ); }

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		// grid filtering conditions
		if(isset($this->active)) {$query->andFilterWhere(['active' => $this->active,]); }
		if(isset($this->restricted)) {$query->andFilterWhere(['restricted' => $this->restricted,]); }
		if(isset($this->name)) { $query->andFilterWhere(['like', 'name', $this->name]); }

		return $dataProvider;
    }
}
