<?php

use backend\models\clubs;
use backend\models\agcFacility;
use backend\models\agcEventStatus;
use backend\models\agcRangeStatus;
use backend\models\Params;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

use kartik\date\DatePicker;
use kartik\widgets\TimePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\Calendar */
/* @var $form yii\widgets\ActiveForm */

$getNowTime = yii::$app->controller->getNowTime();
if($model->isNewRecord) {
    $model->start_time=date('h:00 A', strtotime($getNowTime));
    $model->end_time=date('h:00 A', strtotime($getNowTime) + 60*60*2);
    $model->date_requested =$getNowTime;
} else {
    $model->start_time=date('h:i A', strtotime($model->start_time));
    $model->end_time=date('h:i A', strtotime($model->end_time));
}
$Req_Lanes = ArrayHelper::index(agcFacility::find('facility_id')->where(['active'=>1])
                ->andwhere('available_lanes>0')->orderBy(['name'=>SORT_ASC])->asArray()->all(),'facility_id');
if (array_intersect(json_decode($model->facility_id),array_column($Req_Lanes,'facility_id'))) {
    $allowLanes = true;
} else {
    $allowLanes = false;
    $model->lanes_requested = 0;
}
$is_club = ArrayHelper::getColumn(clubs::find()->where(['status'=>0,'is_club'=>1])->orderBy(['club_name'=>SORT_ASC])->asArray()->all(), 'club_id');

$chkMon = $chkTue = $chkWed = $chkThu = $chkFri = $chkSat = $chkSun = '';
if(isset($model->recur_week_days)) {
    $chk= @json_decode($model->recur_week_days);
    if((is_object($chk)) && (isset($chk->weekly)) && ($chk->weekly==1)) {
        if(in_array('mon',$chk->days)) { $chkMon = 'checked'; }
        if(in_array('tue',$chk->days)) { $chkTue = 'checked'; }
        if(in_array('wed',$chk->days)) { $chkWed = 'checked'; }
        if(in_array('thu',$chk->days)) { $chkThu = 'checked'; }
        if(in_array('fri',$chk->days)) { $chkFri = 'checked'; }
        if(in_array('sat',$chk->days)) { $chkSat = 'checked'; }
        if(in_array('sun',$chk->days)) { $chkSun = 'checked'; }
    }
}

$isMaster=false; $recur_disab=false; $Div_recur='style="display: none"';
if ($model->recur_every && $model->recurrent_calendar_id != 0 ) {
    echo "<hr />Editing a Recurring Event. ";
    if($model->recurrent_calendar_id == $model->calendar_id  ) {
        $isMaster=true;
        echo " Master Record with ";
        $sqlSearch = $model->calendar_id;
        $say="Editing Master Record Will:<ul>".
            "<li>Change All: Sponsor, Event Names, Keywords</li>".
            "<li>Only Adjust Future: Facilities, Lanes Requested, Event Status, Range Status, Event Times, POC info, is Deleted</li></ul><hr />";
    } else {
        if(yii::$app->controller->hasPermission('calendar/recur')) {echo " Click to <a href='/calendar/update?id=".$model->recurrent_calendar_id."'>Edit the Series</a> - "; }
        $sqlSearch = $model->recurrent_calendar_id;
        $say='';
        $recur_disab=true;
    }
    $sql = "select (select count(*) from associat_agcnew.agc_calendar where recurrent_calendar_id=".$sqlSearch." and event_date <'".date('y-m-d', strtotime($getNowTime))."') as past,".
        " (select count(*) from associat_agcnew.agc_calendar where recurrent_calendar_id=".$sqlSearch." and event_date >='".date('y-m-d', strtotime($getNowTime))."') as  fut;";

    $data = Yii::$app->getDb()->createCommand($sql)->queryAll();
    echo " (Past: <B>".$data[0]['past']."</b> Future: <B>".$data[0]['fut'].")</b><hr />";
    echo $say;

    $model->recurrent_start_date = date('d M',strtotime($model->recurrent_start_date));
    $model->recurrent_end_date = date('d M',strtotime($model->recurrent_end_date));
    $Div_recur='';
 }

$crec = Yii::$app->request->get('recur', 0);
if (($crec==1) && ($model->isNewRecord)) {
    $Div_recur='';
    $model->recur_every=1;
    $model->recurrent_calendar_id=$model->calendar_id;
}

$confParams  = Params::findOne('1');
if(yii::$app->controller->hasPermission('calendar/all')) {
	$ary_club =   (new clubs)->getClubList();
	$ary_club_ac =(new clubs)->getClubList(true);
	$ary_avoid =  (new clubs)->getAvoid();
} else {
	$ary_club =	  (new clubs)->getClubList(false,Yii::$app->user->identity->clubs);
	$ary_club_ac =(new clubs)->getClubList(true,Yii::$app->user->identity->clubs);
	$ary_avoid =  (new clubs)->getAvoid(Yii::$app->user->identity->clubs);
}
$dirty = array();
$search_all = [$ary_club, $ary_club_ac,$ary_avoid];
foreach($search_all as $word_search) {
	foreach($word_search as $dirt) {
		$dirt = explode(" ",strtoupper ($dirt));
		foreach($dirt as $item) {
			if($item==''){continue;}
			if(in_array($item,json_decode($confParams->whitelist))) {continue;}
			if(!in_array($item,$dirty)) { $dirty[]=$item; }
		}
	}
}
sort($dirty);

