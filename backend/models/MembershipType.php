<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "membership_type".
 *
 * @property integer $id
 * @property string $type
 * @property string $status
 */

class MembershipType extends \yii\db\ActiveRecord{
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'membership_type';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
           [['type', 'status'], 'required'],
           [['status'], 'string'],
           [['type'], 'string', 'max' => 25],
       ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
           'type' => 'Type',
           'status' => 'Status',
       ];
    }
}
