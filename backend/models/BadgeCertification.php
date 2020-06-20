<?php

namespace backend\models;

use Yii;
use backend\models\FeesStructure;
use backend\models\Badges;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "badge_certification".
 *
 * @property integer $id
 * @property integer $badge_number
 * @property string $created_at
 * @property string $updated_at
 * @property string $stikker
 * @property integer $certification_type
 * @property string $status
 */
class BadgeCertification extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */

    public $cert_amount_due;
	public $cert_payment_type;
	public $cc_num;
	public $cc_cvc;
	public $cc_exp_mo;
	public $cc_exp_yr;
	public $proc_date;

    public static function tableName()
    {
        return 'badge_certification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['badge_number', 'created_at', 'updated_at', 'sticker', 'certification_type', 'status'], 'required'],
            [['certification_type','cc_cvc','cc_exp_yr','cc_exp_mo'], 'integer'],
            [['created_at', 'updated_at','proc_date','is_migrated','cert_amount_due','cert_payment_type'], 'safe'],
            [['status','cc_num','cc_x_id'], 'string'],
            [['sticker'], 'string', 'max' => 255],
            //[['sticker'], 'unique',],
            //[['sticker'], 'unique', 'targetClass' => 'backend\models\Badges', 'targetAttribute'=> ['sticker']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'badge_number' => 'Badge Number',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'stikker' => 'Stikker',
            'certification_type' => 'Certification Type',
            'status' => 'Status',
			'cert_amount_due' => 'Amount Due',
			'cert_payment_type' => 'Payment Type',
			'cc_num'=>'Card Number',
			'cc_cvc'=>'CVC',
			'cc_exp_yr'=>'Exp Year',
			'cc_exp_mo'=>'Exp Month',
        ];
    }
    public function getcertificationList() {
        $feeStructure = FeesStructure::find()
            ->where(['type'=>'certification', 'status'=>'0'])
            ->all();
        return ArrayHelper::map($feeStructure,'id','label');

    }

    public function getCertificationDetails() {
       return $this->hasOne(FeesStructure::className(),['id'=>'certification_type']); 
    }

    public function generateSticker() {

        return 'ST-'.$this->getNowDigit();
    }

    public function getNowDigit() {
        date_default_timezone_set(yii::$app->params['timeZone']);
        $dateTime = date('ymdHis');
        return $dateTime;
    }
    public function validateSticker($sticker) {
        $badgesResult = Badges::find()
            ->where(['sticker'=>$sticker])
            ->all();
        if(empty($badgesResult)) {
           $badgeCertification = BadgeCertification::find()
             ->where(['sticker'=>$sticker])
            ->all();
            if(empty($badgeCertification)) {
                return true;
            }
            else {
                return false;
            }
        }
        else {
           return false;
        }
    }


}
