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
        'testclient' => [
            'class' => 'api\soap\test\TestClient',
            'wsdl' => 'http://api.ecr:8081/'
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
