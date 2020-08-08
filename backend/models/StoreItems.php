<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "privilege".
 *
 * @property integer $id
 */
class StoreItems extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'store_items';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['item','sku','type','img'], 'string'],
			[['item_id','paren','stock','active','new_badge'], 'integer'],
			[['price'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'item_id' => 'ID',
			'item' => 'Item',
            'sku' => 'SKU',
            'price' => 'Price',
			'stock' => 'Stock',
			'img' => 'img',
			'active' => 'Active',
			'new_badge' => 'Badge List'
        ];
    }

	public function getTypes() {
		$myTypes = $this::find()->groupBy(['type'])->all();
		$aryTypes=[];
		foreach($myTypes as $item){
			$aryTypes=array_merge($aryTypes,[$item->type=>$item->type]);
		}
		return $aryTypes;
	}

	public function relations() {
		return $this->hasOne(self::className(), ['item_id' => 'paren'])
            ->from(self::tableName() . ' cat');
	}

}
