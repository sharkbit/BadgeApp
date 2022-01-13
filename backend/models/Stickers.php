<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "sticker".
 */

class Stickers extends \yii\db\ActiveRecord{
	public $pagesize;
	public $start;
	public $end;
	public $to;
	public $yr;
	public $stkrs;

    public static function tableName() {
        return 'sticker';
    }

    public function rules() {
        return [
           [['status','sticker','updated','to','stkrs'], 'safe'],
           [['s_id','holder'], 'number'],
		   [['end','start','yr'],'integer'],
       ];
    }

    public function attributeLabels() {
        return [
			's_id' => 'Id',
			'stkrs'=>'Stickers (Numbers Only, Not Year)',
			'yr'=>'Year',
       ];
    }
}