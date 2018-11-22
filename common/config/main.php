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
  			
        'wsdl'=>'https://89.207.91.230:45443/usom/ws/drrload.1cws?wsdl',
        'username'=>'obmen',
        'password'=>'651865',
        
        //'wsdl'=>'http://217.76.41.228/exp/ws/ws1.1cws?wsdl',
  			//'username'=>'Федулов Денис',
  			//'password'=>'TporgS4',

        // 'wsdl'=>'http://62.148.16.218/bosfor2017/ws/ws1.1cws?wsdl',
        // 'username'=>'гамзат',
        // 'password'=>'гамзат'
  		]
    ],
];
