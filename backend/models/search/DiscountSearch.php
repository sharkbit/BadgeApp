<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Discount;

/**
 * ParamsSearch represents the model behind the search form about `backend\models\Discount`.
 */
class DiscountSearch extends Discount {

	public function rules() {
		return [
			[['dis_active','dis_allowed','dis_name','dis_def'],'safe'],
		];
	}

	public function scenarios() {
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}

	public function search($params) {
		$query = Discount::find();

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if(!isset($params['sort'])) { $query->orderBy( ['dis_name' => SORT_ASC] ); }

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		// grid filtering conditions
		if(isset($this->dis_id)) { $query->andFilterWhere(['dis_id' => $this->role_id]); }
		if(isset($this->dis_name)) { $query->andFilterWhere(['like','dis_name', $this->dis_name]); }
		if(isset($this->dis_active)) { $query->andFilterWhere(['dis_active' => $this->dis_active]); }
		if(isset($this->dis_allowed)) { $query->andFilterWhere(['like','dis_allowed', $this->dis_allowed]); }
		if(isset($this->dis_def)) { $query->andFilterWhere(['like','dis_def', $this->dis_def]); }

		return $dataProvider;
	}
}
