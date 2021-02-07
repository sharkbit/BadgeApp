<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "Events".
 */

class LoginAccess extends \yii\db\ActiveRecord{

	public static function tableName() {
		return 'login_access';
	}

	public function rules() {
		return [
		   [['l_date','module','l_name','ip','l_status'], 'safe'],
		   [['l_id'], 'number'],
	   ];
	}

	public function attributeLabels() {
		return [
			'l_id' => 'ID',
			'l_date' => 'Date',
			'ip' => 'IP',
			'l_name'=>'Name',
			'l_status' => 'Status',			
	   ];
	}
}