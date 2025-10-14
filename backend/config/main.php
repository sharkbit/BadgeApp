<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);


return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'gridview' => ['class' => '\kartik\grid\Module'],

    ],
    'components' => [
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'sourcePath' => '@vendor/bower-asset/jquery/dist',
                    'js' => ['jquery.min.js'],
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    'sourcePath' => '@vendor/bower-asset/bootstrap/dist',
                    'css' => ['css/bootstrap.min.css'],
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'sourcePath' => '@vendor/bower-asset/bootstrap/dist',
                    'js' => ['js/bootstrap.min.js'],
                ],
                'yii\web\YiiAsset' => [
                    'sourcePath' => '@vendor/yiisoft/yii2/assets',
                    'js' => ['yii.js'],
                ],
                'backend\assets\FontAwesomeAsset' => [
                    'sourcePath' => '@vendor/bower-asset/fontawesome',
                    'css' => ['css/all.min.css'],  // Adjust this according to the FontAwesome version.
                ],
                'skinka\widgets\gritter\AlertGritterWidget' => [
                    'sourcePath' => '@vendor/bower-asset/jquery.gritter',
                    'js' => ['js/jquery.gritter.min.js'],
                    'css' => ['css/jquery.gritter.css'],
                ],
            ],
        ],
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true, 'sameSite' => 'strict'],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                // ...
            ],
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'thousandSeparator' => ',',
            'decimalSeparator' => '.',
        ],

    ],
    'params' => $params,
];
