<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\MassEmail;

/**
 * ParamsSearch represents the model behind the search form about `backend\models\MassEmail`.
 */
class MassEmailSearch extends MassEmail {

    public function rules() {
        return [
			[['mass_to','mass_subject','mass_body'], 'safe'],
			[['id','mass_lastbadge','mass_created_by','mass_updated_by','mass_running'], 'number'],
			[['mass_start','mass_finished','mass_created','mass_updated','mass_reply_to','mass_reply_name'],'safe']
        ];
    }

    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params) {
        $query = MassEmail::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

		if(!isset($params['sort'])) { $query->orderBy( ['id' => SORT_DESC] ); }
		
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
