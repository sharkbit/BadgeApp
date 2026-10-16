<?php

namespace backend\models;

/**
 * This is the model class for view "view_banned_peeps".
 *
 * @property string $person_id
 * @property string $first_name
 * @property string $last_name
 * @property string $ban_status
 */
class ViewBannedPeeps extends \yii\db\ActiveRecord {

    public $badge_number;

    public static function tableName() {
        return 'view_banned_peeps';
    }

    public static function primaryKey() {
        return ['person_id'];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['person_id', 'first_name', 'last_name', 'ban_status'], 'safe'],
        ];
    }

    public static function CheckIfBanned($firstName, $lastName) {
        $normalizedFirstName = self::normalizePersonName($firstName);
        $normalizedLastName = self::normalizePersonName($lastName);
        if ($normalizedFirstName === '' || $normalizedLastName === '') {
            return false;
        }

        $bannedPeople = self::find()
            ->select(['first_name', 'last_name'])
            ->asArray()
            ->all();

        foreach ($bannedPeople as $bannedPerson) {
            $bannedFirstName = self::normalizePersonName($bannedPerson['first_name']);
            $bannedLastName = self::normalizePersonName($bannedPerson['last_name']);
            if (
                $bannedFirstName !== ''
                && $bannedLastName !== ''
                && self::namesLooselyMatch($normalizedFirstName, $bannedFirstName)
                && self::namesLooselyMatch($normalizedLastName, $bannedLastName)
            ) {
                return true;
            }
        }

        return false;
    }

    private static function normalizePersonName($name) {
        $name = trim((string)$name);
        if (function_exists('iconv')) {
            $transliteratedName = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);
            if ($transliteratedName !== false) {
                $name = $transliteratedName;
            }
        }

        return strtolower(preg_replace('/[^a-z0-9]/i', '', $name));
    }

    private static function namesLooselyMatch($submittedName, $bannedName) {
        return strpos($submittedName, $bannedName) !== false
            || strpos($bannedName, $submittedName) !== false;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'person_id' => 'Person ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'ban_status' => 'Ban Status',
        ];
    }
}
