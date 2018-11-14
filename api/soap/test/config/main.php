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
                    'levels' => ['error', 'warning'],
                ],
            ],
        ]
    ],
    'params' => [
    ]
];
