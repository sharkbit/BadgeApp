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
<div class="container">
<ul>
<li>Please use a white background.</li>
<li>Try to take a passport style photo.</li>
</ul>

<div class="row" id="video_block">
	<div class="col-md-12 text-center">
		<video id="my_photo" autoplay playsinline muted style="width:80%;"></video>
	</div>
	<div class="col-md-12 text-center">
		<button type="button" id="take_snapshots" class="btn btn-success btn-sm" disabled>Take Photo</button>
	</div>
</div>
<div class="row" id="photo_block" style="display: none;">
	<div class="col-md-12 text-center">
		<div id="new_badge_photo"> </div>
	</div>
	<div class="col-md-12 text-center">
		<button type="button" id="retake_photo" class="btn btn-primary btn-sm">Retake / Choose Another</button>
		<button type="button" id="save_photo" class="btn btn-success btn-sm">Use Photo</button>
	</div>
</div>
<div class="row">
	<div class="col-md-12 text-center">
		<p id="camera_status" role="status">Starting camera...</p>
		<button type="button" id="retry_camera" class="btn btn-primary btn-sm" style="display:none;">Retry Camera</button>
		<br />
		<label for="photo_file">Or choose a photo / use your device camera:</label>
		<input type="file" id="photo_file" accept="image/*" capture="environment">
	</div>
</div>

</div>

