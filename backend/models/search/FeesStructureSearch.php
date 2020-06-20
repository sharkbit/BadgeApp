<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\FeesStructure;

/**
 * FeesStructureSearch represents the model behind the search form about `backend\models\FeesStructure`.
 */
class FeesStructureSearch extends FeesStructure
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['label','membership_id','sku_full','sku_half','status','type'], 'safe'],
            [['fee'], 'number'],
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
        $query = FeesStructure::find();

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
        $query->andFilterWhere([
            'id' => $this->id,
            'membership_id' => $this->membership_id,
            'fee' => $this->fee,
        ]);

        $query->andFilterWhere(['like', 'label', $this->label])
        ->andFilterWhere(['like', 'type', $this->type])
        ->andFilterWhere(['like', 'status', $this->status]);

        //$query->andFilterWhere(['like','membership_type.type',$this->membership_id]);
            //->andFilterWhere(['like','membership_type.id', $this->membership_id]);

        return $dataProvider;
    }
}
