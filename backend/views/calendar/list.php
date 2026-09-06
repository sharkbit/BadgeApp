<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\daterange\DateRangePicker;
use backend\models\clubs;
use backend\models\agcFacility;
use backend\models\agcEventStatus;
use backend\models\agcRangeStatus;

function table_page_navigation($start, $number_displayed, $total, $extra_get_params='', $name_var='start') {
	$first = 'first';
	$prev  = 'previous';
	$next  = 'next';
	$last  = 'last';

	//$extra_get_params = remove_duplicates_in_get_params($extra_get_params, array($name_var));
	if($extra_get_params != '') $extra_get_params = '&' . $extra_get_params;
	if($total > $number_displayed){
	  $result = '<table border="0" width="100%" ><tr><td style="padding-right:5px;" align="right" valign="top">';

	  // First
	  if ($start > 0)
		$result .= '<a  href="?'.$name_var.'=0' . $extra_get_params .'" class="small_link">'.$first.'</a>';
	  else
		$result .= '<span class="dissabled_link">'.$first.'</span>';
	  $result .= '</td><td style="padding-right:5px" align=center>';
	  
	  // Prev $number_displayed
		   $temp_start = $start - $number_displayed;
	  if ($temp_start < 0) $temp_start = 0;
	  if ($start > 0) $result .= '<a href="?'.$name_var.'='.$temp_start . $extra_get_params . '" class="small_link">&#171; '.$prev.' <!--'.$number_displayed.'--></a>';
	  else $result .= '<span class="dissabled_link">&#171; '.$prev.' <!--'.$number_displayed.'--></span>';

	  //pages
	  $result .= '</td><td style="padding-right:2px" align=center>';
	  if($total/$number_displayed >= 21) {
	  $first_number = $start/$number_displayed-9;
	  $last_number = $start/$number_displayed+11;
	  if($start/$number_displayed+1 <= 11){
		  $first_number = 1;
		  $last_number = 21;
		  }

	  if( $start/$number_displayed+11 > (int)($total/$number_displayed)){
		  $first_number = (int)($total/$number_displayed)-20;
		  $last_number = $total/$number_displayed;
		  }
	  }else{
	  $first_number = 1;
	  $last_number = $total/$number_displayed;
	  }

	  for($i=$first_number; $i-1 < $last_number; $i++)
		if(($i-1)*$number_displayed == $start) $result .= '<span class="dissabled_link">'.$i.'</span>&nbsp;';
		else $result .= '<a href="?' . $name_var . '=' . (($i-1) * $number_displayed) . $extra_get_params . '" class="small_link">'.$i.'</a>&nbsp;';
	  $result .= '</td><td style="padding-right:5px" align=center>';

	  // Next $number_displayed
	  if ($start + $number_displayed < $total)
		$result .= '<a href="?' . $name_var . '=' . ($start + $number_displayed) . $extra_get_params . '" class="small_link">'.$next.' &#187;<!--' . $number_displayed . '--></a>';
	  else $result .= '<span class=dissabled_link>'.$next.' &#187;<!--' . $number_displayed . '--></span>';
	  $result .= '</td><td style="padding-right:5px" align=center>';
	  // Last
	  if ($start + $number_displayed < $total)
		$result .= '<a href="?' . $name_var . '=' . ($total-($total%$number_displayed)) . $extra_get_params.'" class="small_link">'.$last.'</a>';
	  else $result .= '<span class="dissabled_link">'.$last.'</span>';
	  

//	  $result .= '</td></tr></table>';
	  $result .= '</tr></table>';
	}
	else $result = '';
	return $result;
}


	/*		   

date_default_timezone_set('America/New_York');
$url="/"; $range=false;
$active_fields = new stdClass();
$active_fields->start_time=1;
$active_fields->end_time=1;
$active_fields->event_name = 1;
$active_fields->range_status_id=1;

$username = Yii::$app->db->username;
$password = Yii::$app->db->password;
$dbname = 'associat_agcnew';
$conn = mysqli_connect('localhost', $username, $password, $dbname);

$display_month_date = '';
$pagesize = 100;
$start=0;
$search_club_id='';
$search_end_date='';
$search_event_category_id='';
$search_event_date = '';
$search_facility_id ='';
$search_keywords = '';
$search_range_status_id='';
$search_start_date='';
$start_month_date=date('Y-m');
$search_string='';

if (isset($_REQUEST['reset_search'])) {
	echo "Destroy!<br>\n";
	//session_destroy(); session_start();
	unset($_SESSION['pagesize']);
	unset($_SESSION['search_club_id']);
	unset($_SESSION['search_event_category_id']);
	unset($_SESSION['search_event_date']);
	unset($_SESSION['search_facility_id']);
	unset($_SESSION['search_keywords']);
	unset($_SESSION['search_range_status_id']);
	unset($_SESSION['search_start_date']);
	unset($_SESSION['start_month_date']);
	
	$range = " WHERE start_time >= '".date('Y-m-d 00:00',strtotime(date('Y-m-d'))).
		"' AND start_time < '".	date('Y-m-d 00:00',strtotime(date('Y-m-d')." +1 month"))."'";
	$search_start_date = date('Y-m-d',strtotime(date('Y-m-d')));
	$search_end_date = 	date('Y-m-d',strtotime(date('Y-m-d')." +1 month"));
	
} else {
	if (isset($_REQUEST['pagesize'])) {
		$pagesize = (INT)$_REQUEST['pagesize'];
//$search_string .= "&pagesize=".$pagesize;
		$_SESSION['pagesize'] = $_REQUEST['pagesize'];
	} elseif (isset($_SESSION['pagesize'])) {
		$pagesize = (INT)$_SESSION['pagesize'];
	//	$search_string .= "&pagesize=".$pagesize;
	}

	if (isset($_REQUEST['start'])) { $start =(int)mysqli_real_escape_string($conn,$_REQUEST['start']); } else { $start=0; }

	if (isset($_REQUEST['search_event_date']) && $_REQUEST['search_event_date'] != '' ) {
//echo "Date a<br>\n";
		$search_event_date = mysqli_real_escape_string($conn,$_REQUEST['search_event_date']);
		$_SESSION['search_event_date'] = $search_event_date;
		$range = " WHERE event_date like '".date('Y-m-d',strtotime(date($search_event_date)))."%'";		
	} elseif (isset($_REQUEST['search_start_date']) && $_REQUEST['search_start_date'] != '' && 	isset($_REQUEST['search_end_date']) && $_REQUEST['search_end_date'] != '') {
//echo "Date b<br>\n";
		$search_start_date = mysqli_real_escape_string($conn,$_REQUEST['search_start_date']);
		$search_end_date = mysqli_real_escape_string($conn,$_REQUEST['search_end_date']);
		$_SESSION['search_start_date'] = $search_start_date;
		$_SESSION['search_end_date'] = $search_end_date;
		$display_month_date = date('d, F, Y',strtotime(date($search_start_date))). ' to '.date('d, F, Y',strtotime(date($search_end_date)));		
		$range = " WHERE event_date >= '".date('Y-m-d 00:00',strtotime(date($search_start_date)))."'".
			" AND event_date <= '".date('Y-m-d 00:00',strtotime(date($search_end_date)))."'";		
	} elseif (isset($_REQUEST['start_month_date'])) {
//echo "Date c<br>\n";
		$start_month_date = mysqli_real_escape_string($conn,$_REQUEST['start_month_date']);
		//$search_string .= "&start_month_date=".$start_month_date;
		$_SESSION['start_month_date'] = $start_month_date;
		$display_month_date = date('F, Y',strtotime(date($start_month_date)));
		$search_start_date = date('Y-m-d',strtotime(date($start_month_date)));
		$search_end_date = date('Y-m-d',strtotime(date($start_month_date)." +1 month"));
		$range = " WHERE event_date >= '".date('Y-m-d 00:00',strtotime(date($start_month_date)))."'".
			" AND event_date <= '".date('Y-m-d 00:00',strtotime(date($start_month_date)." +1 month"))."'";		
	} elseif (isset($_SESSION['search_event_date'])) {
//echo "Date d<br>\n";
		$search_event_date = $_SESSION['search_event_date'];
		$search_start_date = date('Y-m-d',strtotime(date($start_month_date)));
		$search_end_date = date('Y-m-d',strtotime(date($start_month_date)." +1 month"));
		$range = " WHERE event_date like '".date('Y-m-d',strtotime(date($search_event_date)))."%'";		
	} elseif (isset($_SESSION['start_month_date'])) {
//echo "Date e<br>\n";
		$start_month_date = $_SESSION['start_month_date'];
		$display_month_date = date('F, Y',strtotime(date($start_month_date)));
		$search_start_date = date('Y-m-d',strtotime(date($start_month_date)));
		$search_end_date = date('Y-m-d',strtotime(date($start_month_date)." +1 month"));
		$range = " WHERE event_date >= '".date('Y-m-d 00:00',strtotime(date($start_month_date)))."'".
			" AND event_date <= '".date('Y-m-d 00:00',strtotime(date($start_month_date)." +1 month"))."'";		
	} else {
//echo "Date f<br>\n";
		$range = " WHERE event_date >= '".date('Y-m-d 00:00',strtotime(date('Y-m-d'))).
			"' AND event_date <= '".	date('Y-m-d 00:00',strtotime(date('Y-m-d')." +1 month"))."'";
		$search_start_date = date('Y-m-d',strtotime(date('Y-m-d')));
		$search_end_date = 	date('Y-m-d',strtotime(date('Y-m-d')." +1 month"));
	}

	if (isset($_REQUEST['search_club_id']) && $_REQUEST['search_club_id'] !=0) {
		$search_club_id = mysqli_real_escape_string($conn,$_REQUEST['search_club_id']);
		$_SESSION['search_club_id'] = $search_club_id;
		$range .= " AND ac.club_id =".$search_club_id ;
	} elseif (isset($_SESSION['search_club_id'])) {
		$search_club_id = $_SESSION['search_club_id'];
		$range .= " AND ac.club_id =".$search_club_id ;
	} 
	
	if (isset($_REQUEST['search_facility_id']) && $_REQUEST['search_facility_id'] !=0) {
		$search_facility_id = mysqli_real_escape_string($conn,$_REQUEST['search_facility_id']);
		$_SESSION['search_facility_id'] = $search_facility_id;
		$range .= " AND JSON_CONTAINS(ac.facility_id,'".$search_facility_id."')" ;
	} elseif (isset($_SESSION['search_facility_id'])) {
		$search_facility_id = $_SESSION['search_facility_id'];
		$range .= " AND JSON_CONTAINS(ac.facility_id,'".$search_facility_id."')" ;
	}
	
	if (isset($_REQUEST['search_event_category_id']) && $_REQUEST['search_event_category_id'] !=0) {
		$search_event_category_id = mysqli_real_escape_string($conn,$_REQUEST['search_event_category_id']);
		$_SESSION['search_event_category_id'] = $search_event_category_id;
		$range .= " AND ac.event_status_id =".$search_event_category_id ;
	} elseif (isset($_SESSION['search_event_category_id'])) {
		$search_event_category_id = $_SESSION['search_event_category_id'];
		$range .= " AND ac.event_status_id =".$search_event_category_id ;
	}
	
	if (isset($_REQUEST['search_range_status_id']) && $_REQUEST['search_range_status_id'] !=0) {
		$search_range_status_id = mysqli_real_escape_string($conn,$_REQUEST['search_range_status_id']);
		$_SESSION['search_range_status_id'] = $search_range_status_id;
		$range .= " AND ac.range_status_id =".$search_range_status_id ;
	} elseif (isset($_SESSION['search_range_status_id'])) {
		$search_range_status_id = $_SESSION['search_range_status_id'];
		$range .= " AND ac.range_status_id =".$search_range_status_id ;
	}

	if (isset($_REQUEST['search_keywords']) && $_REQUEST['search_keywords'] !='') {
		$req = htmlspecialchars($_REQUEST['search_keywords'], ENT_QUOTES, 'UTF-8');
		$search_keywords = mysqli_real_escape_string($conn,$req);
		$_SESSION['search_keywords'] = $search_keywords;
		$range .= " AND (keywords like '%".$search_keywords."%' OR event_name like '%".$search_keywords."%' OR club_name like '%".$search_keywords."%')";
	} elseif (isset($_SESSION['search_keywords'])) {
		$search_keywords = $_SESSION['search_keywords'];
		$range .= " AND (keywords like '%".$search_keywords."%' OR event_name like '%".$search_keywords."%' OR club_name like '%".$search_keywords."%')";
	} 
}

$start_prev_month_date = date('Y-m',strtotime(date($search_start_date)." -1 month"));
$start_curr_month_date = date('Y-m');
$start_next_month_date = date('Y-m',strtotime(date($search_start_date)." +1 month"));

if ($start > 0) {
	$limit = " Limit ".$start.", ".$pagesize;
} else {
	$limit = " Limit ".$pagesize;
}


$tick=0;  // Colors for Rows

$sql_a = "SELECT ac.*, club_name, es.name AS event_status, rs.name AS range_status";
$sql_b = "SELECT count(*) as cnt";
$sql_x = " FROM cal_calendar ac".
	" LEFT OUTER JOIN BadgeDB.clubs c ON c.club_id = ac.club_id".
	" LEFT OUTER JOIN cal_event_status es ON es.event_status_id = ac.event_status_id".
	" LEFT OUTER JOIN cal_range_status rs ON rs.range_status_id = ac.range_status_id";
$order = " ORDER BY event_date,hour(start_time) ";
$range.=" AND approved=1 AND ac.active=1 AND deleted=0";

//echo  $sql_a.$sql_x.$range.$order.$limit."<hr>\n";
//echo  $range."<hr>\n";
$result = mysqli_query($conn, $sql_a.$sql_x.$range.$order.$limit);
if($result) {
	$res_count = mysqli_query($conn, $sql_b.$sql_x.$range);
	$total_rows = mysqli_fetch_array($res_count)['cnt'];

	$table_navigation = table_page_navigation($start, $pagesize, $total_rows, substr($search_string,1));
} else {$table_navigation='';}
*/
$this->title = "AGC Calendar Events";
?>


