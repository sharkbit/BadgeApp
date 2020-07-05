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
			[['username', 'email', 'full_name','company', 'privilege', 'auth_key', 'password_hash', 'password_reset_token'], 'safe'],
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

		if(!isset($params['sort'])) { $query->orderBy( ['username' => SORT_ASC] ); }

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			yii::$app->controller->createLog(true, 'trex-M-S-US NOT VALID', var_export($this->errors,true));
			return $dataProvider;
		}

		// grid filtering conditions
		if(isset($this->id))			{ $query->andFilterWhere(['id' => $this->id]); }
		if(isset($this->status))		{ $query->andFilterWhere(['status' => $this->status]); }
		if(isset($this->created_at))	{ $query->andFilterWhere(['created_at' => $this->created_at]); }
		if(isset($this->updated_at))	{ $query->andFilterWhere(['updated_at' => $this->updated_at]); }
		if(isset($this->username))		{ $query->andFilterWhere(['like', 'username', $this->username]); }
		if(isset($this->email)) 		{ $query->andFilterWhere(['like', 'email', $this->email]); }
		if(isset($this->badge_number))	{ $query->andFilterWhere(['like', 'badge_number', $this->badge_number]); }
		if(isset($this->full_name))		{ $query->andFilterWhere(['like', 'full_name', $this->full_name]); }
		if(isset($this->privilege))		{ $query->andFilterWhere(['like', 'user.privilege', $this->privilege]); }
		if(isset($this->auth_key))		{ $query->andFilterWhere(['like', 'auth_key', $this->auth_key]); }
		if(isset($this->password_hash))	{ $query->andFilterWhere(['like', 'password_hash', $this->password_hash]); }
		if(isset($this->password_reset_token))	{ $query->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token]); }

	return $dataProvider;
	}
}