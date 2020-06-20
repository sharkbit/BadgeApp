<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\StoreItems;

/**
 * SalesSearch represents the model behind the search form about `backend\models\StoreItems`.
 */
class StoreItemsSearch extends StoreItems {
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['item','sku','img'], 'safe'],
			[['item_id','stock','active','new_badge'], 'integer'],
			[['price'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {

       // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params) {
        $query = StoreItems::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
		
		if(!isset($params['sort'])) { $query->orderBy( ['item' => SORT_ASC] ); }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
			yii::$app->controller->createLog(true, 'trex-m-s-StoreItemsSearch NOT VALID', var_export($this->errors,true));
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'sku' => $this->sku,
			'price'=>$this->price,
			'stock' => $this->stock,
			'active' => $this->active,
			'new_badge' => $this->new_badge,
			
        ]);

        $query->andFilterWhere(['like', 'item', $this->item]);

        return $dataProvider;
    }
}
