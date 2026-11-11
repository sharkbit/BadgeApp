<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use backend\models\BadgeToClubs;

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
            [['club_id', 'status','is_club','allow_members'],'number'],
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
			'allow_members' => 'Allow Members',
            'club_id' => 'Club ID',
            'club_name' => 'Club Name',
            'short_name' => 'Short Name',
			'is_club'=>'Club Type',
            'status' => 'Status',
            'poc_email' => 'POC Email',
        ];
    }

	public function getBadgeToClubs() {
		return $this->hasMany(BadgeToClubs::class, ['club_id' => 'club_id']);
	}

    public function getClubList($use_short=false,$restrict=false,$allow_members=false,$add_CIO=false,$no_really_restrict=false) {
		if($use_short) {$field='short_name';} else {$field='club_name';}

		if($allow_members==2) { $where = ['status'=>'0','allow_members'=>'1']; }
		elseif($allow_members) { $where = ['status'=>'0','is_club'=>'1']; } else { $where = ['status'=>'0']; }
		$clubArray = Clubs::find()
			->where( $where )
			->orderBy(['is_club'=> SORT_DESC,$field => SORT_ASC ])
			->all();

		$hasFullClubListPermission = Yii::$app->controller->hasPermission('badges/all')
			|| Yii::$app->controller->hasPermission('calendar/shoot');
		$mustRestrict = !$hasFullClubListPermission
			&& ($no_really_restrict || !empty($restrict));
		if ($mustRestrict) {
			if (is_string($restrict)) {
				$restrict = json_decode($restrict, true);
			} elseif (is_object($restrict)) {
				$restrict = (array)$restrict;
			}
			if (!is_array($restrict)) {
				$restrict = [];
			}
			$restrict = array_map('strval', $restrict);
			$ClubList = ArrayHelper::map(
				array_filter($clubArray, static function ($club) use ($restrict) {
					return in_array((string)$club->club_id, $restrict, true);
				}),
				'club_id',
				$field
			);
		} else {
			$ClubList = ArrayHelper::map($clubArray,'club_id',$field);
		}

		if (($add_CIO) && (in_array(8,$_SESSION['privilege']))) {
			$usr = (new \backend\models\User)->find()->where(['badge_number'=>$_SESSION['badge_number']])->one();
			if($usr) {
				 foreach (json_decode($usr->clubs) as $club_id) {
					$notFound=true;
					foreach ($ClubList as $key => $value) {
						if ($club_id==$key) { $notFound=false; }
					}
					if ($notFound) {
						$addClub=Clubs::find()->where(['club_id'=>$club_id])->one();
						(object) $ClubList = (array)$ClubList + (array)[$addClub->club_id=>$addClub->$field];
					}
				 }
			 }
		}

		return $ClubList;
    }

	public static function getClubNames($club_ids) {
		if (is_string($club_ids)) {
			$decodedClubIds = json_decode($club_ids, true);
			$club_ids = json_last_error() === JSON_ERROR_NONE
				? $decodedClubIds
				: explode(',', $club_ids);
		}

		if (!is_array($club_ids)) {
			$club_ids = [$club_ids];
		}

		$club_ids = array_values(array_filter($club_ids, static function ($clubId) {
			return $clubId !== null && $clubId !== '';
		}));
		if (!$club_ids) {
			return '';
		}

		$clubNames = static::find()
			->select('club_name')
			->where(['in', 'club_id', $club_ids])
			->orderBy(['club_name' => SORT_ASC])
			->column();

		return implode(', ', $clubNames);
	}

	public function getClubsWithMembers() {
		$dropdownData = Clubs::find()
			->innerJoinWith('badgeToClubs')
			->select(['clubs.club_id', 'clubs.club_name'])
			->orderBy(['clubs.club_name' => SORT_ASC])
			->asArray()
			->all();

		return ArrayHelper::map($dropdownData, 'club_id', 'club_name');
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
		$clubIds = (new Query())
			->select('club_id')
			->from('badge_to_club')
			->where(['badge_number' => $badge_number])
			->column();

		return array_map('strval', $clubIds);
	}

	public function getMyClubsNames($badge_number,$use_short=false) {
		$myClubN = (new Query())
			->select(['clubs.short_name', 'clubs.club_name'])
			->from('clubs')
			->innerJoin('badge_to_club', 'clubs.club_id = badge_to_club.club_id')
			->where(['badge_to_club.badge_number' => $badge_number])
			->all();
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
		$badgeNumbers = (new Query())
			->select('badge_number')
			->from('badge_to_club')
			->where(['club_id' => $club_id]);

		return Badges::find()
			->where(['in', 'badge_number', $badgeNumbers])
			->asArray()
			->all();
	}

	public static function saveClub($badge_number, $clubs, $delOld=true) {
		$connection = Yii::$app->getDb();

		$clubIds = is_array($clubs) ? $clubs : [$clubs];
		$rows = array_map(static function ($clubId) use ($badge_number) {
			return [$badge_number, $clubId];
		}, $clubIds);

		return $connection->transaction(function ($db) use ($badge_number, $delOld, $rows) {
			if ($delOld) {
				$db->createCommand()
					->delete('badge_to_club', ['badge_number' => $badge_number])
					->execute();
			}

			if (!$rows) {
				return 0;
			}

			return $db->createCommand()
				->batchInsert('badge_to_club', ['badge_number', 'club_id'], $rows)
				->execute();
		});
	}

}
