<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Legalgroups;

/**
 * ClubsSearch represents the model behind the search form about `backend\models\Legalgroups`.
 */

class LegalGroupsSearch extends Legalgroups {
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
			[['name'], 'safe'],
			[['is_active'], 'number'],
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
        $query = Legalgroups::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
		
		if(!isset($params['sort'])) { $query->orderBy( ['name' => SORT_ASC] ); }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions

		if(isset($this->name)) { $query->andFilterWhere(['like','name',$this->name]); }
		if(isset($this->is_active)) { $query->andFilterWhere(['is_active'=>$this->is_active]); }

        return $dataProvider;
    }
}