if(isset($_REQUEST['hideRepub']) && ($_REQUEST['hideRepub']=="no")) { $hideRepub=false; } else { $hideRepub=true; }
?>

 <input type='hidden' id='bad_words' value='<?=htmlspecialchars(json_encode($dirty),ENT_QUOTES)?>' />
 <input type="hidden" id="Req_Lanes" name="Req_Lanes" value='<?=json_encode($Req_Lanes )?>' />
 <input type="hidden" id="is_club" name="is_club" value='<?=json_encode($is_club )?>' />

<div class="calendar-form">
<?php $form = ActiveForm::begin(['id' => 'calendar-form']); ?>
<div class="row">
    <div class="col-xs-6 col-sm-2 col-md-3 col-lg-2 col-xl-1 " <?php if($model->isNewRecord) {echo 'style="display:none"';} ?>>
        <?= $form->field($model, 'calendar_id')->textInput(['readonly'=>true,'maxlength'=>true]) ?>
    </div>
    <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2 col-xl-1 ">
    <?php   echo $form->field($model, 'event_date')->widget(DatePicker::classname(), [
                'options' => ['placeholder' => 'Event Date'],
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true ] ] ); ?>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 col-xl-2">
    <?= $form->field($model, 'club_id')->DropDownList($ary_club).PHP_EOL; ?>
    </div>
    <div class="col-xs-9 col-sm-8 col-md-6 col-lg-5 col-xl-5">
	<?php if(yii::$app->controller->hasPermission('calendar/shoot')) {
		 $ary_fac = ArrayHelper::map(agcFacility::find()->where(['active'=>1])->orderBy(['name'=>SORT_ASC])->asArray()->all(), 'facility_id', 'name');
		} else {
		 $ary_fac = ArrayHelper::map(agcFacility::find()->where(['active'=>1])->andWhere(['not',['like', 'name', '%Shooting Bay%', false]])->orderBy(['name'=>SORT_ASC])->asArray()->all(), 'facility_id', 'name');
		}
		echo $form->field($model, 'facility_id')->dropDownList($ary_fac,['value'=>json_decode($model->facility_id),'prompt'=>'Select', 'id'=>'agccal-facility_id','multiple'=>true, 'size'=>false]).PHP_EOL;
	?>
    </div>
    <div class="col-xs-3 col-sm-4 col-md-2 col-lg-2 col-xl-2" id="Div_Lanes_Req" <?php if($allowLanes==false) { echo ' style="display: none;"';} ?>>
        <?= $form->field($model, 'lanes_requested')->textInput(['maxlength'=>true]) ?>
    </div>
    <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4 col-xl-2">
        <?= $form->field($model, 'event_name')->textInput(['maxlength'=>true]) ?>
    </div>
    <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2 col-xl-2">
        <?= $form->field($model, 'keywords')->textInput(['maxlength'=>true]) ?>
    </div>
    <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2 col-xl-2">
        <?= $form->field($model, 'start_time')->widget(TimePicker::classname(),['options'=>['class'=>'form-control'],
            'pluginEvents' => [ "change" => "function(e){ OpenRange(); }", ]]); ?>
    </div>
    <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2 col-xl-2">
        <?= $form->field($model, 'end_time'  )->widget(TimePicker::classname(), ['options'=>['class'=>'form-control'],
          'pluginEvents' => [ "change" => "function(e){ OpenRange(); }", ]]); ?>
    </div>
    <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2 col-xl-2">
    <?= $form->field($model, 'event_status_id')->DropDownList($model->isNewRecord ? [0=>'']: yii::$app->controller->actionGetEventTypes($model->club_id,true));     ?>
    </div>
    <?php
	if (yii::$app->controller->hasPermission('calendar/close')) {
		$ary_range = ArrayHelper::map(agcRangeStatus::find()->where(['active'=>1])->orderBy(['display_order'=>SORT_ASC])->asArray()->all(), 'range_status_id', 'name');
	} else {
		$ary_rng = agcRangeStatus::find()->where(['active'=>1])->orderBy(['display_order'=>SORT_ASC])->asArray()->all();
		$ary_range=[];
		foreach ($ary_rng as $rng_stat) {
			if($rng_stat['restricted']==0){
				$ary_range[$rng_stat['range_status_id']]=$rng_stat['name'];
			} else {
				if ($model->range_status_id==$rng_stat['range_status_id']) {
					$ary_range[$rng_stat['range_status_id']]=$rng_stat['name'];
				}
			}
		}
	} ?>
	<div class="col-xs-4 col-sm-2">
		<?= $form->field($model, 'range_status_id')->DropDownList($ary_range,['value'=> $model->range_status_id]).PHP_EOL; ?>
	</div>
	<div class="col-xs-4 col-sm-2 col-md-2 col-lg-2 col-xl-2">
        <?= $form->field($model, 'active')->DropDownList(['1'=>'Yes','0'=>'No'],['value'=> $model->isNewRecord ? 1 : $model->active ]) ?>
    </div>
    <div class="col-xs-4 col-sm-2 col-md-2 col-lg-2 col-xl-2" <?php if(!yii::$app->controller->hasPermission('calendar/approve')) {echo 'style="display:none"';} ?> >
        <?= $form->field($model, 'approved')->DropDownList(['1'=>'Yes','0'=>'No']) ?>
    </div>
    <div class="col-xs-4 col-sm-2 col-md-2 col-lg-2 col-xl-2">
        <?= $form->field($model, 'poc_badge')->textInput(['maxlength'=>true,'readonly'=>yii::$app->controller->hasPermission('calendar/all')? false : true]) ?>
    </div>
    <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2 col-xl-2">
        <?= $form->field($model, 'date_requested')->textInput(['readonly'=>true,'maxlength'=>true]).PHP_EOL ?>
    </div>
