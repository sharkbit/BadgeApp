<?php

/* @var $this yii\web\View */
/* @var $model backend\models\Params */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Current RSO Report';
$this->params['breadcrumbs'][] = ['label' => 'RSO Reports', 'url' => ['rso-rpt/index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['rso-rpt/current']];
?>

<?=$this->render('_view-tab-menu').PHP_EOL ?>

<?=$this->render('_form',['model'=>$model]).PHP_EOL ?>
