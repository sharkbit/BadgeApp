<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "privilege".
 *
 * @property integer $id
 */
class Privileges extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'user_privileges';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['privilege', 'priv_sort','timeout'], 'required'],
            [['privilege'], 'string'],
			[['priv_sort'], 'integer'],
            [['timeout'], 'integer', 'min'=>2, 'max' => 60],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
			'privilege' => 'Privilege Title',
            'priv_sort' => 'Sort Order',
            'timeout' => 'Time-out',
            'privilege' => 'User Privilege',
        ];
    }

	public function getPriv($level) {
		$sql = "SELECT privilege FROM user_privileges WHERE id=".$level;
		$command = Yii::$app->db->createCommand($sql);
		$privArray= $command->queryAll();
		return $privArray[0]['privilege'];
	}

    public function getPrivList($limit=null) {
		if ($limit) { $where =' where id >2 '; } else { $where =''; }
		
		$sql = "SELECT id,privilege FROM user_privileges $where order by priv_sort ASC";
		$command = Yii::$app->db->createCommand($sql);
		$privArray= $command->queryAll();
        return ArrayHelper::map($privArray,'id','privilege');
    }
}
