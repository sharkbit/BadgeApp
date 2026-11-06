<?php

namespace backend\models;

use common\models\User;
use Yii;
use yii\base\Model;
use yii\db\Expression;

class InitialAdminForm extends Model {
	public $username;
	public $email;
	public $full_name;
	public $password;
	public $confirmPassword;

	public function rules() {
		return [
			[['username', 'email', 'full_name', 'password', 'confirmPassword'], 'required'],
			['username', 'match', 'pattern' => '/^[A-Za-z0-9._-]+$/'],
			[['username', 'email', 'full_name'], 'string', 'max' => 255],
			['username', 'unique', 'targetClass' => User::class],
			['email', 'email'],
			['email', 'unique', 'targetClass' => User::class],
			['password', 'string', 'min' => 12],
			['confirmPassword', 'compare', 'compareAttribute' => 'password'],
		];
	}

	public static function isSetupRequired() {
		$hasRootUser = User::find()
			->where(new Expression('JSON_CONTAINS([[privilege]], :rootPrivilege)', [
				':rootPrivilege' => '1',
			]))
			->exists();

		return !$hasRootUser;
	}

	public function createFirstAdmin() {
		if (!$this->validate()) {
			return null;
		}

		$lockName = 'badgeapp_initial_admin_setup';
		$lockAcquired = (int)Yii::$app->db->createCommand(
			'SELECT GET_LOCK(:lockName, 10)',
			[':lockName' => $lockName]
		)->queryScalar() === 1;
		if (!$lockAcquired) {
			$this->addError('username', 'Initial account setup is busy. Please try again.');
			return null;
		}

		try {
			if (!self::isSetupRequired()) {
				$this->addError('username', 'Initial account setup has already been completed.');
				return null;
			}

			$user = new User();
			$user->username = $this->username;
			$user->email = $this->email;
			$user->full_name = $this->full_name;
			$user->privilege = json_encode([1]);
			$user->status = User::STATUS_ACTIVE;
			$user->setPassword($this->password);
			$user->generateAuthKey();

			if (!$user->save(false)) {
				$this->addError('username', 'The initial administrator account could not be created.');
				return null;
			}

			return $user;
		} finally {
			Yii::$app->db->createCommand(
				'SELECT RELEASE_LOCK(:lockName)',
				[':lockName' => $lockName]
			)->queryScalar();
		}
	}
}
