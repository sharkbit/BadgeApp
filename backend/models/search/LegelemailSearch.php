<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Legelemail;

/**
 * ClubsSearch represents the model behind the search form about `backend\models\Legelemail`.
 */

class LegelemailSearch extends Legelemail {
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['contact_id'], 'integer'],
            [['first_name','last_name','email','groups','title','office','committee','district','is_active'], 'safe'],
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
        $query = Legelemail::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
		
		if(!isset($params['sort'])) { $query->orderBy( ['last_name' => SORT_ASC] ); }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions

		if(isset($this->last_name)) { $query->andFilterWhere(['like','last_name',$this->last_name]); }
		if(isset($this->first_name)) { $query->andFilterWhere(['like','first_name',$this->first_name]); }
		if(isset($this->email)) { $query->andFilterWhere(['like','email',$this->email]); }
		if(isset($this->committee)) { $query->andFilterWhere(['like','committee',$this->committee]); }
		if(isset($this->title)) { $query->andFilterWhere(['like','title',$this->title]); }
				
		if(isset($this->contact_id)) { $query->andFilterWhere(['contact_id'=>$this->contact_id]); }
		if(isset($this->district)) { $query->andFilterWhere(['district'=>$this->district]); }
		if(isset($this->is_active)) { $query->andFilterWhere(['is_active'=>$this->is_active]); }

		if(isset($this->groups) && ($this->groups <>'')) {
			$query->andWhere("contact_id IN (SELECT contact_id FROM associat_agcnew.contact_groups WHERE group_id=".$this->groups.")"); }

        return $dataProvider;
    }
}
