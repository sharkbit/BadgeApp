<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

class RuleList extends \yii\db\ActiveRecord {

    public static function tableName() {
        return 'rule_list';
    }

    public function rules() {
        return [
            [['rule_abrev','rule_name','vi_type'], 'required'],
            [['rule_name'], 'string', 'max' => 255],
			[['rule_abrev'], 'string', 'max' => 6],
			[['vi_type','is_active'], 'integer']
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
			'rule_abrev' => 'Location',
			'vi_type' => 'Class',
            'rule_name' => 'Short Description'
        ];
    }

	public function getRules() {
		$sql="SELECT CONCAT(rule_abrev,'-C',vi_type) as rule_ab, CONCAT(rule_abrev,'-C',vi_type,' - ',rule_name) as rul_name FROM rule_list where is_active='1'";
		$connection = Yii::$app->getDb();
		$command = $connection->createCommand($sql);
		$rules = $command->queryAll(); 
		return ArrayHelper::map($rules,'rule_ab','rul_name');;
	}

	public function getRuleNames($Rule_ids) {
		if (strpos($Rule_ids,',')) {
			
			$rul = explode(", ",$Rule_ids);
			$ruleWhere='';
			foreach($rul as $oneRun) {
				$rules = explode("-C",$oneRun);
				$ruleWhere .= " OR (rule_abrev='".$rules[0]."' AND vi_type=".$rules[1].")" ; 
			}
		} else {
			$rul = explode("-C",$Rule_ids);
			$ruleWhere = " rule_abrev='".$rul[0]."' AND vi_type=".$rul[1] ; 
		}
		$sql="SELECT CONCAT(rule_abrev,'-C',vi_type) as rule_ab, CONCAT(rule_abrev,'-C',vi_type,' - ',rule_name) as rul_name ".
			"FROM rule_list WHERE ".ltrim($ruleWhere, ' OR ');
		$connection = Yii::$app->getDb();
		$command = $connection->createCommand($sql);
		$rules = $command->queryAll(); 
		return implode(ArrayHelper::map($rules,'rule_ab','rul_name'),", ");
	}
}
