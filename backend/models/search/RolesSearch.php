<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Roles;

/**
 * ParamsSearch represents the model behind the search form about `backend\models\Roles`.
 */
class RolesSearch extends Roles {

    public function rules() {
        return [
			
        ];
    }

    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params) {
        $query = Roles::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

		if(!isset($params['sort'])) { $query->orderBy( ['role_id' => SORT_DESC] ); }
		
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'role_id' => $this->role_id,
        ]);

        return $dataProvider;
    }
}
