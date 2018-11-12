<?php


return [
    'id' => 'api-soap',
    'basePath' => dirname(__DIR__),
    'language' => 'ru-RU',
    'controllerNamespace' => 'api\soap\controllers',
    'bootstrap' => ['log'],
    'defaultRoute'=>'api',
    'components' => [
        'user'=>[
            'identityClass'=>'common\models\User'
        ],
        'testclient' => [
            'class' => 'api\soap\test\TestClient',
            'wsdl' => 'http://localhost:8082/ecr/api/soap/web/'
        ],
    
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'ecr-api',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'logFile'=>'@api/soap/logs/info.txt',
                    'logVars'=>[]
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
        // 'urlManager' => [
        //     'enablePrettyUrl' => true,
        //     'enableStrictParsing' => false,
        //     'showScriptName' => false,
        //     'rules' => [
        //         'wsdl'=>'api/index'
        //     ],
        // ],
    ],
    'params' => [
    ]
];
