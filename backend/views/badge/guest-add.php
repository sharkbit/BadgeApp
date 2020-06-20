<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Guest */

$this->title = 'T E S T';
$this->params['breadcrumbs'][] = ['label' => 'Guest', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="guest-create" ng-controller="CreateBadgeController">

    <h2><?= Html::encode($this->title) ?></h2>

    <?	
		//= $this->render('_form', [
		//  'model' => $model,
		//]) 
	?>

</div>


