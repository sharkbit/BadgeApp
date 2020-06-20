<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\RuleList;

/**
 * RuleListSearch represents the model behind the search form about `backend\models\RuleList`.
 */
class RuleListSearch extends RuleList {
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id','vi_type','is_active'], 'integer'],
            [['rule_abrev','rule_name'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {

       // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params) {
        $query = RuleList::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
		
		if(!isset($params['sort'])) { $query->orderBy( ['rule_abrev' => SORT_ASC] ); }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
		$query->andFilterWhere([
            'vi_type' => $this->vi_type
		]);
		
        $query->andFilterWhere(['like', 'rule_abrev', $this->rule_abrev])
            ->andFilterWhere(['like', 'rule_name', $this->rule_name]);
		if(isset($this->is_active) && ($this->is_active <>'')) {
			$query->andFilterWhere(['is_active' => $this->is_active]);}

        return $dataProvider;
    }
}
