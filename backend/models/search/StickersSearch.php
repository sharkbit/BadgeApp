<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Stickers;

/**
 * ParamsSearch represents the model behind the search form about `backend\models\Stickers`.
 */
class StickersSearch extends Stickers {

    public function rules() {
        return [
			[['sticker','updated','status'], 'safe'],
			[['holder'], 'number'],
        ];
    }

    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params) {
        $query = Stickers::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

		if(!isset($params['sort'])) { $query->orderBy( ['updated' => SORT_DESC] ); }
		
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions

		if(isset($this->s_id)) 		{ $query->andFilterWhere(['s_id' => $this->s_id]); }
		if(isset($this->sticker)) { $query->andFilterWhere(['like', 'sticker', $this->sticker]); }
		if(isset($this->status)) 	{ $query->andFilterWhere(['status' => $this->status]); }
		if(isset($this->holder)) 	{ $query->andFilterWhere(['holder' => $this->holder]); }
		if(isset($this->updated)) { $query->andFilterWhere(['like', 'updated', $this->updated]); }

		return $dataProvider;
    }
}
