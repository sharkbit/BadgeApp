<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */

class Select2Asset extends AssetBundle {
	public $sourcePath = '@vendor/select2/select2/dist';
	public $jsOptions = ['position' => \yii\web\View::POS_HEAD];

	public $css = [
		 'css/select2.min.css',
	];

	public $js = [
		'js/select2.full.min.js',
	];

	public $publishOptions = [
		'only' => [
			'css/select2.min.css',
			'js/select2.full.min.js',
			'js/i18n/*',
		],
		//"forceCopy" => YII_DEBUG,
	];

	public $depends = [
		'yii\web\YiiAsset',
		'yii\bootstrap\BootstrapAsset',
		'yii\web\JqueryAsset', //jQueryAsset
	];
}
