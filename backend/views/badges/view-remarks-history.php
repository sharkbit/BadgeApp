<?php 

$this->title = 'Remarks History';
$this->params['breadcrumbs'][] = ['label' => 'Range Badges', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->badge_number, 'url' => ['/badges/view','badge_number'=>$model->badge_number]];

?>

<div class="row">
	<div class="col-xs-12">
		<?= $this->render('_view-tab-menu',['model'=>$model]) ?>
	</div>
	<div class="col-xs-12">
		<?php 
		$remakrs_logs = json_decode($model->remarks,true);
		if(!empty($remakrs_logs)) {
			rsort($remakrs_logs);
		}
		else {
			$remakrs_logs = null;
		}  
	?>
	</div>
	<div class="col-xs-12">
         <div class="row">
            <div class="col-xs-12">
                <h3> Remarks history </h3>
            </div>
            <div class="col-xs-12">
                <?=$this->render('_remarks',['remakrs_logs'=>$remakrs_logs])?>
            </div>
        </div>
    </div>
</div>
