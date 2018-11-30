<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'enableRotation'=>false,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'logFile'=>'@frontend/logs/api.log',
                    'levels' => ['warning'],
                    'enableRotation'=>false
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'i18n'=>[
            'translations'=>[
                '*'=>[
                    'class'=>'yii\i18n\PhpMessageSource',
                    'basePath'=>'@backend/messages',
                ],
            ]
            
        ],
    ],

    'modules' => [
        'debug' => [
            'class' => 'yii\debug\Module',
            'allowedIPs' => ['::1']
        ]
    ],    
    'params' => $params,
];
