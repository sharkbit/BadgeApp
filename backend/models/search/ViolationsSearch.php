<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Violations;

/**
 * ViolationsSearch represents the model behind the search form about `backend\models\Violations`.
 */
class ViolationsSearch extends Violations {
    /**
     * @inheritdoc
     */
	public $atRange_condition;

    public function rules() {
        return [
			[['badge_involved', 'badge_witness','vi_sum','vi_loc','vi_rules','vi_report','vi_action','vi_date','badge_reporter','vi_type','club_id'], 'safe'],
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
        $query = Violations::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
		if(!isset($params['sort'])) { $query->orderBy( ['vi_date' => SORT_DESC] ); }

		if(!yii::$app->controller->hasPermission('violations/all')) {
			$query->andFilterWhere(['like','badge_involved',$_SESSION["badge_number"]]);
		}

		$two=false;
		if(isset($this->vi_date) && $this->vi_date!='') {
			$two=strpos($this->vi_date,'-');
			if($two) {
				if(strtotime(substr($this->vi_date,0,10)) == strtotime(substr($this->vi_date,13,23))) {
					$query->andFilterWhere(['like','vi_date',date('Y-m-d',strtotime(substr($this->vi_date,0,10)))]);
				} else {
					$query->andFilterWhere(['>=','vi_date',date('Y-m-d 00:01',strtotime(substr($this->vi_date,0,10)))]);
					//  +1 Days is needed because of times not being checked +1 = 23:59:00
					$query->andFilterWhere(['<=','vi_date',date('Y-m-d',strtotime(substr($this->vi_date,13,23)." +1 days"))]);
				}
			} else {
				$query->andFilterWhere(['like','vi_date',date('Y-m-d',strtotime($this->vi_date))]);
			}
		} else {
			if(strpos($_SERVER['REDIRECT_URL'],'report')) {
				$query->andFilterWhere(['like','vi_date',date('Y-m-d',strtotime(yii::$app->controller->getNowTime()))]);
			}
		} 
		
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            //  $query->where('0=1');
			yii::$app->controller->createLog(false, 'trex_M_S_VS', var_export($this->errors,true));
            return $dataProvider;
        }

        // grid filtering conditions
		
        $query->andFilterWhere(['vi_type' => $this->vi_type,]);
		
		if(isset($this->vi_loc) && ($this->vi_loc <>'')) {
			$query->andFilterWhere(['vi_loc' => $this->vi_loc,]);}

		if(isset($this->club_id) && ($this->club_id <>'')) {
			$query->andWhere("badge_involved IN (SELECT badge_number FROM badge_to_club WHERE club_id=".$this->club_id.")"); }
		if(isset($this->badge_involved) && ($this->badge_involved <>'')) {
			$query->andFilterWhere(['like', 'badge_involved', $this->badge_involved]);}
		if(isset($this->badge_witness) && ($this->badge_witness <>'')) {
			$query->andFilterWhere(['like', 'badge_witness', $this->badge_witness]);}
		if(isset($this->badge_reporter) && ($this->badge_reporter <>'')) {
			$query->andFilterWhere(['like', 'badge_reporter', $this->badge_reporter]);}
		if(isset($this->vi_sum) && ($this->vi_sum <>'')) {
			$query->andFilterWhere(['like', 'vi_sum', $this->vi_sum]);}
		if(isset($this->vi_rules) && ($this->vi_rules <>'')) {
			$query->andFilterWhere(['like', 'vi_rules', (string)$this->vi_rules]);}

        return $dataProvider;
    }
}
