<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\User;

/**
 * UserSearch represents the model behind the search form about `backend\models\User`.
 */
class UserSearch extends User {
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'status', 'created_at', 'updated_at','badge_number'], 'integer'],
			[['username', 'email', 'full_name','company', 'privilege', 'password_hash', 'password_reset_token','clubs'], 'safe'],
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
		$query = User::find();
		//->joinWith(['privileges']);

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if(empty($params['sort'])) { $query->orderBy( ['username' => SORT_ASC] ); }

		if (!in_array(1, json_decode(yii::$app->user->identity->privilege))) {
			$query->andWhere(" NOT JSON_CONTAINS(privilege,'1')");
		}

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			yii::$app->controller->createLog(true, 'trex-M-S-US NOT VALID', var_export($this->errors,true));
			return $dataProvider;
		}

		// grid filtering conditions
		if(!empty($this->id))			{ $query->andFilterWhere(['id' => $this->id]); }
		if(!empty($this->status))		{ $query->andFilterWhere(['status' => $this->status]); }
		if(!empty($this->created_at))	{ $query->andFilterWhere(['created_at' => $this->created_at]); }
		if(!empty($this->updated_at))	{ $query->andFilterWhere(['updated_at' => $this->updated_at]); }
		if(!empty($this->username))		{ $query->andFilterWhere(['like', 'username', $this->username]); }
		if(!empty($this->email)) 		{ $query->andFilterWhere(['like', 'email', $this->email]); }
		if(!empty($this->badge_number))	{ $query->andFilterWhere(['like', 'badge_number', $this->badge_number]); }
		if(!empty($this->full_name))		{ $query->andFilterWhere(['like', 'full_name', $this->full_name]); }
		if(!empty($this->privilege))		{ $query->andWhere("JSON_CONTAINS(privilege,'".$this->privilege."')"); }
		if(!empty($this->password_hash))	{ $query->andFilterWhere(['like', 'password_hash', $this->password_hash]); }
		if(!empty($this->password_reset_token))	{ $query->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token]); }
		//if(!empty($this->clubs)) { $query->andWhere("JSON_CONTAINS(user.clubs,'" ,$this->clubs ); }

//yii::$app->controller->createLog(true, 'trex-b-m-s-us', 'Raw Sql: '.var_export($query->createCommand()->getRawSql(),true));
	return $dataProvider;
	}
}
