<?php
namespace frontend\models;

use yii\base\Model;
use common\models\User;
use yii;

/**
 * Signup form
 */
class SignupForm extends Model {
    public $username;
    public $email;
	public $badge_number;
    public $password;
    public $full_name;
	public $f_name;
	public $l_name;
    public $confirm_password;
    public $privilege;
	public $id;
	public $auth_key;
	public $clubs;
	public $r_user;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['username','f_name','l_name','email','password','confirm_password','privilege'],'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            [['crated_at','updated_at','clubs','privilege'],'safe'],
            [['username', 'email'], 'trim'],
            ['email', 'email'],
			[['auth_key'],'string','max'=>100],
            [['username','email'], 'string', 'min' => 2, 'max' => 255],
			[['r_user'], 'string', 'max' => 45],
			['email', 'filter', 'filter' => 'trim'],
            ['password', 'string', 'min' => 6],
			[['email'],'unique','message'=>'Email already exist. Please try another one.'],
			['confirm_password', 'compare', 'compareAttribute'=>'password', 'message'=>"Passwords don't match"],
            [['badge_number','id'],'integer'],
        ];
    }

    public function attributeLabels() {
        return [
			'clubs'=>'Calendar Access',
            'f_name' => 'First Name',
			'l_name' => 'Last Name',
			'r_user' => 'Remote User Name'
        ];
    }

    public function signup() {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
		$user->badge_number = $this->badge_number;
		$user->clubs = json_encode($this->clubs);
        $user->full_name = $this->f_name." ".$this->l_name;
        $user->privilege = $this->privilege;
		$user->r_user = $this->r_user;
        $user->created_at = strtotime($this->getNowTime());
        $user->updated_at = strtotime($this->getNowTime());
        $user->setPassword($this->password);
        $user->generateAuthKey();

        return $user->save() ? $user : null;
    }

    public function getNowTime() {

        date_default_timezone_set(yii::$app->params['timeZone']);
        $now = date('Y-m-d H:i:s');
        return $now;
    }
}
