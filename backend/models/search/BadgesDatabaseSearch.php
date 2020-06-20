<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\BadgesDatabase;

/**
 * BadgesDatabaseSearch represents the model behind the search form about `backend\models\BadgesDatabase`.
 */
class BadgesDatabaseSearch extends BadgesDatabase
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
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
        $query = BadgesDatabase::find();

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
        $query->andFilterWhere([
            'status' => $this->status,
            'club_id' => $this->club_id,
            'mem_type' => $this->mem_type,
            'primary' => $this->primary,
            'incep' => $this->incep,
            'expires' => $this->expires,
            'wt_date' => $this->wt_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'badge_number', $this->badge_number])
            ->andFilterWhere(['like', 'prefix', $this->prefix])
            ->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'suffix', $this->suffix])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'state', $this->state])
            ->andFilterWhere(['like', 'zip', $this->zip])
            ->andFilterWhere(['like', 'gender', $this->gender])
            ->andFilterWhere(['like', 'yob', $this->yob])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'phone_op', $this->phone_op])
            ->andFilterWhere(['like', 'ice_contact', $this->ice_contact])
            ->andFilterWhere(['like', 'ice_phone', $this->ice_phone])
            ->andFilterWhere(['like', 'qrcode', $this->qrcode])
            ->andFilterWhere(['like', 'wt_instru', $this->wt_instru])
            ->andFilterWhere(['like', 'remarks', $this->remarks]);

        return $dataProvider;
    }
}
