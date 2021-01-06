<?php

use backend\controllers\BadgesController;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $model backend\models\Badges */

$this->title = $model->badge_number;
$this->params['breadcrumbs'][] = ['label' => 'Range Badges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$badge_info = yii::$app->controller->actionGetBadgeDetails($model->badge_number,true);
?>
<div class="badges-view">
    <div class="row" >
        <div class="col-xs-12">

    <?= $this->render('_view-tab-menu',['model'=>$model]) ?>

            <h3> Work Credit Details</h3>
            <div class="col-xs-12 col-sm-4 pull-right"><h4> Saved Credits <h4>
                <div class="info-box-credit">
                    <div class="info-box-icon aqua">
                        <span> <i class="flaticon flaticon-savings"></i>  </span>
                    </div>
                    <div class="info-box-details">
                        <span>
				<?php // "wcCurYr": "2018", "wcCurHr": 0, "wcLasYr": "2017", "wcLasHr": "314.50"
					echo $badge_info['wcCurYr'].": ".$badge_info['wcCurHr']."<br>";
					echo $badge_info['wcLasYr'].": ".$badge_info['wcLasHr']."<br>";
				?>
                        </span>
                    </div>
                </div>
                <div class="clearfix"> </div>
            </div>
            <div class="col-xs-12 col-sm-8">
            <?= Html::a('Add Work Credit',['work-credits/create','badge_number'=>$model->badge_number],['class'=>'btn btn-primary pull-right'])?>

            </div>
            <div class="col-xs-12 col-sm-8">

            <table class="table table-striped">
                <thead>
                  <tr>
                    <th>Project Name</th>
                    <th>Work Date</th>
                    <th> Authorized By</th>
                    <th>Work Hours</th>
                  </tr>
                </thead>
                <tbody>

                <?= ListView::widget([
                    'dataProvider' => $dataProvider,
                    'itemView' =>'_credit-table-view',
                    'layout' => "{summary}\n{items}\n <div class='clearfix'> </div> <div class='pull-right'>{pager}</div>",

                ]); ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>


<style>
.info-box-credit {
    width: 100%;
    float: left;
}
.info-box-icon {
    float: left;
    width: 30%;

}
.aqua {
    background: #00aff0;
    color: #fff;
}
i.fa.fa-user {
    font-size: 60px !important;
}
.info-box-details .head{
    padding: 4px;
    font-size: 16px;
    color: #262626;
}
.info-box-details {

}
.info-box-credit {
    background: #eee;
    border: 1px solid #00aff0;
}
.info-box-details {
    margin-left: 122px;
}
.info-box-details h4 {
    font-size: 16px;
}
.info-box-details span {
    font-size: 26px;
}
</style>