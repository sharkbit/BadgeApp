<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\AgcCal;
use backend\models\clubs;

/**
 * ParamsSearch represents the model behind the search form about `backend\models\AgcCal`.
 */
class AgcCalSearch extends AgcCal {

	public $SearchTime;

    public function rules() {
        return [
            [['event_name','club_id','active','approved','recur_every','facility_id','event_status_id','range_status_id'], 'safe']
        ];
    }

    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params) {
        $query = AgcCal::find()
		->joinWith(['clubs'])
		->joinWith(['agcEventStatus'])
		->joinWith(['agcRangeStatus']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

		if(!isset($params['sort'])) {
			if(isset($this->recur_every)) {
				$query->orderBy('month(event_date),day(event_date),time(start_time)');
			} else {
				$query->orderBy('event_date,hour(start_time)');
			}
		}

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
			yii::$app->controller->createLog(true, 'trex-B_M_S_AgcCAl Query Error: ', var_export($query->createCommand()->getRawSql(),true));
        
            return $dataProvider;
        }

    // grid filtering conditions
		if(isset($this->club_id)) {	$query->andFilterWhere(['like','clubs.club_name',$this->club_id])->orFilterWhere(['like','clubs.short_name',$this->club_id]); }
		if((isset($this->facility_id)) && ($this->facility_id!=''))	 { $query->andWhere("JSON_CONTAINS(agc_calendar.facility_id,'".$this->facility_id."')"); }
		if(!yii::$app->controller->hasPermission('calendar/all')) {
			$query->andFilterWhere(['in','agc_calendar.club_id',json_decode(Yii::$app->user->identity->clubs)]);
		}

		if(isset($this->conflict) && $this->conflict==1) {
			$query->andFilterWhere(['conflict'=>1]);
			$query->andFilterWhere(['>=','event_date' , date("Y-m-d 00:00",strtotime(yii::$app->controller->getNowTime())) ]);
			$this->deleted=0; 
		} elseif(isset($this->recur_every)) {
				$query->andFilterWhere(['recur_every'=>true]);
				$query->andWhere('recurrent_calendar_id = calendar_id');
		} else {
			$query->andFilterWhere(['conflict'=>0]);
			if(!isset($this->SearchTime)){// || ($this->SearchTime='')) {
				$SearchStart = date("Y-m-d 00:00",strtotime(yii::$app->controller->getNowTime()));
				$SearchEnd = date('Y-m-d 23:59',strtotime('+31 days',strtotime(yii::$app->controller->getNowTime())));
				$query->andFilterWhere(['>=','event_date' , $SearchStart ]);
				$query->andFilterWhere(['<','event_date', $SearchEnd ]);

				$this->SearchTime = $SearchStart." - ".$SearchEnd;
			} else {
				if(strpos($this->SearchTime,' - ')) {
				list($SearchStart,$SearchEnd) = explode(' - ',$this->SearchTime);
				if(strlen($SearchStart)<14) {$SearchStart.=' 00:00';}
				if(strlen($SearchEnd)<14) {$SearchEnd.=' 23:59';}
				$query->andFilterWhere(['>=','event_date' , $SearchStart ]);
				$query->andFilterWhere(['<','event_date', $SearchEnd ]);
				} 
			}
		}
		
		if(isset($this->event_name)) { $query->andFilterWhere(['like','event_name',$this->event_name]); }
		if(isset($this->active)) { $query->andFilterWhere(['agc_calendar.active'=>$this->active]); }
		if(isset($this->approved)) { $query->andFilterWhere(['approved'=>$this->approved]); }
		if(isset($this->deleted) && $this->deleted==1) { $query->andFilterWhere(['deleted'=>1]); } else	{$query->andFilterWhere(['deleted'=>0]);}
		if(isset($this->event_status_id)) { $query->andFilterWhere(['event_status.event_status_id'=>$this->event_status_id]); }
		if(isset($this->range_status_id)) { $query->andFilterWhere(['range_status.range_status_id'=>$this->range_status_id]); }
		if(isset($this->showed_up)) { $query->andFilterWhere(['showed_up'=>$this->showed_up]); }

	//yii::$app->controller->createLog(false, 'trex-B_M_S_AgcCAl Query OK: ', var_export($query->createCommand()->getRawSql(),true));
        return $dataProvider;
    }
}
