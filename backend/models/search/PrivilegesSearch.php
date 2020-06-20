<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Privileges;

/**
 * PrivilegesSearch represents the model behind the search form about `backend\models\Privileges`.
 */
class PrivilegesSearch extends Privileges {
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['priv_sort','timeout'], 'integer'],
            [['privilege'], 'safe'],
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
        $query = Privileges::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
		
		if(!isset($params['sort'])) { $query->orderBy( ['priv_sort' => SORT_ASC] ); }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
			yii::$app->controller->createLog(true, 'trex-m-s-PrivSearch NOT VALID', var_export($this->errors,true));
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
			'priv_sort' => $this->priv_sort,
			'timeout'=>$this->timeout
        ]);

        $query->andFilterWhere(['like', 'privilege', $this->privilege]);

        return $dataProvider;
    }
}