<script>
  (function() {
	"use strict";
	var video = document.getElementById('my_photo');
	var preview = document.getElementById('new_badge_photo');
	var status = document.getElementById('camera_status');
	var stream = null;
	var streamUrl = null;
	var imageData = null;

	function showStatus(message, isError) {
		status.textContent = message;
		status.className = isError ? 'text-danger' : '';
	}

	function stopCamera() {
		if (stream) {
			stream.getTracks().forEach(function(track) { track.stop(); });
			stream = null;
		}
		if ('srcObject' in video) {
			video.srcObject = null;
		} else if ('mozSrcObject' in video) {
			video.mozSrcObject = null;
		} else {
			video.removeAttribute('src');
		}
		if (streamUrl) {
			URL.revokeObjectURL(streamUrl);
			streamUrl = null;
		}
	}

	function captureSource(source, width, height) {
		if (!width || !height) {
			showStatus('The photo is not ready yet. Please try again.', true);
			return;
		}
		var scale = Math.min(1, 1600 / width);
		var canvas = document.createElement('canvas');
		canvas.width = Math.round(width * scale);
		canvas.height = Math.round(height * scale);
		var context = canvas.getContext('2d');
		if (!context) {
			showStatus('Photo capture is not available in this browser.', true);
			return;
		}
		context.drawImage(source, 0, 0, canvas.width, canvas.height);
		try {
			imageData = canvas.toDataURL('image/jpeg', 0.9);
		} catch (error) {
			showStatus('Could not read the selected photo. Please try another image.', true);
			return;
		}
		preview.innerHTML = '';
		preview.appendChild(canvas);
		$('#video_block').hide();
		$('#photo_block').show();
		showStatus('Review the photo, then choose Use Photo or retake it.');
	}

	function startCamera() {
		stopCamera();
		var getUserMedia = navigator.mediaDevices && navigator.mediaDevices.getUserMedia
			? navigator.mediaDevices.getUserMedia.bind(navigator.mediaDevices)
			: null;
		if (!getUserMedia && (navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia)) {
			var legacyGetUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia;
			getUserMedia = function(constraints) {
				return new Promise(function(resolve, reject) {
					legacyGetUserMedia.call(navigator, constraints, resolve, reject);
				});
			};
		}
		if (!getUserMedia) {
			showStatus('Live camera is not supported here. Use the photo chooser below instead.', true);
			document.getElementById('retry_camera').style.display = 'none';
			return Promise.resolve();
		}
		var cameraRequest = getUserMedia({
			video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 }, height: { ideal: 720 } },
			audio: false
		});
		return cameraRequest.catch(function(error) {
				if (error && (
					error.name === 'OverconstrainedError' ||
					error.name === 'ConstraintNotSatisfiedError' ||
					error.name === 'NotReadableError' ||
					error.name === 'TypeError'
				)) {
					return getUserMedia({ video: true, audio: false });
				}
				throw error;
		}).then(function(cameraStream) {
			stream = cameraStream;
			if ('srcObject' in video) {
				video.srcObject = stream;
			} else if ('mozSrcObject' in video) {
				video.mozSrcObject = stream;
			} else if (window.URL && URL.createObjectURL) {
				streamUrl = URL.createObjectURL(stream);
				video.src = streamUrl;
			}
			return video.play();
		}).then(function() {
			document.getElementById('take_snapshots').disabled = false;
			document.getElementById('retry_camera').style.display = 'none';
			showStatus('Camera ready.');
		}).catch(function(error) {
			console.info('Camera unavailable:', error.name);
			var message = 'Could not start the camera. Allow camera access, use HTTPS, or choose a photo below.';
			if (error && error.name === 'NotFoundError') {
				message = 'No camera was found. Choose a photo below.';
			} else if (error && (error.name === 'NotAllowedError' || error.name === 'SecurityError')) {
				message = 'Camera access was denied or blocked. Allow access, use HTTPS, or choose a photo below.';
			} else if (error && error.name === 'NotReadableError') {
				message = 'The camera could not start. It may be in use by another app or browser tab. Close other camera apps, then retry, or choose a photo below.';
			}
			document.getElementById('take_snapshots').disabled = true;
			document.getElementById('retry_camera').style.display = 'inline-block';
			showStatus(message, true);
		});
	}

	document.getElementById('retry_camera').addEventListener('click', function() {
		var button = this;
		button.disabled = true;
		showStatus('Retrying camera...');
		startCamera().then(function() {
			button.disabled = false;
		});
	});

	document.getElementById('take_snapshots').addEventListener('click', function() {
		captureSource(video, video.videoWidth, video.videoHeight);
	});

	document.getElementById('retake_photo').addEventListener('click', function() {
		imageData = null;
		preview.innerHTML = '';
		$('#photo_block').hide();
		$('#video_block').show();
		showStatus(stream ? 'Camera ready.' : 'Choose a photo below, or reload the page to retry the camera.');
	});

	document.getElementById('photo_file').addEventListener('change', function(event) {
		var file = event.target.files && event.target.files[0];
		if (!file) return;
		if (!file.type || file.type.indexOf('image/') !== 0) {
			showStatus('Please choose a valid image file.', true);
			event.target.value = '';
			return;
		}
		var reader = new FileReader();
		reader.onload = function(loadEvent) {
			var image = new Image();
			image.onload = function() {
				stopCamera();
				captureSource(image, image.naturalWidth, image.naturalHeight);
			};
			image.onerror = function() {
				showStatus('Could not open that image. Please choose another photo.', true);
			};
			image.src = loadEvent.target.result;
		};
		reader.onerror = function() {
			showStatus('Could not read that image. Please choose another photo.', true);
		};
		reader.readAsDataURL(file);
	});

	document.getElementById('save_photo').addEventListener('click', function() {
		if (!imageData) {
			showStatus('Take a photo or choose an image first.', true);
			return;
		}
		var button = this;
		button.disabled = true;
		showStatus('Saving photo...');
		$.ajax({
			type: 'POST',
			url: '/badges/photo-add?badge=<?= rawurlencode((string)Yii::$app->request->get('badge')) ?>',
			data: { imgBase64: imageData, '_csrf-backend': <?= \yii\helpers\Json::htmlEncode($csrfToken) ?> }
		}).done(function() {
			window.location.href = '/badges/photo-crop?badge=<?= rawurlencode((string)Yii::$app->request->get('badge')) ?>';
		}).fail(function() {
			button.disabled = false;
			showStatus('Photo could not be saved. Please try again.', true);
		});
	});

	window.addEventListener('beforeunload', stopCamera);
	window.addEventListener('DOMContentLoaded', startCamera);
  })();
</script>
