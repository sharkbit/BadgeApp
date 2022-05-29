<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Guest;

/**
 * GuestSearch represents the model behind the search form about `backend\models\Guest`.
 */
class GuestSearch extends Guest {
    /**
     * @inheritdoc
     */
	public $atRange_condition;
	public $q_Limit;

    public function rules() {
        return [
			[['badge_number','tmp_badge','g_yob'], 'integer'],
			[['g_first_name','g_last_name','g_city','g_state','g_paid'], 'safe'],
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
        $query = Guest::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

		if(!isset($params['sort'])) { $query->orderBy( ['time_in' => SORT_DESC] ); }

		if (isset($params['GuestSearch']['atRange_condition'])){
			$this->atRange_condition = $params['GuestSearch']['atRange_condition'];
		}

		if($this->atRange_condition==null) {
            $this->atRange_condition = 'atRange';
        }

		if($this->atRange_condition=='atRange') {
			$query->andWhere(['is', 'time_out', null]);
		}
		else if($this->atRange_condition=='gone') {
			$query->andWhere(['not', ['time_out'=> null]]);
		} else { }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
			yii::$app->controller->createLog(true, 'trex-M-S-GS NOT VALID', var_export($this->errors,true));
            return $dataProvider;
        }

        // grid filtering conditions
		if(isset($this->g_state)) { $query->andFilterWhere(['g_state'=>$this->g_state]); }
		if(isset($this->g_yob)) { $query->andFilterWhere(['g_yob'=>$this->g_yob]); }
		if(isset($this->g_paid)) { $query->andFilterWhere(['g_paid'=>$this->g_paid]); }
		if(isset($this->badge_number)) { $query->andFilterWhere(['badge_number'=>$this->badge_number]); }
		if(isset($this->g_first_name)) { $query->andFilterWhere(['like', 'g_first_name', $this->g_first_name]); }
		if(isset($this->g_last_name)) { $query->andFilterWhere(['like', 'g_last_name', $this->g_last_name]); }
		if(isset($this->g_city)) { $query->andFilterWhere(['like', 'g_city', $this->g_city]); }

//yii::$app->controller->createLog(false, 'trex-B_M_S_Guest Search Query OK: ', var_export($query->createCommand()->getRawSql(),true));
        return $dataProvider;
    }
}