</div>

<div class="row" style="background-color: silver; padding-left: 15px">
<?php //if ($Div_recur<>'') { echo $form->field($model, 'recur_every')->hiddenInput(['value'=>0])->label(false).PHP_EOL; } ?>

<div id="Div_recur" <?=$Div_recur?>>

<div class="col-xs-12" style="background-color: silver"> <p> Recurring Event:</p>  </div>
    <div  class="col-xs-12 col-sm-12 col-md-7 col-lg-6" style="background-color: silver; border:thin solid black;" >
        <div class="row">
<!--####  Main Select####################### -->
            <div class="col-xs-3 col-sm-3 col-md-2" style="padding:5px 5px;" >
                <input type="radio" id="daily" name="pat_type" value="daily" <?=@$sel_d ?>> <label for="daily">daily</label> <br />
                <input type="radio" id="weekly" name="pat_type" value="weekly" <?=@$sel_w ?>> <label for="weekly">weekly</label> <br />
                <input type="radio" id="monthly" name="pat_type" value="monthly" <?=@$sel_m ?>> <label for="monthly">monthly</label> <br />
                <input type="radio" id="yearly" name="pat_type" value="yearly" <?=@$sel_y ?>> <label for="yearly">yearly</label> <br />
            </div>
<!--####  Daily ############################ -->
            <div id="pattern_day" class="col-xs-9 col-sm-9 col-md-10" style="padding:5px 5px; display: none">
                <input type="radio" id="pat_day_x" name="pat_day" value="d" > Every
                <select id="pat_daily_n" name="pat_daily"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option></select> day(s) <br />
                <input type="radio" id="pat_day_e" name="pat_day" value="wd"> Every weekday
            </div>
<!--####  Weekly ########################### -->
            <div id="pattern_week" class="col-xs-9 col-sm-9 col-md-10" style="padding:5px 5px; display: none">
                Repeats every <select id="pat_week_n" name="pat_week_n"> <option value="1">1</option> <option value="2">2</option> <option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option>
                </select> week(s) on: <br />
                <div class="row">
                <div class="col-xs-4 col-sm-3"><input type="checkbox" id="pat_da_mon" name="pat_da_mon" value="mon" <?=$chkMon ?>> Monday</div>
                <div class="col-xs-4 col-sm-3"><input type="checkbox" id="pat_da_tue" name="pat_da_tue" value="tue" <?=$chkTue ?>> Tuesday</div>
                <div class="col-xs-4 col-sm-3"><input type="checkbox" id="pat_da_wed" name="pat_da_wed" value="wed" <?=$chkWed ?>> Wednesday</div>
                </div>
				<div class="row">
				<div class="col-xs-4 col-sm-3"><input type="checkbox" id="pat_da_thu" name="pat_da_thu" value="thu" <?=$chkThu ?>> Thursday</div>
                <div class="col-xs-4 col-sm-3"><input type="checkbox" id="pat_da_fri" name="pat_da_fri" value="fri" <?=$chkFri ?>> Friday</div>
                <div class="col-xs-4 col-sm-3"><input type="checkbox" id="pat_da_sat" name="pat_da_sat" value="sat" <?=$chkSat ?>> Saturday</div>
                </div>
				<div class="row">
				<div class="col-xs-4 col-sm-3"><input type="checkbox" id="pat_da_sun" name="pat_da_sun" value="sun" <?=$chkSun ?>> Sunday</div>
                </div>
            </div>
<!--####  Monthly ########################## -->
            <div id="pattern_month" class="col-xs-9 col-sm-9 col-md-10" style="padding:5px 5px; display: none">
                <input type="radio" id="pat_mon_a" name="pat_mon_by" value="date" > Day
                <select id="pat_mon_x" name="pat_mon_x">
                    <option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option>
                </select> of Every
                <select id="pat_mon_m" name="pat_mon_m" >
                    <option value="1">1</option> <option value="2">2</option> <option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option>
                </select> month(s)
                <br />
                <input type="radio" id="pat_mon_b" name="pat_mon_by" value="day"> The
                <select id="pat_mon_wk" name="pat_mon_wk">
                    <option value="first">First</option><option value="second">Second</option><option value="third">Third</option><option value="fourth">Fourth</option> <!-- <option value="last">Last</option> -->
                </select>
                <select id="pat_mon_day" name="pat_mon_day">
                    <option value="monday">Monday</option>
                    <option value="tuesday">Tuesday</option>
                    <option value="wednesday">Wednesday</option>
                    <option value="thursday">Thursday</option>
                    <option value="friday">Friday</option>
                    <option value="saturday">Saturday</option>
                    <option value="sunday">Sunday</option>
                </select> of Every
                <select id="pat_mon_n" name="pat_mon_n">
                    <option value="1">1</option> <option value="2">2</option> <option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option>
                </select> month(s)
            </div>
