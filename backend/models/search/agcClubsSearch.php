<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\agcClubs;

/**
 * ClubsSearch represents the model behind the search form about `backend\models\Clubs`.
 */

class agcClubsSearch extends agcClubs {
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name','active','nick_name'], 'safe'],
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
        $query = agcClubs::find();

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
        if(isset($this->active)) {$query->andFilterWhere(['active' => $this->active]); }
        if(isset($this->display_in_administration))	{ $query->andFilterWhere(['display_in_administration'  => $this->display_in_administration]); }
		if(isset($this->display_in_badges_administration)) {$query->andFilterWhere(['display_in_badges_administration' => $this->display_in_badges_administration]); }
        if(isset($this->is_cio)) {$query->andFilterWhere(['is_cio' => $this->is_cio]); }

        if(isset($this->name)) {$query->andFilterWhere(['like', 'name', $this->name]); }
        if(isset($this->nick_name)) {$query->andFilterWhere(['like', 'nick_name', $this->nick_name]); }

  /*      $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'short_name', $this->short_name])
            ->andFilterWhere(['like', 'poc_email', $this->poc_email])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'club_id', $this->club_id]);
*/
        return $dataProvider;
    }
}
