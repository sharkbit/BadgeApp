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
            [['club_id', 'dateEnd', 'dateStart', 'event_date', 'event_name', 'cal_start_time', 'cal_end_time', 'recurrent_start_date', 'recurrent_end_date', 'event_status_id', 'facility_id', 'key_words', 'range_status_id', 'recur_every', 'recur_week_days', 'SearchTime', 'showed_up', 'deleted', 'conflict'], 'safe']
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
			if($this->recur_every) {
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

		if (is_scalar($this->key_words) && trim((string)$this->key_words) !== '') {
			$keyword = trim((string)$this->key_words);
			$query->andWhere([
				'or',
				['like', 'cal_calendar.event_name', $keyword],
				['like', 'clubs.club_name', $keyword],
				['like', 'cal_calendar.key_words', $keyword],
			]);
		}

		if (is_scalar($this->club_id) && !empty($this->club_id)) {
			if (is_numeric($this->club_id)) {
				$query->andFilterWhere(['cal_calendar.club_id'=>$this->club_id]);
			} else {
				$query->andWhere([
					'or',
					['like', 'clubs.club_name', $this->club_id],
					['like', 'clubs.short_name', $this->club_id],
				]);
			}
		}

		if(!empty($this->facility_id))	 {
			$facilityIds = is_array($this->facility_id) ? $this->facility_id : [$this->facility_id];
			$facilityConditions = [];
			foreach ($facilityIds as $index => $facilityId) {
				if (!is_scalar($facilityId)) {
					continue;
				}
				$facilityId = filter_var($facilityId, FILTER_VALIDATE_INT);
				if ($facilityId !== false && $facilityId > 0) {
					$parameter = ':search_facility_' . $index;
					$facilityConditions[] = new \yii\db\Expression(
						"JSON_CONTAINS(cal_calendar.facility_id, {$parameter})",
						[$parameter => json_encode($facilityId)]
					);
				}
			}
			if (!empty($facilityConditions)) {
				$query->andWhere(array_merge(['or'], $facilityConditions));
			}
		}
//event_date
		if((int)$this->conflict === 1) {
			$query->andFilterWhere(['conflict'=>1]);
			$query->andFilterWhere(['>=','event_date' , date("Y-m-d 00:00",strtotime(yii::$app->controller->getNowTime())) ]);
			$this->deleted=0;
		} elseif($this->recur_every) {
				$query->andFilterWhere(['recur_every'=>true]);
				$query->andWhere('recurrent_calendar_id = calendar_id');
		} else {
			$query->andFilterWhere(['conflict'=>0]);
			if (is_scalar($this->event_date) && !empty($this->event_date)) {
				$this->SearchTime='';
				$query->andFilterWhere(['like', 'event_date', $this->event_date]);
			} else {
				if (!is_string($this->SearchTime) || trim($this->SearchTime) === '') {
					$SearchStart = date("Y-m-d 00:00",strtotime(yii::$app->controller->getNowTime()));
					$SearchEnd = date('Y-m-d',strtotime('+32 days',strtotime(yii::$app->controller->getNowTime())));
					$query->andFilterWhere(['>=','event_date' , $SearchStart ]);
					$query->andFilterWhere(['<','event_date', $SearchEnd ]);

					$this->SearchTime = date('Y-m-d', strtotime($SearchStart))." - ".date('Y-m-d', strtotime($SearchEnd.' -1 day'));
				} else {
					$dateRange = explode(' - ', $this->SearchTime, 2);
					if (count($dateRange) === 2) {
						$startDate = \DateTimeImmutable::createFromFormat('!Y-m-d', trim($dateRange[0]));
						$endDate = \DateTimeImmutable::createFromFormat('!Y-m-d', trim($dateRange[1]));
						if ($startDate && $endDate && $endDate >= $startDate) {
							$query->andWhere(['>=', 'event_date', $startDate->format('Y-m-d')]);
							$query->andWhere(['<', 'event_date', $endDate->modify('+1 day')->format('Y-m-d')]);
						}
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
		$query->andFilterWhere(['like', 'cal_calendar.cal_start_time', $this->cal_start_time])
			->andFilterWhere(['like', 'cal_calendar.cal_end_time', $this->cal_end_time])
			->andFilterWhere(['like', 'cal_calendar.recurrent_start_date', $this->recurrent_start_date])
			->andFilterWhere(['like', 'cal_calendar.recurrent_end_date', $this->recurrent_end_date]);
		$query->andWhere(['deleted' => (int)$this->deleted === 1 ? 1 : 0]);
		if(!empty($this->event_status_id)) { $query->andFilterWhere(['cal_event_status.event_status_id'=>$this->event_status_id]); }
		if(!empty($this->range_status_id)) { $query->andFilterWhere(['cal_range_status.range_status_id'=>$this->range_status_id]); }
		if ($this->showed_up !== null && $this->showed_up !== '') { $query->andFilterWhere(['showed_up'=>$this->showed_up]); }

	//yii::$app->controller->createLog(false, 'trex-B_M_S_AgcCAl Query OK: ', var_export($query->createCommand()->getRawSql(),true));
        return $dataProvider;
    }
}
