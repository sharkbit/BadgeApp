<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */

class FontAwesomeAsset extends AssetBundle {
	public $sourcePath = '@bower/fontawesome';
	public $jsOptions = ['position' => \yii\web\View::POS_HEAD];

	public $css = [
		 'css/all.min.css',
	];

	public $publishOptions = [
		'only' => [
			'css/*',
			'js/*',
			'webfonts/*',
			'sprites/*',
			'svgs/*',
		],
		//"forceCopy" => YII_DEBUG,
	];
}
