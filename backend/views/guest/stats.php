<?php

use kartik\daterange\DateRangePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\guests */

$this->title = 'Stats';
$this->params['breadcrumbs'][] = ['label' => 'Guest', 'url' => ['guest/index']];
$this->params['breadcrumbs'][] = $this->title;

$where ='';
if (isset($_REQUEST['GuestSearch']['time_in'])) {
	$searchModel->time_in = $_REQUEST['GuestSearch']['time_in'];
	$time_in = explode(' - ',$_REQUEST['GuestSearch']['time_in']);
		$time_in_s = date('Y-m-d 00:00:00',strtotime($time_in[0]));
		$time_in_f = date('Y-m-d 23:59:59',strtotime($time_in[1]));
		$where =  " WHERE time_in >= '$time_in_s' AND time_in < '$time_in_f'";
} else {
	$time_in_s = date('Y-01-01 00:00:00',strtotime(yii::$app->controller->getNowTime()));
	$time_in_f = date('Y-m-d 23:59:59',strtotime(yii::$app->controller->getNowTime()) + 60*60*24);
	$where =  " WHERE time_in >= '$time_in_s' AND time_in < '$time_in_f'";
	$searchModel->time_in = date('m-d-Y',strtotime($time_in_s)).' - '.date('m-d-Y',strtotime($time_in_f));
}

$paid_sql = "SELECT count(*) as cnt,g_paid FROM BadgeDB.guest $where group by g_paid;";
$paid_Result = Yii::$app->getDb()->createCommand($paid_sql)->queryall();
$paid_yes = $paid_min = $paid_obs = $paid_spo = $paid_you = 0;
foreach ($paid_Result as $rec) {
	switch ($rec['g_paid']) {
		case  'o': $paid_obs += $rec['cnt']; break;
		case  'm': $paid_min += $rec['cnt']; break;
		case  's': $paid_spo += $rec['cnt']; break;
		case  'y': $paid_you += $rec['cnt']; break;
		case  '1':
		case  'a':
		case  'h':
		default: $paid_yes += $rec['cnt']; break;
	}
}

$sql = "select (select Count(*) from guest $where) as tot, (select count(Distinct badge_number) from guest $where) as members";
$Result = Yii::$app->getDb()->createCommand($sql)->queryall();
$gst_Total=$Result[0]['tot'];
$gst_Members=$Result[0]['members'];

if (isset($_REQUEST['GuestSearch']['q_Limit'])) { $limit = $_REQUEST['GuestSearch']['q_Limit']; } else { $limit = 250;}
$searchModel->q_Limit= $limit;


echo $this->render('_view-tab-menu').PHP_EOL;
?>
<br />
<div class="guest-stats">
<?php $form = ActiveForm::begin([
	'id'=>'postPrintTransactionForm',
	'action' => ['/guest/stats'],
	'method' => 'post',
]); ?>
	<div class="row">
        <div class="col-xs-2 col-xs-2">
            <?=html::a('<i class="fa fa-download" aria-hidden="true"></i> Export as CSV',['#'],['id'=>'customExportCsv','class'=>'btn btn-secondary'])?>
        </div>
        <div class="col-xs-4 col-xs-2">
			 <?= $form->field($searchModel, 'q_Limit')->textInput()->label('Limit'); ?>
		</div>
        <div class="col-xs-6 col-xs-6">


<?=  $form->field($searchModel, 'time_in', [
		'options'=>['class'=>'drp-container form-group']
		])->widget(DateRangePicker::classname(), [
			//'presetDropdown'=>true,
			'hideInput'=>true,
			'pluginOptions' => [
				'opens'=>'left',
				'locale'=>['format'=>'MM/DD/YYYY'],
			]])->label('Date:'); ?>
		</div>
		<div class="col-xs-2">
			<?= Html::submitButton('<i class="fa fa-search pull-right" aria-hidden="true"></i> Search', ['class' => 'btn btn-primary']) ?>

        </div>
    </div>
