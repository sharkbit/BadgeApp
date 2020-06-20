<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Clubs
*/
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Add Photo';
$this->params['breadcrumbs'][] = ['label' => 'Range Badges', 'url' => ['/badges/index']];
$this->params['breadcrumbs'][] = ['label' => $_GET['badge'], 'url' => ['/badges/view?badge_number='.$_GET['badge']]];
$this->params['breadcrumbs'][] = $this->title;

$csrfToken=Yii::$app->request->getCsrfToken();
?>

<div class="row">
  <div class="col-xs-12" id="uploadingInfoError" style="display: none;">
      <div class="alert alert-danger alert-dismissable fade in" id="error_info">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      </div>
    </div>
</div>
<div class="row" id="no_camera" style="display: none;">
	<div class="col-md-12 ">
	<p> Or Try the upload button</p>
	<p class="help-block help-block-error"></p>
	</div>
	<div class="col-md-6 ">
	<input type="file" id="file" accept="image/jpeg">
	<p class="help-block help-block-error"></p>
	</div>
	<div class="col-md-6 ">
	<button id="send_photo" class="btn btn-success btn-sm">Submit Photo</button>
	</div>
</div>
<div class="row" id="video_block">
	<div class="col-md-12 text-center"> 
		<!-- <div class="select" style="display: none;">
			<label for="audioSource">Audio source: </label><select id="audioSource"></select>
		</div> -->
		<div class="select">
			<label for="videoSource">Video source: </label> <select id="videoSource"></select>
		</div>

		<!-- <video muted autoplay></video> -->
		<video muted autoplay id="my_photo" style="width:80%; max-width:600px;"></video>
	</div>
	<div class="col-md-12 text-center">
		<button id="take_snapshots" class="btn btn-success btn-sm">Take Snapshots</button>
	</div>
</div>
<div class="row" id="photo_block" style="display: none;">
	<div class="col-md-12 text-center">
		<div id="new_badge_photo"> </div>
	</div>
	<div class="col-md-12 text-center">
		<button id="retake_photo" class="btn btn-primary btn-sm">Re-Take Photo</button>
		<button id="save_photo" class="btn btn-success btn-sm">Use Photo</button>
	</div>
</div>

<br> If you change your video source, hold picture to refresh...  

<!-- <a href="https://simpl.info/getusermedia/sources/" target="_blank">test</a> -->
<script src="/js/GetMedia.js"></script>
<script>
  (function() {
	"use strict";
	var myimg
	var video = document.querySelector("video"), canvas;
	var localStream;

    $("#take_snapshots").click(function(event) {
      console.log("take photo clicked");
      takeSnapshot();
    });

    $("#retake_photo").click(function(event) {
	    $("#photo_block").hide();
        $("#video_block").show();
    });

	$("#save_photo").click(function(event) {
		console.log("saving...");
console.log( "using: "+ JSON.stringify(myimg).length );
		$.ajax({
			type: "POST",
			url: "/badges/photo-add?badge=<?=$_GET['badge']?>",
			data: { imgBase64: myimg }
		}).done(function(o) {
			console.log("saved");
			window.location.href = "/badges/photo-crop?badge=<?=$_GET['badge']?>";
		});
    });

    /**
     *  generates a still frame image from the stream in the <video>
     *  appends the image to the <body>
     */
    var takeSnapshot = function () {
		myimg = document.querySelector("my_photo");
		var context;
		var width = video.offsetWidth
		, height = video.offsetHeight;

		canvas = canvas || document.createElement("canvas");
		canvas.width = width;
		canvas.height = height;

		context = canvas.getContext("2d");
		context.drawImage(video, 0, 0, width, height);

		myimg = canvas.toDataURL("image/jpeg");

		$("#photo_block").show();
		$("#video_block").hide();
		new_badge_photo.append(canvas);
    }
  })();

</script>
