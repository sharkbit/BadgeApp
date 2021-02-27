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
            [['item','sku','img','type'], 'safe'],
			[['item_id','stock','active','new_badge','paren'], 'integer'],
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
		if(isset($this->sku)) 	{ $query->andFilterWhere(['sku' => $this->sku]); }
		if(isset($this->paren)) 	{ $query->andFilterWhere(['paren' => $this->paren]); }
		if(isset($this->price)) { $query->andFilterWhere(['price'=>$this->price]); }
		if(isset($this->stock)) { $query->andFilterWhere(['stock' => $this->stock]); }
		if(isset($this->active)) { $query->andFilterWhere(['active' => $this->active]); }
		if(isset($this->new_badge)) { $query->andFilterWhere(['new_badge' => $this->new_badge]); }
		if(isset($this->type)) { $query->andFilterWhere(['type'=>$this->type]); }
        if(isset($this->item)) { $query->andFilterWhere(['like', 'item', $this->item]); }

		//yii::$app->controller->createLog(true, 'trex-m-s-StoreItemsSearch', 'Raw Sql: '.var_export($query->createCommand()->getRawSql(),true));
        return $dataProvider;
    }
}
