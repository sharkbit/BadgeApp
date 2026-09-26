<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Events;
use backend\models\Event_Att;

/**
 * ParamsSearch represents the model behind the search form about `backend\models\Events`.
 */
class EventsSearch extends Events {

    public function rules() {
        return [
			[['event_date', 'event_name',], 'safe'],
            [['club_id','event_status_id'], 'integer'],
        ];
    }

    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params) {
        $query = Events::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

		if(!isset($params['sort'])) { $query->orderBy( ['event_date' => SORT_DESC] ); }
		
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
		
        if(!empty($this->event_date)) {			$query->andFilterWhere(['like', 'event_date', $this->event_date]); }
		if(!empty($this->club_id)) {  			$query->andFilterWhere(['club_id' => $this->club_id]); }
		if(!empty($this->event_status_id)) {	$query->andFilterWhere(['event_status_id' => $this->event_status_id]); }
		if(!empty($this->event_name)) {			$query->andFilterWhere(['like', 'event_name', $this->event_name]); }
		
//yii::$app->controller->createLog(true, 'trex-b-m-s-es', 'Raw Sql: '.var_export($query->createCommand()->getRawSql(),true));
        return $dataProvider;
    }
}