<!--####  Yearly ############################ -->
            <div id="pattern_year" class="col-xs-9 col-sm-9 col-md-10" style="padding:5px 5px; display: none">
                Repeats Every <select id="pat_yr_n" name="pat_yr_n" ><option value="1">1</option><option disabled value="2">2</option></select> year(s) <br />
                <input type="radio" id="pat_yr_a" name="pat_yearly" value="date" /> On:
                <select id="pat_yr_mon" name="pat_yr_mon">
                    <option value="1">January</option><option value="2">Febuary</option><option value="3">March</option>
                    <option value="4">April</option><option value="5">May</option><option value="6">June</option>
                    <option value="7">July</option><option value="8">August</option><option value="9">September</option>
                    <option value="10">October</option><option value="11">November</option><option value="12">December</option>
                </select>
                <select id="pat_yr_mon_d" name="pat_yr_mon_d" >
                    <option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option>
                </select>
                <br />
                <input type="radio" id="pat_yr_b" name="pat_yearly" value="day"> On the:
                <select id="pat_yr_num" name="pat_yr_num">
                    <option value="first">First</option><option value="second">Second</option><option value="third">Third</option><option value="fourth">Fourth</option><!-- <option value="last">Last</option> -->
                </select>
                <select id="pat_yr_day" name="pat_yr_day">
                    <option value="mon">Monday</option>
                    <option value="tue">Tuesday</option>
                    <option value="wed">Wednesday</option>
                    <option value="thu">Thursday</option>
                    <option value="fri">Friday</option>
                    <option value="sat">Saturday</option>
                    <option value="sun">Sunday</option>
                </select> of    <select id="pat_yr_mon_a" name="pat_yr_mon_a">
                    <option value="1">January</option><option value="2">Febuary</option><option value="3">March</option>
                    <option value="4">April</option><option value="5">May</option><option value="6">June</option>
                    <option value="7">July</option><option value="8">August</option><option value="9">September</option>
                    <option value="10">October</option><option value="11">November</option><option value="12">December</option></select>
            </div>
        </div>
    </div>
	<div class="col-xs-12 col-sm-12 col-md-5 col-lg-6" style="background-color: silver;">
		<div class="row">
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-4" style="background-color: silver">
			<?php   echo $form->field($model, 'recurrent_start_date')->widget(DatePicker::classname(), [
				'options' => ['value' => (isset($model->recurrent_start_date) && $model->recurrent_start_date >0) ? $model->recurrent_start_date : '1 Jan'],
				'size' => 'xs',
				'pluginOptions' => [
					'autoclose'=>true,
					'format' => 'd M',
					'todayHighlight' => true ] ] ).PHP_EOL; ?>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-4" style="background-color: silver">
			<?php   echo $form->field($model, 'recurrent_end_date')->widget(DatePicker::classname(), [
					'options' => ['value' =>  (isset($model->recurrent_end_date) && $model->recurrent_end_date >0) ? $model->recurrent_end_date : '31 Dec'],
					'size' => 'xs',
					'pluginOptions' => [
						'autoclose'=>true,
						'format' => 'd M',
						'todayHighlight' => true ] ] ).PHP_EOL; ?>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-3 col=lg-3" style="background-color: silver">
			<?php echo $form->field($model, 'recur_every')->hiddeninput(['readonly'=>true,'maxlength'=>true])->label(false).PHP_EOL;
			if (in_array(1,$_SESSION['privilege'])) { //(yii::$app->controller->hasPermission('calendar/delete')) { ?>
				<?= $form->field($model, 'recurrent_calendar_id')->textInput(['readonly'=>true,'maxlength'=>true]).PHP_EOL ?>
			<?php } else { echo $form->field($model, 'recurrent_calendar_id')->hiddeninput(['readonly'=>true,'maxlength'=>true])->label(false).PHP_EOL; }?>
		</div>
		<?= $form->field($model, 'recur_week_days')->hiddenInput()->label(false).PHP_EOL; ?>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12">
			<p>* <u>Year is always current for Recurrent Start/End Dates</u><br>
			   ** <u>Dates can wrap arround: 1 Sep - 31 Mar</u></p>
			</div>
		</div>
	</div>
</div>

    <div class="col-xs-4 col-sm-1" style="background-color: pink<?php if(((isset($model->conflict)) && ($model->conflict==0)) || ($model->isNewRecord)) {echo '; display:none';} ?>" >
        <?= $form->field($model, 'conflict')->textInput(['readonly'=>true,'maxlength'=>true]).PHP_EOL ?>
    </div>
</div>

<?php if($model->isNewRecord) {
		echo $form->field($model, 'deleted')->hiddenInput(['value'=>0])->label(false).PHP_EOL;
	} else { echo '<div class="col-xs-4 col-sm-2">';
		if(yii::$app->controller->hasPermission('calendar/delete')) {
			if ($model->deleted=='1') {
				echo $form->field($model, 'deleted')->DropDownList(['1'=>'Yes','0'=>'No']).PHP_EOL;
			} else {
				echo '<br />'.Html::Button(($isMaster)?'Delete all Future Events':'Delete Event', ['class' =>($isMaster)?'btn btn-danger':'btn btn-warning ', 'onclick' => 'delMe();' ]).PHP_EOL;
			}
		} else {
			if ($model->deleted=='1') { echo "<p style='color:red;'><b>Event is Deleted</b></p>"; }
	}	echo '</div>';
	} ?>

