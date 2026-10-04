<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\daterange\DateRangePicker;
use backend\models\clubs;
use backend\models\agcFacility;
use backend\models\agcEventStatus;
use backend\models\agcRangeStatus;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

$this->title = "AGC Calendar Events";
$feedFilters = [];
foreach (['key_words', 'club_id', 'facility_id', 'event_status_id', 'range_status_id'] as $filterAttribute) {
	$filterValue = $searchModel->$filterAttribute;
	if (is_array($filterValue)) {
		$filterValue = array_values(array_filter($filterValue, static function ($value) {
			return is_scalar($value) && (string)$value !== '';
		}));
		if ($filterValue) {
			$feedFilters[$filterAttribute] = $filterValue;
		}
	} elseif (is_scalar($filterValue) && trim((string)$filterValue) !== '') {
		$feedFilters[$filterAttribute] = $filterValue;
	}
}
$clubValues = $feedFilters['club_id'] ?? [];
$clubValues = is_array($clubValues) ? $clubValues : [$clubValues];
$clubIds = [];
foreach ($clubValues as $clubValue) {
	$clubId = is_scalar($clubValue) ? filter_var($clubValue, FILTER_VALIDATE_INT) : false;
	if ($clubId !== false && $clubId > 0) {
		$clubIds[] = $clubId;
	}
}
$clubIds = array_values(array_unique($clubIds));
$hasClubFilter = !empty($clubIds);
if ($hasClubFilter) {
	$feedFilters['club_id'] = $clubIds;
	$feedUrl = Url::to(['/calendar/rss', 'AgcCalSearch' => $feedFilters], true);
	$rssFeedUrl = Url::to(['/calendar/rss', 'format' => 'rss', 'AgcCalSearch' => $feedFilters], true);
}
$facilityNamesById = ArrayHelper::map(
	agcFacility::find()->select(['facility_id', 'name'])->asArray()->all(),
	'facility_id',
	'name'
);
?>

<main>
<div class="work-credits-form">
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
		<div class="col-xs-6 col-sm-3">
			 <?= $form->field($searchModel, 'event_status_id')->DropDownList((new agcEventStatus)->getStatusList(),['prompt'=>'Any','value'=> $searchModel->event_status_id]).PHP_EOL; ?>
		</div>
		<div class="col-xs-6 col-sm-3">
			<?= $form->field($searchModel, 'range_status_id')->DropDownList((new agcRangeStatus)->getStatusList(),['prompt'=>'Any','value'=> $searchModel->range_status_id]).PHP_EOL; ?>
		</div>
		<div class="col-sm-12 col-md-6">
			<?= $form->field($searchModel, 'club_id')->dropDownList((new clubs)->getClubList(false,false,true), ['prompt'=>'Any','value'=> $searchModel->club_id,'multiple'=>true]).PHP_EOL; ?>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-6">
			<?= $form->field($searchModel, 'facility_id')->dropDownList((new agcFacility)->getFacilityList(),['prompt'=>'Any','value'=> $searchModel->facility_id,'multiple'=>true, 'size'=>false]).PHP_EOL; ?>
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
			<?= $form->field($searchModel, 'pagesize')->dropDownlist([ 20 => 20, 50 => 50, 100 => 100],['value'=>$searchModel->pagesize ,'id' => 'pagesize'])->label('Page size: ') ?>
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
<div class="row">
	<div class="col-xs-12">
		<?php if ($hasClubFilter) { ?>
			Subscription URLs: &nbsp;
			<?= Html::a('Open calendar import feed (.ics)', $feedUrl, ['target' => '_blank', 'rel' => 'noopener']) ?>
			&nbsp;|&nbsp;
			<?= Html::a('Open RSS 2.0 feed', $rssFeedUrl, ['target' => '_blank', 'rel' => 'noopener']) ?>
		<?php } else { ?>
			Sponsor needed for subscription URLs.
		<?php } ?>
	</div>
</div>

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
			<a style="text-decoration: none; font-weight: bold;" href="/calendar/viewitem?calendar_id=<?=$model->calendar_id ?>" target='Cal'><?=Html::encode($model->event_name) ?></a>
			<br/><?=$model->clubs->club_name ?><br/>
			<?php
				$facilityIds = json_decode($model->facility_id, true);
				$facilityNames = [];
				if (is_array($facilityIds)) {
					foreach ($facilityIds as $facilityId) {
						if (isset($facilityNamesById[$facilityId])) {
							$facilityNames[] = $facilityNamesById[$facilityId];
						}
					}
				}
				sort($facilityNames);
				echo Html::encode(implode(', ', $facilityNames));
			?>
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

<script>
  $("#agccalsearch-club_id").select2({placeholder_text_multiple:'Choose Clubs',width: "100%"});
  $("#agccalsearch-facility_id").select2({placeholder_text_multiple:'Choose Facilitys',width: "100%"});
</script>

</main>