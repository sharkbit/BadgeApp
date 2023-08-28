<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\BadgesDatabase;

/**
 * BadgesDatabaseSearch represents the model behind the search form about `backend\models\BadgesDatabase`.
 */
class BadgesDatabaseSearch extends BadgesDatabase {
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'club_id', 'mem_type', 'primary'], 'integer'],
            [['badge_number', 'prefix', 'first_name', 'last_name', 'suffix', 'address', 'city', 'state', 'zip', 'gender', 'yob', 'email', 'email_vrfy', 'phone', 'phone_op', 'ice_contact', 'ice_phone', 'incep', 'expires', 'qrcode', 'wt_date', 'wt_instru', 'remarks', 'status', 'soft_delete', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
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
    public function search($params)
    {
        $query = BadgesDatabase::find()
			->joinWith(['membershipType'])
			->joinWith(['badgeToYear'])
			->joinWith(['clubView']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

		if(!isset($params['sort'])) { $query->orderBy( ['updated_at' => SORT_DESC] ); }
				
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
		if(isset($this->club_id) && ($this->club_id <>'')) {
			$query->andWhere("badges.badge_number IN (SELECT badge_number FROM badge_to_club WHERE club_id=".$this->club_id.")"); }

		if(isset($this->status)) 		{ $query->andFilterWhere(['status' => $this->status]); }
		if(isset($this->mem_type)) 		{ $query->andFilterWhere(['mem_type' => $this->mem_type]); }
		if(isset($this->primary)) 		{ $query->andFilterWhere(['primary' => $this->primary]); }
		if(isset($this->incep)) 		{ $query->andFilterWhere(['incep' => $this->incep]); }
		if(isset($this->expires)) 		{ $query->andFilterWhere(['expires' => $this->expires]); }
		if(isset($this->wt_date)) 		{ $query->andFilterWhere(['wt_date' => $this->wt_date]); }
		if(isset($this->created_at)) 	{ $query->andFilterWhere(['created_at' => $this->created_at]); }
		if(isset($this->updated_at)) 	{ $query->andFilterWhere(['updated_at' => $this->updated_at]); }

		if(isset($this->badge_number)) 	{ $query->andFilterWhere(['like', 'badge_number', $this->badge_number]); }
		if(isset($this->prefix)) 		{ $query->andFilterWhere(['like', 'prefix', $this->prefix]); }
		if(isset($this->first_name)) 	{ $query->andFilterWhere(['like', 'first_name', $this->first_name]); }
		if(isset($this->last_name)) 	{ $query->andFilterWhere(['like', 'last_name', $this->last_name]); }
		if(isset($this->suffix)) 		{ $query->andFilterWhere(['like', 'suffix', $this->suffix]); }
		if(isset($this->address)) 		{ $query->andFilterWhere(['like', 'address', $this->address]); }
		if(isset($this->city))			{ $query->andFilterWhere(['like', 'city', $this->city]); }
		if(isset($this->state)) 		{ $query->andFilterWhere(['like', 'state', $this->state]); }
		if(isset($this->zip)) 			{ $query->andFilterWhere(['like', 'zip', $this->zip]); }
		if(isset($this->gender)) 		{ $query->andFilterWhere(['like', 'gender', $this->gender]); }
		if(isset($this->yob)) 			{ $query->andFilterWhere(['like', 'yob', $this->yob]); }
		if(isset($this->email)) 		{ $query->andFilterWhere(['like', 'email', $this->email]); }
		if(isset($this->phone)) 		{ $query->andFilterWhere(['like', 'phone', $this->phone]); }
		if(isset($this->phone_op)) 		{ $query->andFilterWhere(['like', 'phone_op', $this->phone_op]); }
		if(isset($this->ice_contact)) 	{ $query->andFilterWhere(['like', 'ice_contact', $this->ice_contact]); }
		if(isset($this->ice_phone)) 	{ $query->andFilterWhere(['like', 'ice_phone', $this->ice_phone]); }
		if(isset($this->qrcode)) 		{ $query->andFilterWhere(['like', 'qrcode', $this->qrcode]); }
		if(isset($this->wt_instru)) 	{ $query->andFilterWhere(['like', 'wt_instru', $this->wt_instru]); }
		if(isset($this->remarks)) 		{ $query->andFilterWhere(['like', 'remarks', $this->remarks]); }

        return $dataProvider;
    }
}
