<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "privilege".
 *
 * @property integer $id
 */
class Sales extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
	public $badge_number;
	public $first_name;
	public $last_name;
	public $address;
	public $zip;
	public $city;
	public $state;
	public $email;
	public $cart;
	public $total;
	public $payment_method;
	public $cc_num;
	public $cc_exp_mo;
	public $cc_exp_yr;
	public $cc_cvc;
	public $cc_x_id;
	
	
    public static function tableName() {
        return ;//'null';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['badge_number', 'total','payment_method','first_name','last_name'], 'required'],
            [['first_name','last_name','address','city','state','email','cart','payment_method','cc_num','cc_exp_mo','cc_exp_yr','cc_x_id'], 'string'],
			[['badge_number','zip'], 'integer'],
            [['total','cc_cvc'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'address' => 'Address',
			'badge_number'=>'Badge Number',
			'cart' => 'cart',
			'cc_cvc' => 'CVC',
			'cc_exp_mo' => 'Month',
			'cc_exp_yr' => 'Year',
			'cc_num' => 'Credit Card Number',
			'cc_x_id' =>'cc_x_id',
			'city' => 'City',
			'email' => 'Email',
			'first_name' => 'First Name',
			'last_name' => 'Last Name',
            'payment_method' => 'Payment Method',
			'state' => 'State',
			'total' => 'Total',
        ];
    }
}
