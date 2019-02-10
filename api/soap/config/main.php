<?php

$params = require(__DIR__ . '/params.php');
return [
    'id' => 'api-soap',
    'basePath' => dirname(__DIR__),
    'language' => 'ru-RU',
    'controllerNamespace' => 'api\soap\controllers',
    'bootstrap' => ['log'],
    'timeZone'=>'Europe/Moscow',
    'defaultRoute'=>'api',
    'components' => [
        'user'=>[
            'identityClass'=>'common\models\User'
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
                    'logFile'=>'@api/soap/logs/info.log',
                    'enableRotation'=>false,
                    'logVars'=>['api']
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'logFile'=>'@api/soap/logs/error.log',
                    'logVars'=>[]
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logFile'=>'@api/soap/logs/warning.log',
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
    'params' => $params

];
