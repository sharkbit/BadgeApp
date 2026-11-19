<?php

namespace backend\models;

use Yii;
use yii\db\Query;
/**
 * This is the model class for table "Officers".
 *
*/
class Officers extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
	public $pagesize;

    public static function tableName() {
        return 'view_officers';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
			[['badge_number','club','role'], 'required'],
			[['badge_number','club','email_vrfy','role','timeout'], 'integer'],
			[['club_name','email','privs','role_name','short_name'], 'safe'],
		];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'badge_number' => 'Badge Number',
			'club_name' => 'Club',
			'email_vrfy' => 'Email Verified',
			'role_name' => 'Role',
			'short_name' => 'Club Short Name'			
        ];
    }

	public static function getOfficerPrivs() {
		if (isset($_SESSION['O_Privs']) && is_string($_SESSION['O_Privs'])) {
			$cachedPermissions = json_decode($_SESSION['O_Privs'], true);
			if (is_array($cachedPermissions) && array_values($cachedPermissions) === $cachedPermissions) {
				return $_SESSION['O_Privs'];
			}
		}

		$permissions = [];
		$badgeNumber = $_SESSION['badge_number'] ?? null;
		if ($badgeNumber !== null && (int)$badgeNumber > 0) {
			$officers = self::find()
				->select('privs')
				->where(['badge_number' => (int)$badgeNumber])
				->all();

			foreach ($officers as $officer) {
				if ($officer->privs === null || trim((string)$officer->privs) === '') {
					continue;
				}

				$rolePermissions = json_decode((string)$officer->privs, true);
				if (!is_array($rolePermissions) || array_values($rolePermissions) !== $rolePermissions) {
					throw new \UnexpectedValueException('Officer privileges must be stored as a JSON array.');
				}

				foreach ($rolePermissions as $permission) {
					if (!is_string($permission)) {
						throw new \UnexpectedValueException('Officer privileges must contain permission strings.');
					}
					$permissions[] = $permission;
				}
			}
		}

		$permissions = array_values(array_unique($permissions));
		sort($permissions, SORT_STRING);
		$privilegesJson = json_encode($permissions, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
		$_SESSION['O_Privs'] = $privilegesJson;

		return $privilegesJson;
	}

	public static function getOfficerTimeout() {
		if (isset($_SESSION['O_Timeout']) && is_numeric($_SESSION['O_Timeout'])) {
			return (int)$_SESSION['O_Timeout'];
		}

		$timeouts = [];
		$badgeNumber = $_SESSION['badge_number'] ?? null;
		if ($badgeNumber !== null && (int)$badgeNumber > 0) {
			$timeouts = self::find()
				->select('timeout')
				->where(['badge_number' => (int)$badgeNumber])
				->column();
			$timeouts = array_map('intval', array_filter($timeouts, 'is_numeric'));
		}

		$timeout = $timeouts ? max($timeouts) : 0;
		$_SESSION['O_Timeout'] = $timeout;

		return $timeout;
	}

	public static function getOfficerClubs() {
		if (isset($_SESSION['O_Clubs']) && is_array($_SESSION['O_Clubs'])) {
			return $_SESSION['O_Clubs'];
		}

		$clubs = [];
		$badgeNumber = $_SESSION['badge_number'] ?? null;
		if ($badgeNumber !== null && (int)$badgeNumber > 0) {
			$clubs = (new Query())
				->select('club')
				->from('view_officers')
				->where(['badge_number' => (int)$badgeNumber])
				->distinct()
				->column();
			$clubs = array_map('intval', $clubs);
		}

		$_SESSION['O_Clubs'] = $clubs;

		return $clubs;
	}
	
	public static function getOfficerRoles($badge_number) {
		$query = (new Query())
			->select(['view_officers.role_name'])
			->from('view_officers')
			->innerJoin('roles', 'roles.role_id = view_officers.role')
			->where(['view_officers.badge_number' => (int)$badge_number])
			->orderBy(['roles.disp_order' => SORT_ASC, 'roles.role_id' => SORT_ASC])
			->limit(1);
		$command = $query->createCommand();

		//Yii::$app->controller->createLog(true,'trex-b-m-O_or_sql',var_export($command->rawSql, true) );

		return $command->queryOne();
	}
}

