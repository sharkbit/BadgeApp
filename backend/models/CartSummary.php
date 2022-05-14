<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "Events".
 */

class CartSummary extends \yii\db\ActiveRecord{

	public $pagesize;
	public $date_start;
	public $date_stop;
	public $sqty;
	public $sprice;
	 
    public static function tableName() {
        return 'Cart_Summary';
    }

    public function rules() {
        return [
           [['cat','tx_type','citem','csku'], 'string'],
		   [['tx_date'],'safe'],
		   [['qty'],'intiger'],
           [['ea','cprice',], 'number'],
       ];
    }

    public function attributeLabels() {
        return [
			'cat'=>'Category',
			'citem'=>'Item',
			'cprice'=>'Price',
			'csku'=>'SKU',
			'date_start'=>'From',
			'date_stop'=>'To',
			'sprice'=>'Price',
			'sqty'=>'Qty',
			'tx_date' => 'Date',
			'tx_type' => 'Transaction Type',
       ];
    }
}
