<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\MembershipStatus;

/**
 * ParamsSearch represents the model behind the search form about `backend\models\MembershipStatus`.
 */
class MembershipStatusSearch extends MembershipStatus {

	public function rules() {
		return [
			[['act_active','act_login','act_name','act_prefill','act_renew','act_short','act_signup'],'safe'],
		];
	}

	public function scenarios() {
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}

	public function search($params) {
		$query = MembershipStatus::find();

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if(!isset($params['sort'])) { $query->orderBy( ['act_order' => SORT_ASC] ); }

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		// grid filtering conditions
		if(isset($this->act_id)) { $query->andFilterWhere(['act_id' => $this->act_id]); }
		if(isset($this->act_active)) { $query->andFilterWhere(['act_active'=> $this->act_active]); }
		if(isset($this->act_name)) { $query->andFilterWhere(['like','act_name', $this->act_name]); }
		if(isset($this->act_short)) { $query->andFilterWhere(['like','act_short', $this->act_short]); }
		if(isset($this->act_login)) { $query->andFilterWhere(['act_login' => $this->act_login]); }
		if(isset($this->act_order)) { $query->andFilterWhere(['act_order' => $this->act_order]); }
		if(isset($this->act_renew)) { $query->andFilterWhere(['act_renew' => $this->act_renew]); }
		if(isset($this->act_prefill)) { $query->andFilterWhere(['act_prefill' => $this->act_prefill]); }
		if(isset($this->act_signup)) { $query->andFilterWhere(['act_signup' => $this->act_signup]); }

		return $dataProvider;
	}
}
