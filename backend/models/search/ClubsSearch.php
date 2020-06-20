<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\clubs;

/**
 * ClubsSearch represents the model behind the search form about `backend\models\Clubs`.
 */

class ClubsSearch extends Clubs {
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['club_id'], 'integer'],
            [['club_name', 'short_name', 'poc_email', 'status', 'is_club'], 'safe'],
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
        $query = Clubs::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

		if(!isset($params['sort'])) { $query->orderBy( ['club_name' => SORT_ASC] ); }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
		if(isset($this->club_name)) { $query->andFilterWhere(['like','club_name',$this->club_name]); }
		if(isset($this->poc_email)) { $query->andFilterWhere(['like','poc_email',$this->poc_email]); }
		if(isset($this->short_name)) { $query->andFilterWhere(['like','short_name',$this->short_name]); }
		if(isset($this->club_id)) { $query->andFilterWhere(['club_id'=>$this->club_id]); }
		if(isset($this->is_club)) { $query->andFilterWhere(['is_club'=>$this->is_club]); }
		if(isset($this->status)) { $query->andFilterWhere(['status'=>$this->status]); }

        return $dataProvider;
    }
}
