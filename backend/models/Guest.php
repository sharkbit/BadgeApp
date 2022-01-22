<?php

namespace backend\models;

use Yii;
use backend\models\Badges;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "work_credits".
 *
 * @property integer $id
 * @property integer $badge_number
 * @property string $g_first_name
 * @property string $g_last_name
 * @property string $g_city
 * @property string $g_state
 * @property integer $g_yob
 * @property integer $tmp_badge
 * @property date $time_in
 * @property date $time_out
 */
class Guest extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
	public $g_zip;
    public $badge_holder_name;
	public $pagesize;
	public $guest_count;
	public $amount_due;
	public $payment_type;
	public $cc_address;
	public $cc_city;
	public $cc_state;
	public $cc_zip;
	public $cc_name;
	public $cc_num;
	public $cc_cvc;
	public $cc_exp_mo;
	public $cc_exp_yr;
	public $cc_x_id;
	public $tax;

    public static function tableName() {
        return 'guest';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
			[['badge_number', 'g_first_name', 'g_last_name', 'time_in','guest_count','payment_type'], 'required'],
			[['time_in', 'time_out','g_address','badge_holder_name','cc_x_id','amount_due'], 'safe'],
			[['badge_number','tmp_badge','pagesize','cc_zip','cc_exp_mo','cc_exp_yr'], 'integer'],
			[['g_yob'], 'integer', 'max' => 3999,'min'=>1900],
			[['g_city','cc_city','cc_address','cc_name'], 'string', 'max' => 255],
			[['g_first_name', 'g_last_name'], 'string', 'max' => 35],
			[['g_state','cc_state','g_paid'], 'string', 'max' => 2],
			['cc_num','string','min'=>15],
			['cc_cvc','string','min'=>3],
			[['g_zip','tax'],'number'],
			[['cc_name','cc_address','cc_city','cc_state','cc_zip','cc_num','cc_cvc'], 'required', 'when' => function ($model) {
				return $model->payment_type == 'creditnow';},
			'whenClient' => "function (attribute, value) { return $('#guest-payment_type').val() == 'creditnow'; }"
			],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'badge_number' => 'Badge Number',
            'g_first_name' => 'Guest First Name',
			'g_last_name' => 'Guest Last Name',
			'cc_address' => 'Address',
			'g_zip' => 'ZIP Code',
            'g_city' => 'City',
            'g_state' => 'State',
            'g_yob' => 'YoB',
			'g_paid' => 'Paid',
			'tmp_badge' => 'Temp Badge #',
            'time_in' => 'Time In',
            'time_out' => 'Time Out',
			'cc_city'=>'City',
			'cc_state'=>'State',
			'cc_zip'=>'Zip',
			'cc_name'=>'Name',
			'cc_num'=>'Card Number',
			'cc_cvc'=>'CVC',
			'cc_exp_mo'=>'Exp Mon',
			'cc_exp_yr'=>'exp Yr',
        ];
    }
}
