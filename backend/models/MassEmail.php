<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "Events".
 */

class MassEmail extends \yii\db\ActiveRecord{

	public $to_email;
	public $to_active;
	public $to_expired;
	public $to_single;
	public $to_users;

    public static function tableName() {
        return 'mass_email';
    }

    public function rules() {
        return [
			[['mass_subject','mass_body'], 'required'],

			[['mass_to','mass_subject','mass_body'], 'safe'],
			[['id','mass_lastbadge','mass_created_by','mass_updated_by','mass_running'], 'number'],
			//[['type'], 'string', 'max' => 25],
			['mass_reply_to',  'email'],
			[['mass_to_users','mass_start','mass_finished','mass_created','mass_updated','mass_runtime','mass_reply_name'],'safe']
       ];
    }

    public function attributeLabels() {
        return [
			'to_active'=> 'Active Members',
			'to_expired' => 'Expired Members',
			'to_single' => 'Specific Address(s)',
			'to_users' => ' To Users',
			'mass_to' => 'To',
			'mass_to_users'=>'Users:',
			'mass_reply_to' => 'Reply To',
			'mass_subject' => 'Subject',
			'mass_body' => 'HTML',
			'mass_lastbadge' => 'Last Processed',
			'mass_created' => 'Created At',
			'mass_created_by' => 'Created By',
			'mass_updated_by' => 'Updated By',
			'mass_updated' => 'Updated At',
			'mass_running' => 'Is Running',
			'mass_start' => ' Started at',
			'mass_finished' => 'Finished'
       ];
    }

}
