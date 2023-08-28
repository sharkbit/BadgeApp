<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\MembershipType;

/**
 * MembershipTypeSearch represents the model behind the search form about `backend\models\MembershipType`.
 */
class MembershipTypeSearch extends MembershipType
{
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id','renew_yearly','self_service'], 'integer'],
            [['status','type'], 'safe'],
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
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = MembershipType::find();

        // add conditions that should always apply here


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        //echo'<pre>'; print_r($this->membership_id); die();
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        if(isset($this->id)) { $query->andFilterWhere(['id' => $this->id]); }
		if(isset($this->renew_yearly)) { $query->andFilterWhere(['renew_yearly' => $this->renew_yearly]); }
		if(isset($this->self_service)) { $query->andFilterWhere(['self_service' => $this->self_service]); }
		if(isset($this->status)) { $query->andFilterWhere(['like', 'status', $this->status]); }
		if(isset($this->type)) { $query->andFilterWhere(['like', 'type', $this->type]); }

        return $dataProvider;
    }
}
