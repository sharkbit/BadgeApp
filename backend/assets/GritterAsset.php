<?php

namespace backend\assets;

use yii\web\AssetBundle;

class GritterAsset extends AssetBundle
{
    public $sourcePath = '@vendor/bower-asset/jquery.gritter';
    public $js = [
        'js/jquery.gritter.min.js',
    ];
    public $css = [
        'css/jquery.gritter.css',
    ];
    public $depends = [
        'yii\web\YiiAsset', // Ensure jQuery is loaded first
    ];
}
