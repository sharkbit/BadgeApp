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
			//[['sponsor'], 'integer'],
            [['event_name','event_status_name','club_name','short_name'], 'safe']
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
        if(isset($this->event_name)) { $query->andFilterWhere(['like','event_name',$this->event_name]); }
	//	if(isset($this->e_poc)) {  $query->andWhere(" CONCAT(badges.first_name,' ',badges.last_name) like '%". $this->e_poc."%'"); }
		if(isset($this->event_status_name)) { $query->andFilterWhere(['event_status_name'=>$this->event_status_name]); }
		if(isset($this->club_name)) { $query->andFilterWhere(['club_name'=>$this->club_name]); }
		if(isset($this->short_name)) { $query->andFilterWhere(['short_name'=>$this->short_name]); }

//yii::$app->controller->createLog(true, 'trex-b-m-s-es', 'Raw Sql: '.var_export($query->createCommand()->getRawSql(),true));
        return $dataProvider;
    }
}
