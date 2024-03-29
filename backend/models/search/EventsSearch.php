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
			[['sponsor'], 'integer'],
            [['e_name','e_inst','e_poc','e_status','e_type'], 'safe']
        ];
    }

    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params) {
        $query = Events::find()
		->joinWith(['clubs'])
		->joinWith(['badges']);
		//->joinWith(['event_Att']);
		//->rightJoin('event_attendee',"`events`.e_id=event_attendee.ea_event_id");

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

		if(!isset($params['sort'])) { $query->orderBy( ['e_date' => SORT_DESC] ); }
		
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        if(isset($this->e_name)) { $query->andFilterWhere(['like','e_name',$this->e_name]); }
		if(isset($this->e_poc)) {  $query->andWhere(" CONCAT(badges.first_name,' ',badges.last_name) like '%". $this->e_poc."%'"); }
		if(isset($this->e_status)) { $query->andFilterWhere(['e_status'=>$this->e_status]); }
		if(isset($this->e_type)) { $query->andFilterWhere(['e_type'=>$this->e_type]); }
		if(isset($this->sponsor)) { $query->andFilterWhere(['sponsor'=>$this->sponsor]); }

//yii::$app->controller->createLog(true, 'trex-b-m-s-es', 'Raw Sql: '.var_export($query->createCommand()->getRawSql(),true));
        return $dataProvider;
    }
}
