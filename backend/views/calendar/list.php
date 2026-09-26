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

	  $result .= '</tr></table>';
	}
	else $result = '';
	return $result;
}

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
			<?= $form->field($searchModel, 'key_words')->textInput(['value'=>$searchModel->key_words]).PHP_EOL; ?>
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
			<?= $form->field($searchModel,'event_date')->widget(DatePicker::classname(), ['options'=>['class'=>'form-control'],'pluginOptions' =>['todayHighlight' => true]]); ?>
		</div>
		<div class="col-xs-4 col-sm-2 col-md-2 col-lg-2 col-xl-2">
			<?= $form->field($searchModel, 'pagesize')->dropDownlist([ 20 => 20, 50 => 50, 100 => 100, 200=>200 ],['value'=>$searchModel->pagesize ,'id' => 'pagesize'])->label('Page size: ') ?>
		</div>
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
			<?php echo date('g:i a', strtotime($model->cal_start_time))." - ".date('g:i a', strtotime($model->cal_end_time));?>
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
		} elseif ($model->range_status_id == 5 ) {
			echo '<a style="text-decoration: none; font-weight: bold;font-size: 12px;" title="Event Details" href="/calendar/viewitem?calendar_id='.$model->calendar_id.'" target="Cal">Range Open<br>CLUB REGULATED </a>';
			//<!--<img src="/images/flag_clubregulated.png" alt="Range Open-Club Regulated" title="Range Open-Club Regulated"/>-->
		} elseif ($model->range_status_id == 6) { ?>
			<div style="text-decoration: none; font-weight: bold;font-size: 14px;">
			<a title="Event Details" href="/calendar/viewitem?calendar_id=<?=$model->calendar_id ?>" target='Cal'>
			Range Open<br />CLUB REGULATED<br />
			<img src="/images/flag_caliber_restriction.png" alt="Caliber Restriction" title="Caliber Restriction"/><br />
			<b>22LR ONLY<b/> </a></div>
<?php 	} else {
			echo $model->agcEventStatus->name;
		}
	} elseif($model->event_status_id == 21) {
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