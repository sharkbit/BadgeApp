<?php

namespace backend\models;

use backend\models\Params;
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
		$limit=15;
		if (array_intersect([1,2], json_decode(yii::$app->user->identity->privilege))) {
			$whr="status='adm' OR status='cas'"; $limit=150; }
		elseif ((in_array(3, json_decode(yii::$app->user->identity->privilege))) && (in_array(10, json_decode(yii::$app->user->identity->privilege)))) {
			$whr="status='rso' OR status='cas'"; $limit=150;}
		elseif (array_intersect([10], json_decode(yii::$app->user->identity->privilege))) {
			$whr=['status'=>'cas']; }
		elseif (array_intersect([3,6], json_decode(yii::$app->user->identity->privilege))) {
			$whr=['status'=>'rso']; }
		else {$whr='1=2';}
		$sticker = (new Stickers)->find()->where($whr)->limit($limit)->orderBy('sticker')->all();
		if($sticker){
			$use = ArrayHelper::map($sticker,'sticker','sticker');

			$confParams  = Params::findOne('1');
			$DateChk = date("Y-".$confParams['sell_date'], strtotime(yii::$app->controller->getNowTime()));
			$nowDate = date('Y-m-d',strtotime(yii::$app->controller->getNowTime()));

			$tmpstkr = date('Y', strtotime($nowDate)).'-zzzz';
			$use = array_merge($use,[$tmpstkr=>$tmpstkr]);

			if ($DateChk <= $nowDate) {
				$tmpstkr = date('Y', strtotime("+1 years",strtotime($nowDate))).'-zzzz';
				$use = array_merge($use,[$tmpstkr=>$tmpstkr]);
			}
			return $use;
		} else {
			return ['fix'=>'No Stickers. See Admin'];
		}

	}

	public function listStickerStatus($stat=false) {
		if($stat) {
			switch ($stat){
				case 'adm': return 'Admin';
				case 'cas': return 'Casher';
				case 'rso': return 'RSO';
				case 'isu': return 'Issued';
				case 'mis': return 'Missing';
				case 'ret': return 'Retired';
				default: return $stat;
			}
		}
		else {
			return ['adm'=>'Admin','cas'=>'Casher','rso'=>'RSO','isu'=>'Issued','mis'=>'Missing?','ret'=>'Retired'];
		}
	}
}
