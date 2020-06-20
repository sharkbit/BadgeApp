<?php

use backend\controllers\ViolationsController;
use kartik\daterange\DateRangePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\WorkCredits */

$this->title = 'Stats: ';// . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Violations', 'url' => ['violations/index']];
$this->params['breadcrumbs'][] = $this->title;


$sql = "select (select count(*) from violations) as tot, (select count(*) from violations where was_guest=1) as guest;";
$connection = Yii::$app->getDb();
$command = $connection->createCommand($sql);
$Result = $command->queryall();
$vio_Total=$Result[0]['tot'];
$vio_Guest=$Result[0]['guest'];

$limit = 30;

echo $this->render('_view-tab-menu').PHP_EOL ?>
<br />
<div class="violations-stats">
	<div class="row">
         <div class="col-xs-5">
            <?=html::a('<i class="fa fa-download" aria-hidden="true"></i> Export as CSV',['#'],['id'=>'customExportCsv','class'=>'btn btn-primary'])?>
        </div>
        
        <div class="col-xs-5">
             <?php $form = ActiveForm::begin([
                'id'=>'postPrintTransactionForm',
                'action' => ['/violations/stats'],
                'method' => 'get',
            ]); ?>
			
<?=  $form->field($searchModel, 'vi_date', [
		'options'=>['class'=>'drp-container form-group']
		])->widget(DateRangePicker::classname(), [
			'presetDropdown'=>true,
			'hideInput'=>true,
			'pluginOptions' => [
				'opens'=>'left',
				'locale'=>['format'=>'MM/DD/YYYY'],
			]])->label(false); ?>
		</div>
		<div class="col-xs-2"> dosnt work yet 
			<?= Html::submitButton('<i class="fa fa-search pull-right" aria-hidden="true"></i> Search', ['class' => 'btn btn-secondary']) ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

	<div class="row">
	<div class="col-xs-12">

	<!-- <h1 style="text-align:center;"> Need more DATA!</h1> -->
	<h2 style="text-align:center;"><?=$vio_Total?> Violations / <?=($vio_Total-$vio_Guest)?> Member, <?=$vio_Guest?> Guest Violations (Top <?=$limit?>)</h2>
	 <hr />
	 </div>
	 </div>
</div>
<div class="container">
<div class="row">
	<div class="col-sm-3">
		<p><b>Violations by Badge</b></p>
<?php 	$sql="select badge_involved, count(*)as cnt from violations group by badge_involved order by cnt desc limit ".$limit;
		$connection = Yii::$app->getDb();
		$command = $connection->createCommand($sql);
		$Result = $command->queryAll();

	$cnt=0;
	foreach ($Result as $key => $value) {
		$cnt +=1;
		echo "<p>".$cnt.". ".str_pad($value['badge_involved'], 5, '0', STR_PAD_LEFT)." - ".$value['cnt']."</p>".PHP_EOL;
	} ?>				
	</div>
	
	
	<div class="col-sm-3">
	<p><b>Violations by Club</b> (M/G)</p>
	
<?php	$sql="select short_name,was_guest FROM violations as v ".
			"JOIN badge_to_club as btc on v.badge_involved=btc.badge_number ".
			"JOIN clubs on btc.club_id=clubs.club_id;";
		$connection = Yii::$app->getDb();
		$command = $connection->createCommand($sql);
		$Result = $command->queryAll();
		
	$myClub=[];
	foreach ($Result as $club) {
		if(isset($myClub[$club['short_name']] )) {
			$myClub[$club['short_name']]['total'] +=1;
			if($club['was_guest']) {
				if(isset($myClub[$club['short_name']]['guest'])) {
					$myClub[$club['short_name']]['guest'] +=1;
				}else { $myClub[$club['short_name']]['guest'] = 1; }
			} else {
				if(isset($myClub[$club['short_name']]['mem'])) {
					$myClub[$club['short_name']]['mem'] +=1;
				}else { $myClub[$club['short_name']]['mem'] = 1; }
			}
		} else {
			$myClub[$club['short_name']]['total'] =1;
			if($club['was_guest']) {
				if(isset($myClub[$club['short_name']]['guest'])) {
					$myClub[$club['short_name']]['guest'] +=1;
				} else { $myClub[$club['short_name']]['guest'] = 1; }
			
			} else {
				if(isset($myClub[$club['short_name']]['mem'])) {
					$myClub[$club['short_name']]['mem'] +=1;
				} else { $myClub[$club['short_name']]['mem'] = 1; }
			}
		}
	}
	
	//Sort m_Array
	foreach ($myClub as $key => $row) {
		$myClub_value[$key] = $row['total'];
	}
	array_multisort($myClub_value, SORT_DESC, $myClub);

	$cnt=0;
	foreach ($myClub as $key => $value) {
		$cnt +=1;  If($cnt>$limit) { break; }
		if(isset($value['mem'])) {$mem = $value['mem']; } else { $mem=0; }
		if(isset($value['guest'])) {$guest = $value['guest']; } else { $guest=0; }
		echo "<p>".$cnt.". ".$key.' - '.$value['total']." (".$mem."/".$guest.")</p>".PHP_EOL;
	}
	
	?>
	</div>
	<div class="col-sm-3">
	<p><b>Violations By Rule</b></p>
<?php	$sql="select vi_rules, count(*) as cnt from violations group by vi_rules order by cnt desc;";
		$connection = Yii::$app->getDb();
		$command = $connection->createCommand($sql);
		$Result = $command->queryAll();

	$mydata=[];
	foreach ($Result as $key => $value) {
		if(strpos($value['vi_rules'],',')) {
			$more = explode(", ",$value['vi_rules']);
			foreach ($more as $rule) {
				
				if(Isset($mydata[$rule])) {
					$mydata[$rule] += $value['cnt'];
				} else {
					$mydata[$rule] = $value['cnt'];
				}
			}
		} else {
			if(Isset($mydata[$value['vi_rules']])) {
				$mydata[$value['vi_rules']] += $value['cnt'];
			} else {
				$mydata[$value['vi_rules']] = $value['cnt'];
			}				
		}
	}

	//Sort m_Array
	foreach ($mydata as $key => $row) {
		$vc_array_value[$key] = $row;
		$vc_array_name[$key] = $key;
	}
	array_multisort($vc_array_value, SORT_DESC, $vc_array_name, SORT_ASC, $mydata);

	$viol_list = ViolationsController::getViolationsList();
	
	$cnt=0;
	foreach ($mydata as $key => $value) {
		$cnt +=1;  If($cnt>$limit) { break; }
		if (isset($viol_list[$key])) { $note = $viol_list[$key]; } else { $note='Unk'; }
		echo $cnt.". ".$key.'<img src="/images/note.png" title="'.$note .'" style="width:20px"> - '.$value."<br />".PHP_EOL;
	}  ?>
	
	</div>
	<div class="col-sm-3">
	What Else?
	</div>
</div>
</div>