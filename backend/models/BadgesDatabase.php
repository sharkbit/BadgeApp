<?php

namespace backend\models;

use backend\models\BadgeToYear;
use backend\models\ClubView;
use backend\models\MembershipType;
use Yii;

/**
 * This is the model class for table "badges".
 */
class BadgesDatabase extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
	public $club_id;
	public $pagesize;
	public $remarks_temp;
	
    public static function tableName() {
        return 'badges';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['prefix', 'first_name', 'last_name', 'address', 'city', 'state', 'zip', 'club_id', 'mem_type', 'incep', 'wt_date', 'remarks'], 'required'],
            [['address', 'gender', 'qrcode', 'remarks', 'status', 'soft_delete'], 'string'],
            [['badge_number', 'mem_type', 'primary','email_vrfy'], 'integer'],
            [['incep', 'club_id', 'expires', 'wt_date', 'created_at', 'updated_at'], 'safe'],
            [['prefix', 'suffix'], 'string', 'max' => 15],
            [['first_name'], 'string', 'max' => 20],
            [['last_name', 'city', 'phone', 'phone_op', 'ice_phone'], 'string', 'max' => 25],
            [['state', 'zip'], 'string', 'max' => 10],
            [['yob'], 'integer', 'min' =>1900,'max' => 9999],
            [['email', 'ice_contact'], 'string', 'max' => 40],
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
            'yob' => 'Yob',
            'email_vrfy' => 'Verified',
            'phone_op' => 'Phone Op',
            'ice_contact' => 'Ice Contact',
            'ice_phone' => 'Ice Phone',
            'club_id' => 'Club(s)',
            'mem_type' => 'Mem Type',
            'qrcode' => 'QR Code',
            'wt_date' => 'WT Date',
            'wt_instru' => 'WT Instru',
            'soft_delete' => 'Soft Delete',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

	public function getBadgeToYear() {
		return $this->hasOne(BadgeToYear::className(), ['badge_number' => 'badge_number']);
	}

	public function getClubView() {
		return $this->hasMany(ClubView::className(), ['badge_number' => 'badge_number']);
	}

	public function getMembershipType() {
		return $this->hasOne(MembershipType::className(),['id'=>'mem_type']);
	}
}
