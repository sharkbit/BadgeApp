<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Officers;

/**
 * ParamsSearch represents the model behind the search form about `backend\models\Officers`.
 */
class OfficerSearch extends Officers {

	public function rules() {
		return [ 
			[['badge_number','club','role'], 'integer'],
			[['club_name','role_name','full_name'], 'safe'],
		];
	}

	public function scenarios() {
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}

	public function search($params) {
		$query = Officers::find();

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if(!isset($params['sort'])) { $query->orderBy( ['club_name' => SORT_DESC] ); }
		
		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

        // grid filtering conditions
		if(isset($this->badge_number)) { $query->andFilterWhere(['badge_number' => $this->badge_number]); }
		if(isset($this->full_name)) { $query->andFilterWhere(['like', 'full_name', $this->full_name]); }
		if(isset($this->club)) { $query->andFilterWhere(['club' => $this->club]); }
		if(isset($this->role)) { $query->andFilterWhere(['role' => $this->role]); }

		return $dataProvider;
	}
}
