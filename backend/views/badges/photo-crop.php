<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
$this->title = $isGuestPhoto ? 'Crop Guest Photo' : 'Crop Photo';
if ($isGuestPhoto) {
    $this->params['breadcrumbs'][] = ['label' => 'Banned', 'url' => ['/accounts/banned']];
} else {
    $this->params['breadcrumbs'][] = ['label' => 'Range Badges', 'url' => ['/badges/index']];
    $this->params['breadcrumbs'][] = ['label' => $photoTarget, 'url' => ['/badges/view', 'badge_number' => $photoTarget]];
}
$this->params['breadcrumbs'][] = $this->title;

$photoUrl = '/files/badge_photos/' . rawurlencode($photoFileName);
$addUrl = Url::to(['/badges/photo-add', 'badge' => $photoTarget]);
$saveUrl = Url::to(['/badges/photo-add', 'badge' => $photoTarget]);
$returnUrl = $isGuestPhoto
	? Url::to(['/accounts/banned'])
	: Url::to(['/badges/view', 'badge_number' => $photoTarget]);
?>
<style>
@import "/css/cropper.css";

img {
  max-width: 100%;
}
.row,
.preview {
  overflow: hidden;
}
.col {
  float: left;
}
.col-6 {
  width: 50%;
}
.col-3 {
  width: 25%;
}
.col-2 {
  width: 16.7%;
}
.col-1 {
  width: 8.3%;
}
</style>
    <div class="row">
      <div class="col col-1"> <p> </div>
	  <div class="col col-6">
		<img id="image" src="<?= Html::encode($photoUrl . '?dummy=' . rand(10000, 99999)) ?>" alt="Picture">
      </div>
	  <div class="col col-1"> <p> </div>
      <div class="col col-3">
        <div class="preview"></div>
      </div>
	  <div class="col col-1"> <p> </div>
    </div>
 	<div class="col-md-12 text-center">
		<button id="retake_photo" class="btn btn-primary btn-sm">Re-Take Photo</button>
		<button id="save_photo" class="btn btn-success btn-sm">Save</button>
	</div>
<script src="/js/cropper.js"></script>
<script>
	"use strict";
	var cropper

	function each(arr, callback) {
      var length = arr.length;
      var i;
      for (i = 0; i < length; i++) {callback.call(arr, arr[i], i, arr);}
	  return arr;
    }

	$("#retake_photo").click(function(event) {
		window.location.href = <?= \yii\helpers\Json::htmlEncode($addUrl) ?>;
	});

	$("#save_photo").click(function(event) {
		console.log("Saving...");
		if (cropper) {
			var canvas = cropper.getCroppedCanvas({
				//width: 200,	height: 200,     //  aspectRatio: 1 / 1
				width: 260,	height: 340, //  aspectRatio: ...
				//width: 300,	height: 450, //  aspectRatio: 2 / 3
			});
			var mydata = cropper.getData();
			var myimgdata = cropper.getImageData();
			var myimg = canvas.toDataURL("image/jpeg");
			console.log( "Crop: "+ JSON.stringify(myimg).length );

			$.ajax({
				type: "POST",
				url: <?= \yii\helpers\Json::htmlEncode($saveUrl) ?>,
				data: { imgBase64: myimg, '_csrf-backend': <?= \yii\helpers\Json::htmlEncode(Yii::$app->request->getCsrfToken()) ?> }
			}).done(function(o) {
				console.log("saved");
				window.location.href = <?= \yii\helpers\Json::htmlEncode($returnUrl) ?>;
			}).fail(function() {
				alert('Could not save the cropped photo. Please try again.');
			});

		} else { console.log("cropper no found!"); }
	});

    window.addEventListener('DOMContentLoaded', function () {
      var image = document.querySelector('#image');
      var previews = document.querySelectorAll('.preview');
      cropper = new Cropper(image, {
		  aspectRatio: 260 / 340,
          ready: function () {
            var clone = this.cloneNode();

            clone.className = ''
            clone.style.cssText = (
              'display: block;' +
              'width: 100%;' +
              'min-width: 0;' +
              'min-height: 0;' +
              'max-width: none;' +
              'max-height: none;'
            );

            each(previews, function (elem) {
              elem.appendChild(clone.cloneNode());
            });
          },

          crop: function (e) {
            var data = e.detail;
            var cropper = this.cropper;
            var imageData = cropper.getImageData();
            var previewAspectRatio = data.width / data.height;

            each(previews, function (elem) {
              var previewImage = elem.getElementsByTagName('img').item(0);
              var previewWidth = elem.offsetWidth;
              var previewHeight = previewWidth / previewAspectRatio;
              var imageScaledRatio = data.width / previewWidth;

              elem.style.height = previewHeight + 'px';
			  try{
				  previewImage.style.width = imageData.naturalWidth / imageScaledRatio + 'px';
				  previewImage.style.height = imageData.naturalHeight / imageScaledRatio + 'px';
				  previewImage.style.marginLeft = -data.x / imageScaledRatio + 'px';
				  previewImage.style.marginTop = -data.y / imageScaledRatio + 'px';
			  }catch(e){};
            });
          }
        });
    });
</script>