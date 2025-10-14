<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "violation_status".
 *
 * @property int $id
 * @property int $badge_number
 * @property int $violation_count
 * @property string $status
 * @property string|null $last_violation_date
 * @property string|null $blocked_until
 * @property bool $admin_contact_required
 * @property bool $is_guest
 * @property string $created_at
 * @property string $updated_at
 */
class ViolationStatus extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 'active';
    const STATUS_WARNING = 'warning';
    const STATUS_ESCALATED = 'escalated';
    const STATUS_BLOCKED = 'blocked';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'violation_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['badge_number', 'created_at', 'updated_at'], 'required'],
            [['badge_number', 'violation_count'], 'integer'],
            [['status'], 'string'],
            [['last_violation_date', 'blocked_until', 'created_at', 'updated_at'], 'safe'],
            [['admin_contact_required', 'is_guest'], 'boolean'],
            [['badge_number'], 'unique'],
            [['badge_number'], 'exist', 'skipOnError' => true, 'targetClass' => Badges::className(), 'targetAttribute' => ['badge_number' => 'badge_number']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'badge_number' => 'Badge Number',
            'violation_count' => 'Violation Count',
            'status' => 'Status',
            'last_violation_date' => 'Last Violation Date',
            'blocked_until' => 'Blocked Until',
            'admin_contact_required' => 'Admin Contact Required',
            'is_guest' => 'Is Guest',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Badge]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBadge()
    {
        return $this->hasOne(Badges::className(), ['badge_number' => 'badge_number']);
    }

    /**
     * Increments violation count and updates status
     * @param int $badge_number
     * @param bool $is_guest Whether this is a guest violation
     * @return bool
     */
    public static function incrementViolation($badge_number, $is_guest = false)
    {
        $status = self::findOne(['badge_number' => $badge_number]);
        if (!$status) {
            $status = new self();
            $status->badge_number = $badge_number;
            $status->created_at = date('Y-m-d H:i:s');
            $status->is_guest = $is_guest;
        }

        $status->violation_count++;
        $status->last_violation_date = date('Y-m-d H:i:s');
        $status->updated_at = date('Y-m-d H:i:s');

        // For guests, apply stricter penalties
        if ($is_guest) {
            if ($status->violation_count >= 2) { // Guests get blocked after 2 violations
                $status->status = self::STATUS_BLOCKED;
                $status->blocked_until = date('Y-m-d H:i:s', strtotime('+30 days'));
                $status->admin_contact_required = true;
            } elseif ($status->violation_count == 1) {
                $status->status = self::STATUS_WARNING;
                $status->admin_contact_required = true;
            }
        } else {
            // Regular member workflow
            if ($status->violation_count >= 3) {
                $status->status = self::STATUS_BLOCKED;
                $status->blocked_until = date('Y-m-d H:i:s', strtotime('+30 days'));
                $status->admin_contact_required = true;
            } elseif ($status->violation_count == 2) {
                $status->status = self::STATUS_ESCALATED;
                $status->admin_contact_required = true;
            } elseif ($status->violation_count == 1) {
                $status->status = self::STATUS_WARNING;
            }
        }

        return $status->save();
    }

    /**
     * Checks if a badge is blocked
     * @param int $badge_number
     * @return bool
     */
    public static function isBlocked($badge_number)
    {
        $status = self::findOne(['badge_number' => $badge_number]);
        if (!$status) {
            return false;
        }

        if ($status->status === self::STATUS_BLOCKED) {
            if ($status->blocked_until && strtotime($status->blocked_until) > time()) {
                return true;
            }
            // Block has expired, reset status
            $status->status = self::STATUS_ACTIVE;
            $status->violation_count = 0;
            $status->blocked_until = null;
            $status->admin_contact_required = false;
            $status->save();
        }

        return false;
    }
} 