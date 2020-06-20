<?php
namespace backend\models;
use yii\helpers\ArrayHelper;

use Yii;

/**
 * This is the model class for table "badges".
 *
 * @property integer $privilege
 */
class UserPrivileges extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */

    public $privilege;


    public static function tableName() {
        return 'user_privileges';
    }

    /**
     * @inheritdoc
     */
    public function rules() {

    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
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