<?php ActiveForm::end(); ?>
	<div class="row">
		<div class="col-xs-12">
			<h2 style="text-align:center;"><?=$gst_Total?> Guests from <?=$gst_Members?> Members  <!--(Top <?=$limit?>)--></h2>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-2"><b>Paying:</b> <?=$paid_yes?></div>
		<div class="col-xs-2"><b>Spouse:</b> <?=$paid_spo?></div>
		<div class="col-xs-2"><b>Minor:</b> <?=$paid_min?></div>
		<div class="col-xs-2"><b>Youth:</b> <?=$paid_you?></div>
		<div class="col-xs-2"><b>Observer:</b> <?=$paid_obs?></div>
	</div>
<hr />

</div>
<div class="container">
<div class="row">
	<div class="col-xs-1"><p>#</p></div>
	<div class="col-xs-2 col-md-1"><p><b>Badge</b></p></div>
	<div class="col-xs-1"><p><b># Guests</b></p></div>
	<div class="col-xs-5"><p><b>Number of different Guests & frequency <u>(ALL Time)</u></b></p></div>
	<div class="col-xs-3"><p><b>Guest became a Member (Badge/Date Joined)</b></p></div>
</div>


<?php
	// All Guests
	$sql="select badge_number, count(*) as cnt from guest $where group by badge_number order by cnt desc LIMIT ".$limit;

	$connection = Yii::$app->getDb();
	$command = $connection->createCommand($sql);
	$Result = $command->queryAll();

	$cnt=0;
	foreach ($Result as $key => $value) {
		$cnt +=1;
		echo "<div class='row'>\n".
			"<div class='col-xs-1'>".$cnt.".</div>".
			"<div class='col-xs-2 col-md-1'>".str_pad($value['badge_number'], 5, '0', STR_PAD_LEFT)."</div>".
			"<div class='col-xs-1'>".$value['cnt']."</div>".PHP_EOL;
		// COL #2
		echo '<div class="col-xs-5">';
		$gfrequency="";
		$gsingle=0;
		$gcount=0;
		$myGuest=array();

		$sql="select Count(*) as tot1,g_last_name, g_first_name, time_in from guest where badge_number=".$value['badge_number']." group by g_last_name, g_first_name order by tot1 desc";
		$connection = Yii::$app->getDb();
		$command = $connection->createCommand($sql);
		$Result = $command->queryAll();
		foreach ($Result as $key => $value1) {
			$myGuest[strtoupper($value1['g_last_name']).strtoupper($value1['g_first_name'])] = $value1['time_in'];

			if($value1['tot1']>1) {
				$gfrequency .=$value1['tot1']."x, ";
				$gcount +=1;
			} else {
				$gsingle +=1;
			}
		}
		$gcount += $gsingle;
		$gfrequency=substr($gfrequency,0,strlen($gfrequency)-2);
		if($gcount>1) {
			echo "<p>".$gcount." different guests; ".$gfrequency.", plus ".$gsingle." other(s)</p>";
		} else {
			echo "<p>".$gcount." guest(s); ".$gfrequency."</p>";
		}
		echo "</div>".PHP_EOL;

		// COL #3
		echo '<div class="col-xs-3">'."\n";

		$sql="Select badge_number, incep, first_name, last_name from badges where (first_name, last_name) in".
		" (select distinct g.g_first_name, g.g_last_name from guest g where badge_number = ".$value['badge_number']." group by g.g_first_name, g.g_last_name)";
		$connection = Yii::$app->getDb();
		$command = $connection->createCommand($sql);
		$Result = $command->queryAll();

		$badge_count=0;
		foreach ($Result as $key => $mem_badge) {

			$find_name = strtoupper($mem_badge['last_name']).strtoupper($mem_badge['first_name']);
//yii::$app->controller->createLog(false, 'stats:103', var_export($find_name,true));

			if (isset($myGuest[$find_name])) {

				if (strtotime($myGuest[$find_name]) < strtotime($mem_badge['incep'])) {
					echo " [(".$mem_badge['badge_number'].") ".$mem_badge['incep'];
					$badge_count ++;
				}
			}
		}
		if($badge_count) echo " - ".$badge_count;

		echo "</div>\n</div>".PHP_EOL;
	}
?>
</div>
</div>
</div>
</div>