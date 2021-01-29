<?php
namespace backend\models;

use Yii;

/**
 * This is the model class for table "params".
 *
 * @property integer $id
 * @property string $qb_oauth_cust_key
 * @property string $qb_oauth_cust_sec
 * @property string $qb_realmId
 */

class Params extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
	 public $AddWhitelist;

    public static function tableName() {
        return 'params';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['sell_date','guest_sku','guest_total'], 'required'],
			[['pp_id','pp_sec','remote_users'],'string'],
			[['conv_p_merc_id','conv_p_user_id','conv_p_pin','conv_d_merc_id','conv_d_user_id','conv_d_pin'],'string'],
			[['qb_env','qb_oauth_cust_key','qb_oauth_cust_sec','qb_realmId','qb_token_date','qb_token'], 'string'],
			[['qb_oa2_id','qb_oa2_sec','qb_oa2_realmId','qb_oa2_access_token','qb_oa2_access_date','qb_oa2_refresh_token','qb_oa2_refresh_date'], 'string'],
			[['sell_date'], 'string', 'max' => 5],
			[['log_rotate'],'integer'],
			[['whitelist'],'safe'],
        ];
    }
	
    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
			'remote_users' => 'Remote Users File',
			'sell_date' => 'Badge Sales Start Date (MM-DD)',
			'pp_id'=>'PayPal Client ID',
			'pp_sec'=>'PayPal Clinet Secret',
			'qb_env'=>'Enviroment',
			'conv_p_merc_id'=>'C Merchant ID (Prod)',
			'conv_p_user_id'=>'C User ID (Prod)',
			'conv_p_pin'=>'C Pin (Prod)',
			'conv_d_merc_id'=>'C Merchant ID (Dev)',
			'conv_d_user_id'=>'C User ID (Dev)',
			'conv_d_pin'=>'C Pin (Dev)',
            'qb_oauth_cust_key' => 'QB OA2 Dev Client ID',
			'qb_oauth_cust_sec' => 'QB OA2 Dev Client Secret',
            'qb_realmId' => 'Quickbooks RealmID',
            'qb_token_date' => 'QB Token issue date',
			'qb_token' => 'QB Token',
			'qb_oa2_id' => 'QB OA2 Prod Client ID',
			'qb_oa2_sec' => 'QB OA2 Prod Client Secret',
			'qb_oa2_realmId' => 'QB OA2 RealmID',
			'qb_oa2_access_token' => 'QB OA2 Access Token',
			'qb_oa2_access_date' =>  'QB OA2 Access Exp Date',
			'qb_oa2_refresh_token'=> 'QB OA2 Refresh Token',
			'qb_oa2_refresh_date' => 'QB OA2 Refresh Exp Date',
        ];
    }
}
