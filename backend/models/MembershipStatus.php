<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "bn_to_cl".
 */
class MembershipStatus extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
    //public $file;

    public static function tableName() {
        return 'account_status';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['act_id','act_login','act_prefill','act_order','act_active','act_renew'],'number'],
            [['act_color'], 'string', 'max' => 20],
			[['act_name'], 'string', 'max' => 45],
            [['act_short'], 'string', 'max' => 3],
			[['act_desc'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
			'act_id' => 'ID',
			'act_login' => 'Can Login',
			'act_prefill' => 'Prefill Data',
            'act_active' => 'is Active',
			'act_color' => 'Color',
			'act_name' => 'Status Name',
			'act_short' => 'Short Name',
			'act_desc' => 'Description',
			'act_order'=>'Order',
			'act_renew'=>'can Renew',
        ];
    }

	static public function getCanLogin() {
		$canLogin = (New MembershipStatus)->find()->where(['act_login' => 1 ])->all();
		return ArrayHelper::getColumn($canLogin,'act_short');
	}

	static public function getCanRenew($aStatus) {
		$can_Renew = (New MembershipStatus)->find()->where(['act_short' => $aStatus ])->one();
		if (($can_Renew) && ($can_Renew->act_renew=1)) {
			return true;
		} else { return false; }
	}

	static public function GetMemStatus($eStatus) {
		$memStatus = (New MembershipStatus)->find('act_name')->where(['act_short' => $eStatus ])->one();
		if ($memStatus) { return $memStatus->act_name; } else {return ' Account Status Error '; }
	}

	static public function getMemberStatus() {
		$MemberStatus = (New MembershipStatus)->find()->where(['act_active' => 1 ])->orderby(['act_order' => SORT_ASC])->all();
		return ArrayHelper::map($MemberStatus,'act_short','act_name');
	}

	static public function getPrefill() {
		$preFill = (New MembershipStatus)->find()->where(['act_prefill' => 1 ])->all();
		$mydata= json_encode( ArrayHelper::getColumn($preFill,'act_short') );
		//yii::$app->controller->createLog(true, 'trex--mydata', var_export($mydata,true));
		return $mydata;
	}
}
