<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Clubs
*/
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Crop Photo';
$this->params['breadcrumbs'][] = ['label' => 'Range Badges', 'url' => ['/badges/index']];
$this->params['breadcrumbs'][] = ['label' => $_GET['badge'], 'url' => ['/badges/view?badge_number='.$_GET['badge']]];
$this->params['breadcrumbs'][] = $this->title;

$csrfToken=Yii::$app->request->getCsrfToken();
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
		<img id="image" src="/files/badge_photos/<?=str_pad($_GET['badge'], 5, '0', STR_PAD_LEFT)?>.jpg?dummy=<?=rand(10000,99999)?>" alt="Picture">
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
		window.location.href = "/badges/photo-add?badge=<?=$_GET['badge']?>";
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
				url: "/badges/photo-add?badge=<?=$_GET['badge']?>",
				data: { 'data': mydata, 'imgdata': myimgdata, imgBase64: myimg }
			}).done(function(o) {
				console.log("saved");
				window.location.href = "/badges/view?badge_number=<?=$_GET['badge']?>";
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