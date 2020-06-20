<?php

namespace backend\models;

use Yii;
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
class agcClubs extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
	public $migrated;
	
    public static function tableName() {
        return 'associat_agcnew.clubs';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
			[['name','nick_name'], 'required'],
			//[[''], 'safe'],
			[['active','is_cio','display_order','club_id','display_in_administration','display_in_badges_administration'], 'integer'],
			[['name','nick_name','ca','contact_first_name','contact_last_name','contact_phone'],'string']
		];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'name' => 'Club Name',
			'nick_name'=> 'Short Name',
			'active'=>'Active',
        ];
    }
}