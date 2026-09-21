<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\AgcCal;

/**
 * ParamsSearch represents the model behind the search form about `backend\models\AgcCal`.
 */
class AgcCalSearch extends AgcCal {

	public $dateStart;
	public $dateEnd;
	public $pagesize;
	public $SearchTime;

    public function rules() {
        return [
            [['active','approved','club_id','dateEnd','dateStart','event_date','event_name','event_date','event_status_id','facility_id','key_words','range_status_id','recur_every','recur_week_days','SearchTime','showed_up'], 'safe']
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
		//if (!empty($this->pagesize)) {$dataProvider->pagination->pageSize = $this->pagesize;}

        $this->load($params);

		if(!isset($params['sort'])) {
			if(isset($this->recur_every)) {
				$query->orderBy('month(event_date),day(event_date),time(cal_start_time)');
			} else {
				$query->orderBy('event_date,hour(cal_start_time)');
			}
		}

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
			yii::$app->controller->createLog(true, 'trex-B_M_S_AgcCAl Query Error: ', var_export($query->createCommand()->getRawSql(),true));

            return $dataProvider;
        }

    // grid filtering conditions

		if((!yii::$app->controller->hasPermission('calendar/all')) && (isset(Yii::$app->user->identity->clubs))) {
			$query->andFilterWhere(['in','cal_calendar.club_id',json_decode(Yii::$app->user->identity->clubs)]);
		}

		if(!empty($_REQUEST['AgcCalSearch']['key_words'])) {
			$this->key_words = $_REQUEST['AgcCalSearch']['key_words'];
			$query->andWhere("REGEXP_LIKE(event_name,'".$this->key_words."','i')")
					->orWhere("REGEXP_LIKE(club_name,'".$this->key_words."','i')")
					->orWhere("REGEXP_LIKE(key_words,'".$this->key_words."','i')");
		}

		if(!empty($this->club_id)) {
			if (is_numeric($this->club_id)) {
				$query->andFilterWhere(['cal_calendar.club_id'=>$this->club_id]);
			} else {
				$query->andFilterWhere(['like','clubs.club_name',$this->club_id])->orFilterWhere(['like','clubs.short_name',$this->club_id]);
			}
		}

		if(!empty($this->facility_id))	 {
			if (is_array($this->facility_id)) {
				$sql = "(";
				foreach ($this->facility_id as $facilid){
					$sql .= "JSON_CONTAINS(cal_calendar.facility_id,'".$facilid."') OR ";
				}
				$query->andWhere(substr($sql, 0, -4).")");
			} else {
				$query->andWhere("JSON_CONTAINS(cal_calendar.facility_id,'".$this->facility_id."')");
			}
		}
//event_date
		if(isset($this->conflict) && $this->conflict==1) {
			$query->andFilterWhere(['conflict'=>1]);
			$query->andFilterWhere(['>=','event_date' , date("Y-m-d 00:00",strtotime(yii::$app->controller->getNowTime())) ]);
			$this->deleted=0;
		} elseif(isset($this->recur_every)) {
				$query->andFilterWhere(['recur_every'=>true]);
				$query->andWhere('recurrent_calendar_id = calendar_id');
		} else {
			$query->andFilterWhere(['conflict'=>0]);
			if((!empty($this->event_date)) || (!empty($_REQUEST['AgcCalSearch']['event_date']))) {
				if(empty($this->event_date)) {$this->event_date=$_REQUEST['AgcCalSearch']['event_date']; }
				yii::$app->controller->createLog(true, 'trex-B_M_S_AgcCAl eventdate: ', 'yuuppp');
				$this->SearchTime='';
				$query->andFilterWhere(['LIKE','event_date', date("Y-m-d",strtotime($this->event_date))]);
			} else {
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
		}
		if(isset($this->recur_week_days)) {
			switch ($this->recur_week_days) {
				case 'd':$likeThis='daily'; break;
				case 'w':$likeThis='weekly'; break;
				case 'm':$likeThis='monthly'; break;
				case 'y':$likeThis='yearly'; break;
			}
			if(isset($likeThis)) { $query->andFilterWhere(['like','recur_week_days',$likeThis]); }
		}

		if(!empty($this->event_name)) { $query->andFilterWhere(['like','event_name',$this->event_name]); }
		if(!empty($this->active)) { $query->andFilterWhere(['cal_calendar.active'=>$this->active]); }
		if(!empty($this->approved)) { $query->andFilterWhere(['approved'=>$this->approved]); }
		if(!empty($this->deleted) && $this->deleted==1) { $query->andFilterWhere(['deleted'=>1]); } else	{$query->andFilterWhere(['deleted'=>0]);}
		if(!empty($this->event_status_id)) { $query->andFilterWhere(['cal_event_status.event_status_id'=>$this->event_status_id]); }
		if(!empty($this->range_status_id)) { $query->andFilterWhere(['cal_range_status.range_status_id'=>$this->range_status_id]); }
		if(!empty($this->showed_up)) { $query->andFilterWhere(['showed_up'=>$this->showed_up]); }

	//yii::$app->controller->createLog(false, 'trex-B_M_S_AgcCAl Query OK: ', var_export($query->createCommand()->getRawSql(),true));
        return $dataProvider;
    }
}
