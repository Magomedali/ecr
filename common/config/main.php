<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
  		],
  		'webservice1C'=>[
  			'class'=>'soapclient\SClient',
  			
        'wsdl'=>'https://crm.omegamail.org/usom/ws/drrload.1cws?wsdl',
        'username'=>'obmen',
        'password'=>'651865'
  		]
    ],
];