<div class="row"><div class="col-xs-12" id="inpatt_msg"></div></div>
<div class="row"><div class="col-xs-12" id="error_msg"></div>
<div class="row">
	<div class="col-xs-0 col-md-6" id="error_msg"></div>
	<div class="col-xs-6 col-md-3 form-group" style="text-align: right;">
        <div id="searchng_cal_animation" style="display: none">
            <img src="<?=yii::$app->params['rootUrl']?>/images/animation_processing.gif" style="width: 100px">Searching..</h4>
        </div>
        <?= Html::Button('Check Availability', ['class' => 'btn btn-primary','id'=>'cal_check_avail', 'onclick' => 'OpenRange();' ]).PHP_EOL ?>
        <?= Html::submitButton($model->isNewRecord ? 'Create':'Update', ['class' => 'btn btn-secondary ','id'=>'cal_update_item']).PHP_EOL ?>
        <?php if (($isMaster) && (!$model->isNewRecord) && (yii::$app->controller->hasPermission('calendar/republish'))){ ?>
	</div>
    <div class="col-xs-6 col-md-3" id="div_hideRepub" style="background-color:whitesmoke; padding:8px; <?php if($hideRepub) {echo 'display:none;';} ?>">
		<center>
		RePublishing is ONLY needed if you modify the Recurring pattern.<br>
		<?= Html::submitButton('RePublish Upcomming', ['class' => 'btn btn-info','id'=>'re_pub','name' => 'republish','value'=>1 ]).PHP_EOL ?>
		</center>
		<?php } ?>
    </div>
</div>

<?php if($model->remarks) { ?>
<div class="row" >
    <div class="col-xs-12">
	<!--    <div class="col-xs-12 col-sm-12">
        <?= $form->field($model, 'remarks')->textInput(['readonly'=>true,'maxlength'=>true]).PHP_EOL ?>
    </div> -->
         <div class="row">
            <div class="col-xs-12">
                <h3> Remarks history </h3>
            </div>
            <div class="col-xs-12">

                <?= $this->render('/badges/_remarks',['remakrs_logs'=>json_decode($model->remarks,true) ] ).PHP_EOL ?>
            </div>
        </div>
	</div>
</div>
<?php } ?>
<?php ActiveForm::end(); ?>
</div>
</div>

<style>
  th { padding: 10px; text-align: center;}
  td { padding: 10px; white-space: nowrap }
  td { word-wrap: break-word word-break: break-all;  }
