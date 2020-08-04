<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "clubs".
 *
 * @property integer $club_id
 * @property string $club_name
 * @property string $short_name
 * @property string $status
 */
class Clubs extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
    public $file;

    public static function tableName() {
        return 'clubs';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['club_name', 'short_name'], 'required'],
            [['club_id', 'status','is_club'],'number'],
            [['avoid','club_name', 'poc_email'], 'string', 'max' => 255],
            [['short_name'], 'string', 'max' => 20],
            [['poc_email'],'safe'],
            ['poc_email', 'email'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
          //  'id' => 'Club ID',
            'club_id' => 'Club ID',
            'club_name' => 'Club Name',
            'short_name' => 'Short Name',
			'is_club'=>'Is a Club',
            'status' => 'Status',
            'poc_email' => 'POC Email',
        ];
    }

    public function getClubList($use_short=false,$restrict=false,$allow_members=false) {
		if($use_short) {$field='short_name';} else {$field='club_name';}
		
		if($allow_members) { $where = ['status'=>'0','is_club'=>'1']; } else { $where = ['status'=>'0']; }		
		$clubArray = Clubs::find()
			->where($where )
			->orderBy(['is_club'=> SORT_DESC,$field => SORT_ASC ])
			->all();
		
		if($restrict) {			
			$myClubs='';
			foreach ($clubArray as $key=>$value) {
				if(in_array($value->club_id,json_decode($restrict))) {
					$myClubs.= '"'.$value->club_id.'":"'.$value->$field.'",';
				}
			}
			return json_decode('{'.rtrim($myClubs,',').'}');
		}
		else {
			return ArrayHelper::map($clubArray,'club_id',$field);
		}
    }
	
   public function getAvoid($restrict=false) {
		$clubArray = Clubs::find()
			->where(['status'=>'0'])
			->orderBy(['is_club'=> SORT_DESC ])
			->all();
		
		if($restrict) {			
			$myClubs='';
			foreach ($clubArray as $key=>$value) {
				if(in_array($value->club_id,json_decode($restrict))) {
					$myClubs.= '"'.$value->club_id.'":"'.$value->avoid.'",';
				}
			}
			return json_decode('{'.rtrim($myClubs,',').'}');
		}
		else {
			return ArrayHelper::map($clubArray,'club_id','avoid');
		}
    }

	public function getMyClubs($badge_number) {
		$command = Yii::$app->db->createCommand("SELECT club_id FROM badge_to_club WHERE badge_number=".$badge_number);
		$myClubs = $command->queryAll();
		$myClubsId='';
		foreach($myClubs as $club){
			$myClubsId .= $club['club_id'].",";
		}
		return explode(",",rtrim($myClubsId, ','));
	}

	public function getMyClubsNames($badge_number,$use_short=false) {
		$sql = "SELECT short_name, club_name from clubs JOIN badge_to_club on (clubs.club_id = badge_to_club.club_id) ".
				"WHERE badge_number=".$badge_number;
		$command = Yii::$app->db->createCommand($sql);
		$myClubN = $command->queryAll();
		$myClubsNames='';
		foreach($myClubN as $club){
			if($use_short){
				$myClubsNames .= $club['short_name'].' <img src="/images/note.png" title="'.$club['club_name'].'" style="width:18px" />, ';
			} else {
				$myClubsNames .= $club['club_name'].", ";
			}
		}
		return rtrim($myClubsNames, ', ');
	}

	public function getClubRoster($club_id) {
		$sql = "SELECT *  FROM badges WHERE badge_number IN (SELECT badge_number FROM badge_to_club WHERE club_id = ".$club_id.")";
		$command = Yii::$app->db->createCommand($sql);
		return $command->queryAll();
	}
}
