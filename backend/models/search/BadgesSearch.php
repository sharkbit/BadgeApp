<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Badges;

/**
 * BadgesSearch represents the model behind the search form about `backend\models\Badges`.
 */
class BadgesSearch extends Badges {
    /**
     * @inheritdoc
     */
    public $expire_date_range;
    public $expire_condition;
    public $nowDateplus2;
    public $nowDate;
    public $nowDateMin2;
    public $nowDateMin5;

    public function rules() {
        return [
            [['badge_number','club_id', 'mem_type', 'primary'], 'integer'],
            [['prefix', 'first_name', 'last_name', 'suffix', 'address', 'city', 'state', 'zip', 'gender', 'yob', 'email','email_vrfy', 'phone', 'phone_op', 'ice_contact', 'ice_phone', 'incep', 'wt_date', 'wt_instru', 'payment_method','status','expire_date_range','expire_condition'], 'safe'],
            [['badge_fee', 'discounts', 'amt_due'], 'number'],
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
        $query = Badges::find()
			->joinWith(['membershipType'])
			->joinWith(['badgeToYear'])
			->joinWith(['clubView']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
//yii::$app->controller->createLog(false, 'trex_M_S_BS params', var_export($params,true));
		if(!isset($params['sort'])) { 
			$query->orderBy( ['updated_at' => SORT_DESC] ); 
		}
		
        $this->nowDateplus2 = date('Y-m-d', strtotime("+2 years",strtotime(yii::$app->controller->getNowTime())));
        $this->nowDateMin2 = date('Y-m-d', strtotime("-2 years",strtotime(yii::$app->controller->getNowTime())));
        $this->nowDate = date('Y-m-d',strtotime(yii::$app->controller->getNowTime()));
        $this->nowDateMin5 = date('Y-m-d', strtotime("-5 years",strtotime(yii::$app->controller->getNowTime())));

        if($this->expire_condition==null) {
            $this->expire_condition = 'all';
        }

        if($this->expire_condition=='active+2') {
            $query->andFilterWhere(['>=','bn_to_by.badge_year',$this->nowDate]);
            $query->orFilterWhere(['between','bn_to_by.badge_year',$this->nowDateMin2, $this->nowDate]);
        }
        else if($this->expire_condition=='active') {
            $query->andFilterWhere(['>=','bn_to_by.badge_year',$this->nowDate]);
        }
        else if($this->expire_condition=='expired<2') {
            $query->andFilterWhere(['between','bn_to_by.badge_year',$this->nowDateMin2,$this->nowDate]);
        }
        else if($this->expire_condition=='expired>2') {
             $query->andFilterWhere(['<','bn_to_by.badge_year',$this->nowDateMin2]);
        }
        else if($this->expire_condition=='inactive') {
             $query->andFilterWhere(['<','bn_to_by.badge_year',$this->nowDateMin5]);
        }
        else { /* no filter needed for all */ }

		if(!yii::$app->controller->hasPermission('badges/all')) {
			$query->andFilterWhere(['badges.badge_number'=>$_SESSION["badge_number"]]);
		}

        if($this->expire_date_range==null) {
            $expireDateRange[0] = '2000-01-01';
            $expireDateRange[1] = '2100-01-01';
        }
        else  {
            $tempexpireDateRange = explode(' - ', $this->expire_date_range);
            $expireDateRange[0] = date('Y-m-d',strtotime($tempexpireDateRange[0]));
            $expireDateRange[1] = date('Y-m-d',strtotime($tempexpireDateRange[1]));
        }
        
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
			yii::$app->controller->createLog(false, 'trex-m-s-bs:112 NOT VALID', var_export($this->errors,true));
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'yob' => $this->yob,
            'mem_type' => $this->mem_type
        ]);

        if(isset($this->club_id) && ($this->club_id <>'')) {
			$query->andWhere("badges.badge_number IN (SELECT badge_number FROM badge_to_club WHERE club_id=".$this->club_id.")"); }

		if(isset($this->badge_number)) { 
			$this->badge_number=ltrim($this->badge_number, '0');
			$query->andFilterWhere(['badges.badge_number'=>$this->badge_number]);  
		}
		if(isset($this->first_name)) { $query->andFilterWhere(['like', 'first_name', $this->first_name]); }
		if(isset($this->last_name)) { $query->andFilterWhere(['like', 'last_name', $this->last_name]); }
		if(isset($this->suffix)) { $query->andFilterWhere(['like', 'suffix', $this->suffix]); }
		if(isset($this->status)) { $query->andFilterWhere(['like', 'badges.status', $this->status]); }
			
//yii::$app->controller->createLog(true, 'trex-b-m-s-bs', 'Raw Sql: '.var_export($query->createCommand()->getRawSql(),true));
		return $dataProvider;
    }
}
