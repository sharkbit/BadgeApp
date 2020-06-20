<?php

namespace backend\models;
use backend\models\Legalgroups;
use yii\helpers\ArrayHelper;

use Yii;
/**
 * This is the model class for table "AGC.event_status".

 */
class Legeltogroup extends \yii\db\ActiveRecord {
    public static function tableName() {
        return 'associat_agcnew.contact_groups';
    }

	public function rules() {
		return [	[['group_id','contact_id'], 'integer'],	];
	}
}

class Legelemail extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
	
    public static function tableName() {
        return 'associat_agcnew.contacts';
    }

    /**
     * @inheritdoc
     */
	 
	public $groups;
	 
    public function rules() {
        return [
			[['first_name','last_name','email'], 'required'],
			[['date_created','date_modified','groups'], 'safe'],
			[['is_active','display_order'], 'integer'],
			[['first_name','last_name','middle_name','email','title','office','committee','district'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'first_name' => 'First Name',
			'is_active'=>'Active',
        ];
    }

	public function getMyGroups($contact_id,$use_short=false) {
		$sql = "SELECT g.group_id gm,`name` FROM associat_agcnew.groups g JOIN associat_agcnew.contact_groups c on (g.group_id = c.group_id) ".
				"WHERE contact_id=".$contact_id;
		$command = Yii::$app->db->createCommand($sql);
		$myGroupN = $command->queryAll();
		$myGroupNames='';
		foreach($myGroupN as $group){
			$myGroupNames .= $group['gm'].", ";
		}
		return explode(',',rtrim($myGroupNames, ', '));
	}

	public function getGroupList() {
		$groups = Legalgroups::find()->where(['is_active'=>'1'])->all(); //->orderby('display_order');
		return ArrayHelper::map($groups,'group_id','name');
	}

	public function getGroups($contact_id) {
		$groups = Legeltogroup::find()->where(['contact_id'=>$contact_id])->all(); //->orderby('display_order');
		return $groups;
	}
}

