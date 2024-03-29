<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Params;
use backend\models\WorkCredits;

/**
 * WorkCreditsSearch represents the model behind the search form about `backend\models\WorkCredits`.
 */
class WorkCreditsSearch extends WorkCredits {
    /**
     * @inheritdoc
     */

    public function rules() {
        return [
            [['work_date', 'project_name', 'remarks', 'authorized_by', 'updated_at'], 'safe'],
			[['badge_number','id','status'], 'integer'],
            [['work_hours'], 'number'],
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
        $query = WorkCredits::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

		if(!isset($params['sort'])) { $query->orderBy( ['created_at' => SORT_DESC] ); }

		if(!yii::$app->controller->hasPermission('work-credits/all')) {
			$query->orFilterWhere(['=','created_by',$_SESSION["badge_number"]]);
			$query->orFilterWhere(['=','badge_number',$_SESSION["badge_number"]]);
		}
		if(@$params['type']=='pen') {
			$query->andFilterWhere(['=','status',2]);
		}

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
			'status'=> $this->status,
            'work_hours' => $this->work_hours,
            'updated_at' => $this->updated_at,
        ]);

		if(isset($this->project_name)) {
			$query->andFilterWhere(['like', 'project_name', $this->project_name]); }
		if(isset($this->remarks)) {
			$query->andFilterWhere(['like', 'remarks', $this->remarks]); }
        if(isset($this->badge_number)) {
			$query->andFilterWhere(['like', 'badge_number', $this->badge_number.'%',false]); }
		if(isset($this->authorized_by)) {
			$query->andFilterWhere(['like', 'authorized_by', $this->authorized_by]); }

		$confParams  = Params::findOne('1');
		$nowDate = date('Y-m-d',strtotime(yii::$app->controller->getNowTime()));
		$DateChk = date("Y-".$confParams['sell_date'], strtotime(yii::$app->controller->getNowTime()));
		if((isset($this->work_date)) && ($this->work_date=='A')) {} 
		elseif((isset($this->work_date)) && ($this->work_date=='N')) {
			if ($DateChk <= $nowDate) {
				$query->andFilterWhere(['>','work_date', date('Y-12-31', strtotime("+0 years",strtotime($nowDate)))]);
			} else {
				$query->andFilterWhere(['>', 'work_date',date('Y-12-31', strtotime("-1 years",strtotime($nowDate)))]);
			}
		} 
		else {
			$this->work_date='C';
			if ($DateChk <= $nowDate) {
				$query->andFilterWhere(['like','work_date', date('Y', strtotime("-0 years",strtotime($nowDate)))]);
			} else {
				$query->andFilterWhere(['like', 'work_date',date('Y', strtotime("-1 years",strtotime($nowDate)))]);
			}
		}

//yii::$app->controller->createLog(true, 'trex-b-m-s-WCs', 'Raw Sql: '.var_export($query->createCommand()->getRawSql(),true));
        return $dataProvider;
    }
}
