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
            //[['privilege', 'priv_sort','timeout'], 'required'],
            [['item','sku','type','img'], 'string'],
			[['item_id','paren','stock','active','new_badge'], 'integer'],
			[['price'], 'number'],
            //[['timeout'], 'integer', 'min'=>2, 'max' => 60],
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

	public function relations() {
		//return array( 'Cat' => array(self::hasone, 'item', 'item_id') );
		return $this->hasOne(self::className(), ['item_id' => 'paren'])
            ->from(self::tableName() . ' cat');
	}

/*	public function getCat() {
		// return $this->hasone(StoreItems::className(), ['paren' => 'item_id']);
		$equery= StoreItems::model()->find("paren=$this->item_id"); 
		$cnm=$equery['item'];
		return $cnm;
    }*/
}
