<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "badges".
 *
 * @property integer $id
 * @property string $badge_number
 * @property string $prefix
 * @property string $first_name
 * @property string $last_name
 * @property string $suffix
 * @property string $address
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $gender
 * @property string $yob
 * @property string $email
 * @property string $phone
 * @property string $phone_op
 * @property string $ice_contact
 * @property string $ice_phone
 * @property string $club_name
 * @property integer $club_id
 * @property integer $mem_type
 * @property integer $primary
 * @property string $incep
 * @property string $expires
 * @property string $qrcode
 * @property string $wt_date
 * @property string $wt_instru
 * @property string $remarks
 * @property string $status
 * @property string $soft_delete
 * @property string $created_at
 * @property string $updated_at
 */
class BadgesDatabase extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
	public $club_name;
	public $pagesize;
	
    public static function tableName() {
        return 'badges';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['prefix', 'first_name', 'last_name', 'address', 'city', 'state', 'zip', 'club_id', 'mem_type', 'incep', 'expires', 'wt_date', 'remarks'], 'required'],
            [['address', 'gender', 'qrcode', 'remarks', 'status', 'soft_delete'], 'string'],
            [['badge_number', 'club_id', 'mem_type', 'primary','email_vrfy'], 'integer'],
            [['incep', 'expires', 'wt_date', 'created_at', 'updated_at'], 'safe'],
            [['prefix', 'suffix'], 'string', 'max' => 15],
            [['first_name'], 'string', 'max' => 20],
            [['last_name', 'city', 'phone', 'phone_op', 'ice_phone'], 'string', 'max' => 25],
            [['state', 'zip'], 'string', 'max' => 10],
            [['yob'], 'integer', 'min' =>1900,'max' => 9999],
            [['email', 'ice_contact'], 'string', 'max' => 40],
            [['club_name'], 'string', 'max' => 52],
            [['wt_instru'], 'string', 'max' => 255],
            [['badge_number'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'badge_number' => 'Badge Number',
            'prefix' => 'Prefix',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'suffix' => 'Suffix',
            'address' => 'Address',
            'city' => 'City',
            'state' => 'State',
            'zip' => 'Zip',
            'gender' => 'Gender',
            'yob' => 'Yob',
            'email' => 'Email',
            'email_vrfy' => 'Verified',
            'phone' => 'Phone',
            'phone_op' => 'Phone Op',
            'ice_contact' => 'Ice Contact',
            'ice_phone' => 'Ice Phone',
            'club_name' => 'Club Name',
            'club_id' => 'Club Name',
            'mem_type' => 'Mem Type',
            'primary' => 'Primary',
            'incep' => 'Incep',
            'expires' => 'Expires',
            'qrcode' => 'Qrcode',
            'wt_date' => 'WT Date',
            'wt_instru' => 'WT Instru',
            'remarks' => 'Remarks',
            'status' => 'Status',
            'soft_delete' => 'Soft Delete',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
