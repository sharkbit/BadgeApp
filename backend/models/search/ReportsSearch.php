<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Reports;

/**
 * ParamsSearch represents the model behind the search form about `backend\models\Reports`.
 */
class ReportsSearch extends Reports {
	
	public $year;

	public function rules() {
		return [
			[['tx_month', 'tx_type', 'item_count'], 'safe'],
		];
	}

	public function scenarios() {
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}

	public function search($params) {
		$query = Reports::find()->orderBy(['tx_month' => SORT_ASC, 'tx_type' => SORT_ASC]);
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => false,
		]);

		$this->load($params);

		if (!$this->validate()) {
			$query->where('0=1');
			return $dataProvider;
		}

		$query->andFilterWhere(['tx_month' => $this->tx_month])
			->andFilterWhere(['tx_type' => $this->tx_type])
			->andFilterWhere(['item_count' => $this->item_count]);

		return $dataProvider;
	}
}
