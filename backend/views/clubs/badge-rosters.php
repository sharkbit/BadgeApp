<?php

use backend\models\clubs;
use yii\helpers\Html;
//use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
//use yii\bootstrap\Modal;

//use kartik\export\ExportMenu;
//use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\ClubsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Create Badge Rosters for Clubs';
$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url'=>['badge-rosters']];
?>
<div class="clubs-index" ng-controller="clubRosterpage">
    <div class="row"> 
        <idv class="col-xs-12" id="uploadingInfo" style="display: none;">
            <h4> <img src="<?php echo Yii::$app->params['rootUrl']; ?>/images/animation_processing.gif" style="width: 100px;">  Please wait while the Data is being exported.</h4>
        </idv>
        <div class="col-xs-8">
            <h2><?= Html::encode($this->title) ?></h2>
           <?php $form = ActiveForm::begin([
                'action' => ['/clubs/badge-rosters'],
                'method' => 'post',
                'id'=>'badgeRosterFormFilter'
            ]) ?>

			<?= $form->field($clubModel, 'club_id')->dropDownList((new clubs)->getClubList(), ['prompt'=>'All','id'=>'clubs-club_id']).PHP_EOL; ?>
            <input type="hidden" value="false" id="isXls" name="xls">
		</div>
		<div class="col-xs-8">
            <div class="form-group">
            <?= Html::a('<i class="fa fa-download" aria-hidden="true"></i> Export as CSV', [''], ['class' => 'btn btn-primary pull-left single-csv', 'style' => 'margin-right: 10px;' ]).PHP_EOL; ?>
            <?= Html::a('<i class="fa fa-download" aria-hidden="true"></i> Export All as CSV', [''], ['class' => 'btn btn-primary all-csv' ]).PHP_EOL; ?>
            <?= Html::a('<i class="fa fa-download" aria-hidden="true"></i> Export as Xls', [''], ['class' => 'btn btn-primary xls-export', 'style' => 'display : none;' ]).PHP_EOL; ?>
			<?= Html::a('<i class="fa fa-envelope " aria-hidden="true"></i> Send via Email', [''], ['class' => 'btn btn-primary email-csv', 'style' => 'margin-left: 60px;' ]).PHP_EOL; ?>
			</div>
            <?php ActiveForm::end(); ?> 
        </div>
    </div>
	<hr >
    <div class="row" id="roster_report">
      
    </div>
	<div class="row" >
<?php if(is_dir("files/rosters/")) {
		$myReports = scandir ("files/rosters");
		
		sort ($myReports);
		foreach ($myReports as $myFile) {
			if ( strpos ($myFile,".csv")) {
				echo "<div class='col-sm-6'><div class='form-group'><a href='/files/rosters/".$myFile."'>".$myFile."</a></div></div>".PHP_EOL;
			}
		}
	} else { echo "No Reports Found <br>".PHP_EOL; }
		?>
		</div>
	</div>
</div>
