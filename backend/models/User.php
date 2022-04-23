<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;
use backend\models\Privileges;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property string $full_name
 * @property string $privilege
 * @property integer $status
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property integer $created_at
 * @property integer $updated_at
 */

class User extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['username', 'email', 'password_hash', 'created_at', 'updated_at'], 'required'],
			[['clubs','privilege'],'safe'],
            [['badge_number','status','created_at', 'updated_at'], 'integer'],
            [['username', 'email', 'full_name', 'password_hash', 'password_reset_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
			[['company','r_user'], 'string', 'max' => 45],
            [['username'], 'unique','message' => 'This username has already been taken.'],
            [['email'], 'unique','message' => 'This email has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
	 public function attributeLabels() {
        return [
            'id' => 'ID',
            'username' => 'Username',
			'clubs'=>'Calendar Access',
            'email' => 'Email',
            'full_name' => 'Full Name',
			'company' => 'Company',
            'privilege' => 'Privilege',
            'status' => 'Status',
			'badge_number' => 'Badge Number',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
			'r_user' => 'Remote User Name (Case sensitive)',
        ];
    }

	public function getPrivileges() {
		return $this->hasMany(Privileges::className(),['id'=>'privilege']);
	}

	public function getPrivilege_Names($levels) {
		if(!is_array($levels)) {$levels = json_decode($levels); }
		$privs = (new Privileges)->find()->all();
		$found='';
		foreach ($privs as $level) {
			if (in_array($level->id,$levels))
				$found .= $level->privilege.', ';
		}
		return rtrim ($found,", ");
	}

	public function getPriv($level) {
		$sql = "SELECT privilege FROM user_privileges WHERE id=".$level;
		$command = Yii::$app->db->createCommand($sql);
		$privArray= $command->queryAll();
		return $privArray[0]['privilege'];
	}

    public function getPrivList($limit=null) {
		if (in_array(1, json_decode(yii::$app->user->identity->privilege))) { $where =''; }
		elseif($limit=='usr_filter') { $where =' where id >=2 '; }
		elseif(is_array($limit)) {
			$where='';
			foreach ($limit as $id) { $where .= " OR id=".$id; }
			$where =' WHERE restricted=0 '.$where;
		}
		else {
			//if ($limit) { $where =' where id >2 '; }
			//else { $where =' where id >=2 '; }
			$where =' where restricted=0 ';
		}
		$sql = "SELECT id,privilege FROM user_privileges $where order by priv_sort ASC";
		$privArray= Yii::$app->db->createCommand($sql)->queryAll();
        return ArrayHelper::map($privArray,'id','privilege');
    }
}