<?php


return [
    'id' => 'api-soap',
    'basePath' => dirname(__DIR__),
    'language' => 'ru-RU',
    'controllerNamespace' => 'api\soap\controllers',
    'bootstrap' => ['log'],
    'defaultRoute'=>'api',
    'components' => [
        
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'ecr-api',
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
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'showScriptName' => false,
            'rules' => [
                'wsdl'=>'api/index'
            ],
        ],
    ],
    'params' => [
    ]
];
