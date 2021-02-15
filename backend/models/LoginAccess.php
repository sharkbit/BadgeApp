<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "Events".
 */

class LoginAccess extends \yii\db\ActiveRecord{

	public static function tableName() {
		$tablename = 'login_access_'.explode ('.',$_SERVER['HTTP_HOST'])[0];
		if (Yii::$app->db->getTableSchema($tablename,true)===null) {
			$sqlQuery = "CREATE TABLE IF NOT EXISTS $tablename (".
				"`l_id` INT NOT NULL AUTO_INCREMENT,".
				"`l_date` DATETIME NULL,".
				"`module` VARCHAR(45) NULL,".
				"`l_name` VARCHAR(80) NULL,".
				"`ip` VARCHAR(45) NULL,".
				"`l_status` VARCHAR(45) NULL,".
				"PRIMARY KEY (`l_id`));";
			$sqlCommand = Yii::$app->db->createCommand($sqlQuery);
			$sqlCommand->execute();
			Yii::$app->db->schema->refreshTableSchema($tablename);
		}

		return $tablename;
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