</style>
<script>
    const convertTime12to24 = (time12h) => {
      const [time, modifier] = time12h.split(' ');
      let [hours, minutes] = time.split(':');
      if (hours === '12') { hours = '00'; }
      if (modifier === 'PM') {
        hours = parseInt(hours, 10) + 12;
      }

      return `${hours}:${minutes}`;
    };

	Recure(true);
	runClub();

    document.getElementById("cal_update_item").disabled=true;
	$("#agccal-facility_id").select2({placeholder_text_multiple:'Choose Clubs',width: "100%"}).change(function(){ OpenRange(); });

  $("#re_pub").click(function (e) {
	  e.preventDefault();
	  document.getElementById("re_pub").disabled=true;
	  var myForm = $("#calendar-form");
	  $(myForm).submit();
  });

    $("#daily").change(function(e) {
        if(document.getElementById("daily").value=='daily'){
            $("#pattern_day").show(); $("#pattern_week").hide(); $("#pattern_month").hide(); $("#pattern_year").hide();
            document.getElementById("pat_day_x").checked=true;
        }
    });
    $("#pat_daily_n").change(function(e) { document.getElementById("pat_day_x").checked=true; });

    $("#weekly").change(function(e) {
        if(document.getElementById("weekly").value=='weekly'){
            $("#pattern_day").hide(); $("#pattern_week").show(); $("#pattern_month").hide(); $("#pattern_year").hide();
        }
    });

    $("#monthly").change(function(e) {
        if(document.getElementById("monthly").value=='monthly'){
            $("#pattern_day").hide(); $("#pattern_week").hide(); $("#pattern_month").show(); $("#pattern_year").hide();
            document.getElementById("pat_mon_a").checked=true;
        }
    });
    $("#pat_mon_x").change(function(e) { document.getElementById("pat_mon_a").checked=true; });
	$("#pat_mon_wk").change(function(e) { document.getElementById("pat_mon_b").checked=true; });

    $("#yearly").change(function(e) {
        if(document.getElementById("yearly").value=='yearly'){
            $("#pattern_day").hide(); $("#pattern_week").hide(); $("#pattern_month").hide(); $("#pattern_year").show();
            document.getElementById("pat_yr_a").checked=true;
        }
    });
    $("#pat_yr_mon").change(function(e) { document.getElementById("pat_yr_a").checked=true; });
    $("#pat_yr_num").change(function(e) { document.getElementById("pat_yr_b").checked=true; });

    $("#pat_day_x").change(function(e) {
        document.getElementById("pat_daily_n").disabled=false;
    });
    $("#pat_day_e").change(function(e) {
        document.getElementById("pat_daily_n").disabled=true;
    });

    $("#agccal-club_id").change(function(e) { runClub(); });

	$("#agccal-event_name").change(function(e) { OpenRange(); });

	function runClub() {
		var my_url = '<?=yii::$app->params['rootUrl']?>/calendar/get-event-types?event_club_id='+document.getElementById("agccal-club_id").value+'&is_sel='+document.getElementById("agccal-event_status_id").value+"&is_new_rec="+<?php if($model->isNewRecord){echo '1';} else {echo '0';} ?>;
		//console.log(my_url);
		jQuery.ajax({
			method: 'GET',
			crossDomain: false,
			dataType: 'json',
			url: my_url,
			success: function(responseData, textStatus, jqXHR) {
				//console.log("success ");
				document.getElementById("agccal-event_status_id").innerHTML = responseData;
			},
			error: function (responseData, textStatus, errorThrown) {
				console.log("error ");
				console.log(responseData.responseText);
			},
		});

	};

    function delMe() {
		var formData = $("#agccal").serializeArray();
		jQuery.ajax({
			method: 'POST',
			crossDomain: false,
			data: formData,
			dataType: 'json',
			url: '<?=yii::$app->params['rootUrl']?>/calendar/delete?id='+document.getElementById("agccal-calendar_id").value<?php if(isset($_SERVER['HTTP_REFERER'])) { if($isMaster) { ?>+'&type=m' <?php }
			if(strpos($_SERVER['HTTP_REFERER'],'recur')) { ?>+'&redir=recur' <?php } elseif(strpos($_SERVER['HTTP_REFERER'],'conflict')) { ?>+'&redir=conflict' <?php }  } ?>,
			success: function(responseData, textStatus, jqXHR) {
				console.log("success ");
			},
			error: function (responseData, textStatus, errorThrown) {
				console.log("error ");
			},
		});
	};

    function Recure(load=true) {
        console.log("Loading Pattert");
        if(load) {
            try {
            var rec_pat = JSON.parse($("#agccal-recur_week_days").val());
            console.log(rec_pat);

            if(rec_pat.daily){
                document.getElementById("daily").checked=true;
                $("#pattern_day").show();
                if(rec_pat.daily=='wd'){
                    document.getElementById("pat_day_e").checked=true;
                    document.getElementById("pat_daily_n").disabled=true;
                }else{
                    document.getElementById("pat_day_x").checked=true;
                    document.getElementById("pat_daily_n").value=rec_pat.daily;
                }
            }
            else if(rec_pat.weekly){
                document.getElementById("weekly").checked=true;
                $("#pattern_week").show();
                document.getElementById("pat_week_n").value = rec_pat.weekly;
                //for each -- select
                for (var i = 0; i < rec_pat.days.length; i++) {
                    if(rec_pat.days[i]=='mon'){document.getElementById("pat_da_mon").checked=true;}
                    if(rec_pat.days[i]=='tue'){document.getElementById("pat_da_tue").checked=true;}
                    if(rec_pat.days[i]=='wed'){document.getElementById("pat_da_wed").checked=true;}
                    if(rec_pat.days[i]=='thu'){document.getElementById("pat_da_thu").checked=true;}
                    if(rec_pat.days[i]=='fri'){document.getElementById("pat_da_fri").checked=true;}
                    if(rec_pat.days[i]=='sat'){document.getElementById("pat_da_sat").checked=true;}
                    if(rec_pat.days[i]=='sun'){document.getElementById("pat_da_sun").checked=true;}
                }

            }
            else if(rec_pat.monthly){
                document.getElementById("monthly").checked=true;
                $("#pattern_month").show();
                if(rec_pat.monthly=='date'){
                    document.getElementById("pat_mon_a").checked=true;
                    document.getElementById("pat_mon_x").value = rec_pat.day;
                    document.getElementById("pat_mon_m").value = rec_pat.every;
                } else {
                    document.getElementById("pat_mon_b").checked=true;
                    document.getElementById("pat_mon_wk").value=rec_pat.when;
                    document.getElementById("pat_mon_day").value=rec_pat.day;
                    document.getElementById("pat_mon_n").value = rec_pat.every;
                }
            }
            else if(rec_pat.yearly){
                document.getElementById("yearly").checked=true;
                $("#pattern_year").show();
                $("#pat_yr_n").val(rec_pat.every);
                if(rec_pat.yearly=='date'){
                    document.getElementById("pat_yr_a").checked=true;
                    document.getElementById("pat_yr_mon").value = rec_pat.mon;
                    document.getElementById("pat_yr_mon_d").value = rec_pat.day;
                } else {
                    document.getElementById("pat_yr_b").checked=true;
                    document.getElementById("pat_yr_num").value = rec_pat.on;
                    document.getElementById("pat_yr_day").value = rec_pat.day;
                    document.getElementById("pat_yr_mon_a").value = rec_pat.of;
                }
            }
        }  catch(err){}
        }

    }

    $("#pat_yr_mon").change(function(e) {
        var mon = parseInt(e.target.value); var cnt = 0; var str='';
        const thirtyone = [1,3,5,7,8,10,12];
        const thirty = [4,6,9,11];
        if ( thirtyone.indexOf(mon) != -1 ) { cnt=31; }
        else if (thirty.indexOf(mon) != -1 ) { cnt=30 ;}
        else { cnt=29; }
        for (i = 1; i < (cnt+1); i++) {
            str += "<option value="+i+">"+i+"</option>";
        }
        document.getElementById("pat_yr_mon_d").innerHTML = str;
    });

    $("#agccal-lanes_requested").change(function(e) {
        OpenRange();
    });

    $("#agccal-event_date").change(function(e) {
        OpenRange();
    });

    function OpenRange() {
        console.log('Run: OpenRange 576');
		$("#div_hideRepub").hide();
		$("#error_msg").html('');

		var is_dirty=false; var dirty_word='';
		var dirty = JSON.parse($('#bad_words').val());
		var chk_event_name = $("#agccal-event_name").val().toUpperCase().split(" ");
		chk_event_name.forEach(function(name) {
			if(dirty.indexOf(name) >= 0) {
				dirty_word +=name+', ';
				is_dirty=true;
			}
		});
		if (is_dirty){
			document.getElementById("cal_update_item").disabled=true;
			$("#error_msg").html('<center><p style="color:red;">Do not to use Club Names or Acronyms in the Event Name. &nbsp;Found: '+dirty_word.slice(0,-2)+'</p></center>');
			return;
		}

        var OnlyOneLane=0; var available_lanes=0; var fa_name=''; var reqLanes ='';
		var facil_ids = $("#agccal-facility_id").val();
		var Req_Lanes = JSON.parse($("#Req_Lanes").val());
		console.log(facil_ids);
		if(facil_ids.length < 1) {
			document.getElementById("cal_update_item").disabled=true;
			$("#error_msg").html('<center><p style="color:red;">Please Choose a Facility.</p></center>');
			return;
		}

        facil_ids.forEach(function(facil_id) {
			if ((Req_Lanes[facil_id]) && (Req_Lanes[facil_id].available_lanes > 0)) {
				OnlyOneLane ++;
				available_lanes =  parseInt(JSON.parse($("#Req_Lanes").val())[facil_id].available_lanes);
				console.log(JSON.parse($("#Req_Lanes").val())[facil_id]);
				fa_name += JSON.parse($("#Req_Lanes").val())[facil_id]['name']+', ';
				$("#Div_Lanes_Req").show();
			} else {

			}
		});
		fa_name=fa_name.slice(0, -2);

		if(OnlyOneLane > 1) {
			document.getElementById("cal_update_item").disabled = true;
            $("#error_msg").html('<center><p style="color:red;"><b>App only allows one range with lanes. You will have to make another event. Found: '+fa_name+'</b></p></center>');
            return;
		} else if(OnlyOneLane == 1) {
			var lReq = parseInt($("#agccal-lanes_requested").val());
			if ( lReq == 0 || lReq > available_lanes) {
				document.getElementById("cal_update_item").disabled=true;
				$("#error_msg").html('<center><p style="color:red;"><b>Please Choose # of lanes requested. No more than ' + available_lanes + '</b></p></center>');
				return;
			}

			reqLanes = $("#agccal-lanes_requested").val();
			if (reqLanes) {reqLnN = parseInt(reqLanes); reqLanes = '&lanes='+reqLanes;}
			else {$("#error_msg").html('<center><p style="color:red;"><b>Please Provide how many Lanes requested.</b></p></center>');return;}
		} else {
			$("#Div_Lanes_Req").hide();
			$("#agccal-lanes_requested").val(0);
		}
	console.log('found: '+OnlyOneLane+' - '+fa_name+', Lanes: '+available_lanes+', Requested: '+reqLanes);

        var reqStart = convertTime12to24($("#agccal-start_time").val());
        var reqStop  = convertTime12to24($("#agccal-end_time").val());
        //console.log('checking start Time:' + reqStart +' - ' + reqStop);
        if (reqStart >= reqStop) {
            document.getElementById("cal_update_item").disabled = true;
            $("#error_msg").html('<center><p style="color:red;"><b>Event Start Time Must be before End Time.</b></p></center>');
            return;
        }

        if ($("#agccal-deleted").val() != '1') {
            document.getElementById("cal_update_item").disabled = true;
    <?php if (($isMaster) && (!$model->isNewRecord)) { ?>   document.getElementById("re_pub").disabled=true; <?php } ?>
            $("#error_msg").html('');reqLnN=0; var reqcal_id='';

            var reqFacl = $("#agccal-facility_id").val();
            var reqDate = $("#agccal-event_date").val();
            if (!reqDate) {$("#error_msg").html('<center><p style="color:red;"><b>Please verify Date.</b></p></center>');return;}
            var reqStart = $("#agccal-start_time").val();
            var reqStop = $("#agccal-end_time").val();

			var req_pat = '';
			var pat_type = document.getElementsByName("pat_type");
			pat_type.forEach((pType) => { if (pType.checked) { req_pat = pType.value; } });

			var req_stat = $("#agccal-event_status_id").val();
            var req_cal_id = $("#agccal-calendar_id").val();
            if (req_cal_id) {reqcal_id = '&id='+req_cal_id;}
            $("#searchng_cal_animation").show(500);

	var myUrl = "<?=yii::$app->params['rootUrl']?>/calendar/open-range?eDate="+reqDate+"&start="+reqStart+"&stop="+reqStop+"&facility=["+reqFacl+']'+reqLanes+"&id="+req_cal_id+"&pattern="+req_pat+"&e_status="+req_stat;
	//console.log(myUrl+"&tst=1");
			var calendarFormData = $("#calendar-form").serializeArray();
            jQuery.ajax({
                method: 'POST',
                dataType:'json',
				data: calendarFormData,
                url: myUrl,
                success: function(responseData, textStatus, jqXHR) {
            //      console.log('success:379');
			//      console.log(responseData);
                    $("#searchng_cal_animation").hide(500);

					if(responseData.chkpat=='error') {
						 $("#inpatt_msg").html('<center><p style="color:red;"><b>'+responseData.inPattern+'</b></p></center>');
						 document.getElementById("cal_update_item").disabled=false;
					} else {
						if(responseData.inPattern){
							if(reqDate >= '<?=date("Y-m-d",strtotime($getNowTime))?>')  { var msg_never=''; } else { var msg_never='<br>(Dates in the past will never be in scope)'; }
							$("#inpatt_msg").html('<center><p><b style="color:orange;">'+responseData.inPattern+'</b>'+msg_never+'</p></center>');
						} else {
							$("#inpatt_msg").html('');
						}

						if((responseData.status=='success')&& (responseData.chkpat=='success')) {
							//console.log('success:530');
							$("#error_msg").html('<center>'+responseData.msg+'</center>');
							document.getElementById("cal_update_item").disabled=false;
		<?php if (($isMaster) && (!$model->isNewRecord)) { ?>   document.getElementById("re_pub").disabled=false; <?php } ?>
							if ( document.getElementById("cal_update_item").classList.contains('btn-secondary') ){
								document.getElementById("cal_update_item").classList.add('btn-success');
								document.getElementById("cal_update_item").classList.remove('btn-secondary');
							}

						} else {
							//console.log('Error:621');
						   // console.log(responseData);
							$("#error_msg").html('<center>'+responseData.msg+'</center>');

							if ( document.getElementById("cal_update_item").classList.contains('btn-success') ){
								document.getElementById("cal_update_item").classList.add('btn-secondary');
								document.getElementById("cal_update_item").classList.remove('btn-success');}
						}

						if(responseData.data) { //parseInt()
							if (available_lanes != 0) {var resp_str='<th>Lanes ('+available_lanes+')</th>';} else {var resp_str='';}
							$("#error_msg").html( $("#error_msg").html() + '<br><center><table id="cal_items" width=100% border=1><thead><tr><th>ID</th><th>Range</th><th>Club</th><th>Name</th><th>Start</th><th>Stop</th><th>Event Status</th><th>Range Status</th><th>Type</th>'+resp_str+'</tr></thead></table></center>');
							var table = document.getElementById("cal_items");
							console.log(responseData.data);
							for( var j = 0; j < responseData.data.length; j++ ){
								var row = table.insertRow();
								var cell1 = row.insertCell(0); var cell2 = row.insertCell(1); var cell3 = row.insertCell(2); var cell4 = row.insertCell(3); var cell5 = row.insertCell(4);var cell6 = row.insertCell(5);var cell7 = row.insertCell(6);var cell8 = row.insertCell(7);var cell9 = row.insertCell(8);
								if (available_lanes != 0) {var cell10 = row.insertCell(9);}

								// Add some text to the new cells:
								cell1.innerHTML = '<a href="/calendar/update?id='+responseData.data[j].cal_id+'" target="_blank">'+responseData.data[j].cal_id+'</a>';
								cell2.innerHTML = responseData.data[j].fac_name;
								cell3.innerHTML = responseData.data[j].club;
								cell4.innerHTML = responseData.data[j].name;
								cell5.innerHTML = responseData.data[j].start;
								cell6.innerHTML = responseData.data[j].stop;
								cell7.innerHTML = responseData.data[j].eve_status_name;
								cell8.innerHTML = responseData.data[j].rng_status_name;
								cell9.innerHTML = responseData.data[j].type_n;
								if (available_lanes != 0) {cell10.innerHTML = responseData.data[j].lanes;}
								//console.log(responseData.data[j].name);
							}
						}
					}
                },
                error: function (responseData, textStatus, errorThrown) {
                    $("#searchng_cal_animation").hide(500);
                    console.log('Error:704');
					$("#error_msg").html('<center><p style="color:red;"><b>'+responseData.responseText+'</b></p></center>');
                  //  console.log(responseData);
                    if ( document.getElementById("cal_update_item").classList.contains('btn-success') ){
                        document.getElementById("cal_update_item").classList.add('btn-secondary');
                        document.getElementById("cal_update_item").classList.remove('btn-success');}
                },
            });
        } else {
            console.log('Marked as Deleted:713');
            document.getElementById("cal_update_item").disabled=false;
            if ( document.getElementById("cal_update_item").classList.contains('btn-secondary') ){
                document.getElementById("cal_update_item").classList.add('btn-success');
                document.getElementById("cal_update_item").classList.remove('btn-secondary');
            }
        }
    }

</script>