<div class="work-credits-form" ng-controller="CalendarListFrom">
	<?php $form = ActiveForm::begin([ 'id'=>'CalendarListFrom' ]); ?>
	<div class="row">
		<div class="col-xs-12">
			<h2><?= Html::encode($this->title) ?></h2>
		</div>
	</div>
<details>
	<summary> -- Search Filter -- </summary>
	
	<section>

	<div class="row">
		<div class="col-sm-6 col-md-3">
			<?= $form->field($searchModel, 'keywords')->textInput(['maxlength'=>true]) ?>
		</div>
		<div class="col-sm-6 col-md-3">
			<?= $form->field($searchModel, 'club_id')->dropDownList((new clubs)->getClubList(false,false,true), ['prompt'=>'Any','value'=> $searchModel->club_id]).PHP_EOL; ?>
		</div>
		<div class="col-xs-12 col-sm-9 col-md-6">
			<?= $form->field($searchModel, 'facility_id')->dropDownList((new agcFacility)->getFacilityList(),['prompt'=>'Any','multiple'=>true, 'size'=>false]).PHP_EOL; ?>
		</div>
		<div class="col-xs-6 col-sm-3">
			 <?= $form->field($searchModel, 'event_status_id')->DropDownList((new agcEventStatus)->getStatusList(),['prompt'=>'Any','value'=> $searchModel->event_status_id]).PHP_EOL; ?>
		</div>
		<div class="col-xs-6 col-sm-3">
			<?= $form->field($searchModel, 'range_status_id')->DropDownList((new agcRangeStatus)->getStatusList(),['prompt'=>'Any','value'=> $searchModel->range_status_id]).PHP_EOL; ?>
		</div>
	
		<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 col-xl-2" >
		<?=  $form->field($searchModel, 'SearchTime', [
		'options'=>['class'=>'drp-container form-group']
		])->widget(DateRangePicker::classname(), [
		'convertFormat'=>true,
		'pluginOptions' => [
			'opens'=>'left',
			'locale'=>['format'=>'Y-m-d','separator'=>' - ',],
		]])->label('Date range:'); ?>
		</div>
		<div class="col-xs-1 text-center"> <br /><b>OR</b> </div>
		<div class="col-xs-5 col-sm-3 col-md-2 col-lg-2 col-xl-2">
			<?= $form->field($searchModel,'event_date')->widget(DatePicker::classname(), ['options'=>['class'=>'form-control']]); ?>
		</div>
		<div class="col-xs-4 col-sm-2 col-md-2 col-lg-2 col-xl-2">
			<?= $form->field($searchModel, 'pagesize')->dropDownlist([ 20 => 20, 50 => 50, 100 => 100, 200=>200 ],['value'=>$searchModel->pagesize ,'id' => 'pagesize'])->label('Page size: ') ?>
		</div>
		
