<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use backend\models\Legelemail;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\WorkCreditsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Legeslative Contacts';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['legelemail/index']];

if (isset($_SESSION['pagesize'])) {
	$pagesize = $_SESSION['pagesize'];
} else {
	$pagesize=20;
}
$dataProvider->pagination = ['pageSize' => $pagesize];


//if(yii::$app->controller->hasPermission('Legelemail/delete')) {
	$myTemplate=' {update}  {delete} ';
//} elseif(yii::$app->controller->hasPermission('Legelemail/update')) {
//	$myTemplate=' {view}  {update} ';
//} else {$myTemplate='{view}';}


//echo $this->render('_search',['model'=>$searchModel,'violationsModel'=>$violationsModel]).PHP_EOL;
//echo $this->render('_view-tab-menu').PHP_EOL ;
?>
<?=$this->render('_view-tab-menu').PHP_EOL ?>
<h1><?= Html::encode($this->title) ?></h1>
<div class="Legelemail-index">

<?php //if(yii::$app->controller->hasPermission('violations/create')) { ?>
<?php $form = ActiveForm::begin(['action' => ['legelemail/import'],'id'=>'GuestForm','options' => ['enctype' => 'multipart/form-data']]); ?>
<div class="row">
<!--	<div class="col-xs-0 col-md-7" > </div>
	<div class="col-xs-8 col-md-3" >
		<?= $form->field($model, 'fileImport')->fileInput()->label(false).PHP_EOL; ?>
	</div>
	<div class="col-xs-4 col-md-2"  class="btn-group pull-right">
		<?= Html::submitButton('Import Contacts', ['class' =>'btn btn-primary','id'=>'upload_btn']).PHP_EOL;  ?>
		
	</div> -->
	<div class="btn btn-group pull-right"><?= Html::a('Create Contact', ['create'], ['class' => 'btn btn-success']).PHP_EOL;  ?></div>
</div>
<?php ActiveForm::end(); ?>
<?php // } ?>

<div class="col-xs-12 col-md-12">

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
		//	'display_order',
			'last_name',
			'first_name',
			'middle_name',
			'email',
			'title',
			'office',
			'committee',
			'district',
		//	'date_created',
		//	'date_modified',
			'is_active',
			
/*			[
				'attribute'=>'badge_reporter',
				//'format' => 'raw',
				'value'=>function ($model) {
					return yii::$app->controller->decodeBadgeName((int)$model->badge_reporter).' ('.$model->badge_reporter.')';
				}
			],
			[
				'attribute' => 'vi_type',
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'vi_type',
					['1'=>'Class 1','2'=>'Class 2','3'=>'Class 3','4'=>'Class 4'],['class'=>'form-control','prompt' => 'All']),
				'value'=> 'vi_type',
			],
			[
				'attribute' => 'badge_involved',
				'value' => function($model) {
					return yii::$app->controller->decodeBadgeName((int)$model->badge_involved).' ('.$model->badge_involved.')';
				},
			],
			'vi_rules',
			[
				'attribute' => 'vi_loc',
				'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'vi_loc',(new Legelemail)->getLocations(),['class'=>'form-control','prompt' => 'All']),
				'value'=> function($model, $attribute) {
					return $model->getLocations($model->vi_loc);
				},
			],
			[
				'attribute'=>'vi_date',
				'value'=>function($model) {
					return yii::$app->controller->pretydtg($model->vi_date);
				},
			], */
			[
				'header' => 'Actions',
				'class' => 'yii\grid\ActionColumn',
				'template'=>$myTemplate,
			/*	'buttons'=>[
					'view' => function ($url, $model) {
						return  Html::a(' <span class="glyphicon glyphicon-eye-open"></span>', ['view','id'=>$model->id],
						[	'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'View',
							'class'=>'edit_item',
						]);
					},
					'update' => function ($url, $model) {
						return  Html::a(' <span class="glyphicon glyphicon-pencil"></span>', ['update','id'=>$model->id],
						[	'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'Edit',
							'class'=>'edit_item',
						]);
					},
					'delete' => function($url,$model) {
						return  Html::a(' <span class="glyphicon glyphicon-trash"></span>', $url,
						[	'data-toggle'=>'tooltip',
							'data-placement'=>'top',
							'title'=>'Delete',
							'data' => [
								'confirm' => 'Are you sure you want to delete this item?',
								'method' => 'post',
							],
						]);
					},

				] */
			]
		]
	]); ?>
</div>
</div>
</div>

<script>
   /*          $("#uploadFile").on('submit',(function(e) {
            e.preventDefault();
       $('#uploadingInfo').show();
            $.ajax({
                url: '<?=yii::$app->params['rootUrl']?>/work-credits/import', // Url to which the request is send
                type: "POST",             // Type of request to be send, called as method
                data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
                contentType: false,       // The content type used when sending data to the server.
                cache: false,             // To unable request pages to be cached
                processData:false,      // To send DOMDocument or non processed data file it is set to false
                    xhr: function () {
                            var xhr = new window.XMLHttpRequest();
                            xhr.upload.addEventListener("progress", function (evt) {
                                if (evt.lengthComputable) {
                                    var percentComplete = evt.loaded / evt.total;
                                    percentComplete = parseInt(percentComplete * 100);
                                    console.log(percentComplete);
                                    //$('.myprogress').text(percentComplete + '%');
                                    //$('.myprogress').css('width', percentComplete + '%');
                                }
                            }, false);
                            return xhr;
                    },
                     success: function(responseData, textStatus, jqXHR) {
                        responseData =  JSON.parse(responseData);
                        console.log(responseData);
                        $('#uploadingInfo').hide();
                        if(responseData.status=='error-file') {
                            $("#uploadingInfo").hide();
                             $(".sucessbox").html('<idv class="col-xs-12""> <div class="alert alert-danger alert-dismissable fade in"> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> <strong> Error !</strong> '+responseData.remarks+'</div> </idv>');
                        }
                        else if(responseData.status=='success') {
                            $(".sucessbox").html('<idv class="col-xs-12""> <div class="alert alert-success alert-dismissable fade in"> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> <strong> Process Summary  </strong>  <br> <ul> <li> '+responseData.successful+' Work Credit records created.</li>     </ul></div> </idv>');
                        }
                    },
                    error: function (responseData, textStatus, errorThrown) {
                        $('#uploadingInfo').hide();
                        $(".sucessbox").html('<idv class="col-xs-12""> <div class="alert alert-danger alert-dismissable fade in"> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> <strong> Error </strong> an error occurred while processing your request pleae upload a valid formated data. </div> </idv>');
                    },
            }); */
        }));
</script>