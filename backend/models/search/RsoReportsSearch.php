<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\RsoReports;

/**
 * ParamsSearch represents the model behind the search form about `backend\models\RsoReports`.
 */
class RsoReportsSearch extends RsoReports {

    public function rules() {
        return [
			//[['cash_bos','cash_eos','id','rso','par_50','par_100','par_200'], 'integer'],
			[['closing','date','notes','shift','shift_anom'], 'safe'],
        ];
    }

    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params) {
        $query = RsoReports::find();

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