<? //$search_start_date?>
<? //$search_end_date?>
<? //$search_event_date?>
		
	</div>
	<div class="row">
		
		<div class="col-sm-3 btn-group pull-right">
		
		<?= Html::submitButton('Search <i class="fa fa-arrow-right"> </i>',['class' => 'btn btn-primary next-Credit']) ?>
		
		<?= Html::submitButton('Reset <i class="fa fa-eraser"> </i>', ['name' => 'form_action','value' => 'reset','class' => 'btn btn-warning']) ?>
		</div>
	</div>
	<?php ActiveForm::end(); ?>
	</section>
</details>
</div>
<hr />

<?php if($groupedModels) { ?>
<div class="post-list">
<style>
  .darker { background-color: #d3d3d3; }
</style>
<?php foreach ($groupedModels as $dateKey => $models): ?>
		
	<!-- Date Breakout Header -->
	<div class="row">
		<div class="col-xs-12">
			<h3><?= Html::encode(date('l, F j, Y',strtotime($dateKey))) ?></h3>
		</div>
	</div>
		
	<!-- Items under this date -->
	<?php $xx=0;
		foreach ($models as $model):
			$xx++; 
			if ($xx % 2 != 0) { $bgColor=' darker';} else {$bgColor='' ;} ?>
	<div class="row<?=$bgColor?>">
		<div class="col-xs-3">
			<?php echo date('g:i a', strtotime($model->start_time))." - ".date('g:i a', strtotime($model->end_time));?>
		</div>
		<div class="col-xs-7">
			<a style="text-decoration: none; font-size: 12px;" href="/calendar/viewitem?calendar_id=<?=$model->calendar_id ?>" target='Cal'><?=Html::encode($model->event_name) ?></a>  
			<br/><?=$model->clubs->club_name ?><br/><?=(New AgcFacility)->getFacilityNames($model->facility_id);?>
		</div>
		<div class="col-xs-2">
<?php 	// ### Range Status Icons
	if ( ($model->event_status_id != 19) and ($model->event_status_id != 21) ) {
		$fac_id=json_decode($model->facility_id);

		if ( ($model->range_status_id ==2 ) and ( array_intersect(array(2,3,7,10,24,25,27,28,30,32),$fac_id) ) ) {
			echo '<img src="/images/flag_closed.png" alt="Closed" title="Closed"/>';
		}
		if ($model->range_status_id == 5 ) { 
			echo '<a style="text-decoration: none; font-weight: bold;font-size: 12px;" title="Event Details" href="/calendar/viewitem?calendar_id='.$model->calendar_id.'" target="Cal">Range Open<br>CLUB REGULATED </a>';
			//<!--<img src="/images/flag_clubregulated.png" alt="Range Open-Club Regulated" title="Range Open-Club Regulated"/>-->
		}
		if ($model->range_status_id == 6) { ?>
			<div style="text-decoration: none; font-weight: bold;font-size: 14px;">
			<a title="Event Details" href="/calendar/viewitem?calendar_id=<?=$model->calendar_id ?>" target='Cal'>
			Range Open<br />CLUB REGULATED
			<br />
			<img src="/images/flag_caliber_restriction.png" alt="Caliber Restriction" title="Caliber Restriction"/><br />
			<b>22LR ONLY<b/> </a></div>
<?php 	} 
	}
 	if($model->event_status_id == 21) {
		echo '<img src="/images/flag_rescheduled.png" alt="Canceled" title="Reschuduled"/> ';
	} elseif($model->event_status_id == 19 || $model->range_status_id == 4) {
		echo '<img src="/images/flag_canceled_event.png" alt="Canceled" title="Canceled"/>';
	} 
?>
		
		</div>
	</div>
	<?php endforeach; ?>
	
<?php endforeach; ?>
</div>

<?php } else { ?>

No Records found! 
<script>
  const detailsElement = document.querySelector("details");
  detailsElement.open = true;
</script>
<?php } ?>

<?php /*
<hr />


<table align="left" cellpadding="0" cellspacing="0" class="list" width="100%" border="0">
	<tr>
		<td colspan="20" align="left" valign="top" class="actions">
			<table cellpadding="0" cellspacing="0" width="100%" border="0">
				<tr>
					<td width="50%" align="left">
						<a style="font-size: 14px; font-weight: bold;" href="<?=$_SERVER['SCRIPT_NAME']?>">Next 30 Days</a>
					</td>
					<td width="50%" align="right">
						<a href="?start_month_date=<?=$start_prev_month_date.$search_string?>">Previous month</a> &nbsp; | &nbsp;
						<a href="?start_month_date=<?=$start_curr_month_date.$search_string?>">Current month</a> &nbsp; | &nbsp;
						<a href="?start_month_date=<?=$start_next_month_date.$search_string?>">Next month</a>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="20" align="left">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr>
					<td width="40%" align="left">
						<?php if ($display_month_date != '') { echo "Results for: ".$display_month_date; } ?>
					</td>
					<td align="right"><?php if($result) { ?> Show
						<select name="pagesize" onchange="this.form.submit();">
							<option value="25"<?php if ($pagesize == 25) {echo " selected";} ?>>25</option>
							<option value="50"<?php if ($pagesize == 50) {echo " selected";} ?>>50</option>
							<option value="100"<?php if ($pagesize == 100) {echo " selected";} ?>>100</option>
							<option value="250"<?php if ($pagesize == 250) {echo " selected";} ?>>250</option>
							<option value="500"<?php if ($pagesize == 500) {echo " selected";} ?>>500</option>

					</select> Results on one page<?php } ?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr class="hidden"><td colspan="20"></td></tr>
<?php if (($result) and ($table_navigation)) { ?>
	<tr>
		<td align="right" colspan="20"><?=$table_navigation?></td>
	</tr>
<?php }
	$event_date_value ='0000-00-00';
	if($result) {
		while ($fetch_result = mysqli_fetch_array($result)) {
			//echo var_export($fetch_result)."<hr>";

			if ($event_date_value != $fetch_result['event_date']) {
?>
			<!--  <tr><td colspan="3"> &nbsp; </td></tr> -->
			<tr>
			<td valign="middle" align="left" class="table_header" colspan="3"><b><?php echo date('l, F j, Y',strtotime($fetch_result['event_date'])); ?></b></td>
			</tr>
		<?php 	$event_date_value = $fetch_result['event_date'];
			}
			$tick++;
			if ($tick==2) {$cycle='dark'; $tick=0;} else {$cycle='light';}
			?>

	<tr onmouseover='this.className="highlight"' onmouseout='this.className="<?=$cycle?>"' class="<?=$cycle?>">

		<td valign="middle" align="left" class="row_css" width="150" style="font-size: 12px;">
		<?php
		if($active_fields->start_time == 1) { echo date('g:i a', strtotime($fetch_result['start_time'])); }
		if($active_fields->end_time == 1)   { echo " - ".date('g:i a', strtotime($fetch_result['end_time'])); }
		?>
		</td>

		<?php if($active_fields->event_name == 1) { ?>
		<td valign="middle" align="left" class="row_css" width="388"  style="font-size: 12px;">
			<a style="text-decoration: none; font-size: 12px;" href="<?=$url?>calendar/viewitem?calendar_id=<?=$fetch_result['calendar_id']?>" target='Cal'><?=$fetch_result['event_name']?></a>  
			<br/><?=$fetch_result['club_name']?><br/><?=(New AgcFacility)->getFacilityNames($fetch_result['facility_id']);?>
		</td>
		<?php } ?>

		<td valign="middle" align="center" class="row_css" width="120"  style="font-size: 12px;">

<?php 	if ( ($active_fields->range_status_id) and ($fetch_result['event_status_id'] != 19) and ($fetch_result['event_status_id'] != 21) ) { ?>
		
<?php	 	$fac_id=json_decode($fetch_result['facility_id']);

			if ( ($fetch_result['range_status_id'] ==2 ) and ( array_intersect(array(2,3,7,10,24,25,27,28,30,32),$fac_id) ) ) { ?>
			<img src="/calendar/images/flag_closed.png" alt="Closed" title="Closed"/>
<?php		}
			if ($fetch_result['range_status_id'] == 5 ) { ?>
			<a style="text-decoration: none; font-weight: bold;font-size: 12px;" title="Event Details" href="<?=$url?>calendar/viewitem.php?calendar_id=<?=$fetch_result['calendar_id']?>" target='Cal'>
			Range Open<br>CLUB REGULATED </a>
			<!--<img src="{$url}images/flags/flag_clubregulated.png" alt="Range Open-Club Regulated" title="Range Open-Club Regulated"/>-->
<?php 		}
			if ($fetch_result['range_status_id'] == 6) { ?>
			<div style="text-decoration: none; font-weight: bold;font-size: 14px;">
			<a title="Event Details" href="<?=$url?>calendar/viewitem.php?calendar_id=<?=$fetch_result['calendar_id']?>" target='Cal'>
			Range Open<br>CLUB REGULATED
			<br>
			<img src="<?=$url?>calendar/images/flag_caliber_restriction.png" alt="Caliber Restriction" title="Caliber Restriction"/><br />
			<B>22LR ONLY<b/> </a></div>
<?php 		} 
		} ?>
		
<?php 	if($fetch_result['event_status_id'] == 21) {
			echo '<img src="/calendar/images/flag_rescheduled.png" alt="Canceled" title="Reschuduled"/> ';
		}
		elseif($fetch_result['event_status_id'] == 19 || $fetch_result['range_status_id'] == 4) {
			echo '<img src="/calendar/images/flag_canceled_event.png" alt="Canceled" title="Canceled"/>';
	 	} ?>
		</td>
	</tr>
<?php } } else { ?>
	<tr>
		<td align="left" colspan="20" class="info_attention"><h2>No Records Found.</h2></td>
	</tr>
<?php } ?>
	<tr class="hidden"><td colspan="20"> </td></tr>
<?php if (($result) and ($table_navigation)) { ?>
	<tr>
		<td align="right" colspan="20"><?=$table_navigation?></td>
	</tr>
<?php } ?>
	<tr ><td colspan="20"> &nbsp; </td></tr>
</table>
</form>
*/ ?>


<link rel="stylesheet" href="js/cal_jquery-ui.css" />
<link rel="stylesheet" href="js/cal_style.css" />
<script src="js/cal_jquery-1.12.4.js"></script>
<script src="js/cal_jquery-ui.js"></script>
<script>
  $("#agccalsearch-facility_id").select2({placeholder_text_multiple:'Choose Clubs',width: "100%"});
  $( function() { $( "#datepicker_S" ).datepicker({dateFormat: "yy-mm-dd",changeMonth: true}); } );
  $( function() { $( "#datepicker_E" ).datepicker({dateFormat: "yy-mm-dd",changeMonth: true}); } );
  $( function() { $( "#datepicker_D" ).datepicker({dateFormat: "yy-mm-dd",changeMonth: true, changeYear: true}); } );
</script>

<?php // mysqli_close($conn); ?>