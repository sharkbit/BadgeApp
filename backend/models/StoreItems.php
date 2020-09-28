<?php

namespace backend\models;

use yii\helpers\ArrayHelper;
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
			'paren' => 'Group',
            'price' => 'Price',
			'stock' => 'Stock',
			'img' => 'img',
			'active' => 'Active',
			'new_badge' => 'Badge List'
        ];
    }

	public function getTypes($limit=false) {
		$myTypes = $this::find()->groupBy(['type'])->all();
		$aryTypes=[];
		foreach($myTypes as $item){
			if(($limit) && ($item->type=='Category')) { continue; }
			$aryTypes=array_merge($aryTypes,[$item->type=>$item->type]);
		}
		return $aryTypes;
	}

	public function relations() {
		return $this->hasOne(self::className(), ['item_id' => 'paren'])
            ->from(self::tableName() . ' cat');
	}

	public function getParen($item_id=null) {
		if (isset($item_id) && ($item_id >0)) {
			$storeitem = $this::find()->where(['item_id'=>$item_id])->one();
			return $storeitem->item;
		} else { return "-";}
	}

	public function getGroups($item_id=null) {
		$storeitem = $this::find()->where(['type'=>'Category'])->orderBy('item')->all();
		return ArrayHelper::map($storeitem, 'item_id', 'item');
	}
}
