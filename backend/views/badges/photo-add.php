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
$agent = $_SERVER['HTTP_USER_AGENT'];
?>
<div class="container">
<ul>
<li>Please use a white background.</li>
<li>Try to take a passport style photo.</li>
</ul>

<div class="row">
  <div class="col-xs-12" id="uploadingInfoError" style="display: none;">
      <div class="alert alert-danger alert-dismissable fade in" id="error_info">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		<br /> <br/ ><p> If you have a camera, <b>grant the webpadge access to you camera</b> and reload the page.</p>
		<p>- or - <b>Please see associat.</b></p>
      </div>
    </div>
</div>
<?php if (str_contains($agent, 'Windows') || str_contains($agent, 'Android')) { ?>
<div class="row" id="video_block">
	<div class="col-md-12 text-center">
		<video accept="image/*" capture="camera" id="my_photo" style="width:80%;"></video>
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
<br /> If you change your video source, hold picture to refresh...
<?php } else {
		echo "<p>Unsupported Device</p>".$agent.'<p><a href="/badges/view?badge_number='.$_GET['badge'].'">Back to User info</a></p>';
} ?>

</div>

<script>
  (function() {
	"use strict";
	var width = 600;
	var height = 0;		// This will be computed based on the input stream

	var myimg
	var video = document.querySelector("video"), canvas;
	var photo = null;
	var streaming = false;

<?php if (str_contains($agent, 'Windows') || str_contains($agent, 'Android')) { ?>
 function startup() {
    video = document.getElementById('my_photo');
    photo = document.getElementById('new_badge_photo');
    navigator.mediaDevices.getUserMedia({video: true, audio: false})
    .then((stream) => {
      video.srcObject = stream;
      video.play();
    })
    .catch((err) => {
      console.log("An error occurred: " + err);
    });

    video.addEventListener('canplay', (event) => {
      if (!streaming) {
        streaming = true;
      }
    }, false);
  }

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
		var csrf = $('meta[name="csrf-token"]').attr('content');
		$.ajax({
			type: "POST",
			url: "/badges/photo-add?badge=<?=$_GET['badge']?>",
			data: { imgBase64: myimg ,'_csrf-backend':csrf}
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
		var width = video.videoWidth
		, height = video.videoHeight;

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

	window.addEventListener('load', startup, false);
  })();

<?php } ?>
</script>
