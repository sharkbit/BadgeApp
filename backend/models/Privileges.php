<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "privilege".
 *
 * @property integer $id
 */
class Privileges extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'user_privileges';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['privilege', 'priv_sort','timeout'], 'required'],
            [['privilege'], 'string'],
			[['priv_sort'], 'integer'],
            [['timeout'], 'integer', 'min'=>2, 'max' => 60],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
			'privilege' => 'Privilege Title',
            'priv_sort' => 'Sort Order',
            'timeout' => 'Time-out'
        ];
    }
}
