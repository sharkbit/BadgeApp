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
			[['closed','date_open','rso','shift'], 'safe'],
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
        if(isset($this->id)) { $query->andFilterWhere(['id' => $this->id,]); }

		if(isset($this->date_open) && ($this->date_open <>'')) {
			$query->andFilterWhere(['like', 'date_open', $this->date_open]);}

		if((isset($this->rso)) && ($this->rso!=''))	 {
			$query->andWhere("JSON_CONTAINS(rso,'".$this->rso."')");
		}

		if(isset($this->shift) && ($this->shift <>'')) {
			$query->andFilterWhere(['shift' => $this->shift,]);}

		if(isset($this->closed) && ($this->closed <>'')) {
			$query->andFilterWhere(['closed' => $this->closed,]);}
        
//yii::$app->controller->createLog(false, 'trex-B_M_S_RSO Rtp Query OK: ', var_export($query->createCommand()->getRawSql(),true));
        return $dataProvider;
    }
}
