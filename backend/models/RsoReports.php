<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;
use backend\models\User;

/**
 * This is the model class for table "work_credits".
 *
*/
class RsoReports extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
	public $pagesize;

    public static function tableName() {
        return 'rso_reports';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
			[['date_open','mics','rso','wb_color','wb_trap_cases'], 'required'],
			[['closed','id','par_50','par_100','par_200','par_steel','par_nm_hq','par_m_hq','par_trap','par_arch','par_pel','par_spr','par_cio_stu','par_act','wb_trap_cases'], 'integer'],
			[['cash_bos','cash_eos'],'number'],
			[['wb_color','closing','mics','notes','remarks','rso','shift','shift_anom','stickers'], 'safe'],
			[['date_open','date_close'],'safe'],
		];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
			'date_close'=>'Date Closed',
			'date_open'=>'Date Open',
			'rso' => "RSO's",
			'shift_anom'=> 'Shift Anomalies',
			'par_50'=>'50 yrd',
			'par_100'=>'100 yrd',
			'par_200'=>'200 yrd',
			'par_steel'=>'Steel',
			'par_nm_hq'=>'N/M Hunter Qual',
			'par_m_hq'=>'M Hunter Qual',
			'par_trap'=>'Trap',
			'par_arch'=>'Archery',
			'par_pel'=>'Pellet',
			'par_spr'=>'SG Ptrn Rnr',
			'par_cio_stu'=>'CIO Students',
			'par_act'=>'Action Rng',
			'cash_bos'=>'Cash BOS',
			'cash_eos'=>'Cash EOS',
			'closing'=>'Closing Notes',
			'mics'=>'MICs Status',
			'wb_trap_cases'=>' Wobbel Trap Cases',
			'wb_color'=> 'Wristband Color',
        ];
    }

	public function listRSOs() {
		$rsoList=[];
		$all_users = User::find()->orderBy(['full_name'=>'DESC'])->all();
		foreach($all_users as $usr) {
			if (array_intersect([3,6],json_decode($usr->privilege))) {
				array_push( $rsoList, ['id'=>$usr->badge_number, 'name'=>$usr->full_name ] );
			}
		}
		return ArrayHelper::map($rsoList, 'id', 'name');
	}
	
	public function getStickerCount($who='rso') {
		$stkr = (new \backend\models\Stickers)->find()->where(['status'=>$who])->all();
		$cnt=0;
		$list = [];
		foreach($stkr as $lick) {
			$list = $this->reduce_ranges($list,(int)substr($lick->sticker,-4));
			$cnt++;
		}
		$replace=['"','[',']'];
		return $cnt.': '.str_replace($replace,'',json_encode($list));
	}

	public function reduce_ranges($reduced, $value) {
		static $in_range, $previous;
		// Reset
		if ($in_range === NULL) {
			$in_range = FALSE;
			$previous = NULL;
		}
		// In a range
		if (isset($previous) && $value == $previous + 1) {
			if (!$in_range) {
				$in_range = TRUE;
			}
			// Update the range
			end($reduced);
			$key = key($reduced);
			$start = strtok($reduced[$key], "-");
			$reduced[$key] = $start."-".$value;
		// Not in a range
		} else {
			$reduced[] = $value;
			$in_range = FALSE;
		}
		$previous = $value;
		return $reduced;
	}
}

