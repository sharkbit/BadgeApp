<?php

namespace backend\models;

use Yii;

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
            [['username', 'email', 'auth_key', 'password_hash', 'created_at', 'updated_at'], 'required'],
            [['privilege'], 'string'],
			[['clubs'],'safe'],
            [['badge_number','status','created_at', 'updated_at'], 'integer'],
            [['username', 'email', 'full_name', 'password_hash', 'password_reset_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
			[['company'], 'string', 'max' => 45],
            [['username'], 'unique','message' => 'This username has already been taken.'],
            [['email'], 'unique','message' => 'This email has already been taken.'],
            [['badge_number','password_reset_token'], 'unique','message' => 'Try new password.'],
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
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}