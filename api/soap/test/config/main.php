<?php


return [
    'id' => 'api-soap-client',
    'basePath' => dirname(__DIR__),
    'language' => 'ru-RU',
    'controllerNamespace' => 'api\soap\test\controllers',
    'bootstrap' => ['log'],
    'defaultRoute'=>'client',
    'components' => [
        'user'=>[
            'class'=>'yii\web\User',
            'identityClass'=>'common\models\User'
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'logFile'=>'@api/soap/logs/info.txt',
                    'logVars'=>['api']
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'logFile'=>'@api/soap/logs/error.txt',
                    'logVars'=>[]
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logFile'=>'@api/soap/logs/warning.txt',
                    'logVars'=>[]
                ],
            ],
        ],
    ],
    'params' => [
    ]
];
