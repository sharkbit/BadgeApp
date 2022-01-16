<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "sticker".
 */

class Stickers extends \yii\db\ActiveRecord{
	public $pagesize;
	public $start;
	public $end;
	public $to;
	public $yr;
	public $yr_mv;
	public $stkrs;

    public static function tableName() {
        return 'sticker';
    }

    public function rules() {
        return [
           [['status','sticker','updated','to','stkrs'], 'safe'],
           [['s_id','holder'], 'number'],
		   [['end','start','yr','yr_mv'],'integer'],
       ];
    }

    public function attributeLabels() {
        return [
			's_id' => 'Id',
			'stkrs'=>'Stickers (Numbers Only)',
			'yr'=>'Year',
			'yr_mv'=>'Year',
       ];
    }

	public function getList() {
		if (array_intersect([1,2,10], json_decode(yii::$app->user->identity->privilege))) { $whr='adm';}
		elseif (array_intersect([3,6], json_decode(yii::$app->user->identity->privilege))) { $whr='rso';}
		else {$whr='x';}
		$sticker = (new Stickers)->find()->where(['status'=>$whr])->limit(15)->orderBy('sticker')->all();
		if($sticker){
			$use = ArrayHelper::map($sticker,'sticker','sticker');
			$tmpstkr = substr(array_values($use)[0],0,5).'zzzz';
			$use = array_merge($use,[$tmpstkr=>$tmpstkr]);
			return $use;
		} else {
			return ['fix'=>'No Stickers. See Admin'];
		}
		
	}

	public function listStickerStatus($stat=false) {
		if($stat) {
			switch ($stat){
				case 'adm': return 'Admin';
				case 'rso': return 'RSO';
				case 'isu': return 'Issued';
				case 'mis': return 'Missing?';
				case 'ret': return 'Retired';
				case 'wha': return '????';
				default: return $stat;
			}
		}
		else {
			return ['adm'=>'Admin','rso'=>'RSO','isu'=>'Issued','mis'=>'Missing?','ret'=>'Retired','wha'=>'???'];
		}
	}
}
