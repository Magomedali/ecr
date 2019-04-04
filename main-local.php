<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=mayorov2019_ecr',
            'username' => 'mayorov2019_ecr',
            'password' => 'nBS6rx3h',
            'charset' => 'utf8',
            'enableSchemaCache' => true
        ],
        'webservice1C'=>[
            'class'=>'soapclient\SClient',
            'wsdl'=>'https://crm.omegamail.org/usotest/ws/drrload.1cws?wsdl',
            'location'=>'https://crm.omegamail.org/usotest/ws/drrload.1cws',
            'username'=>'Окмазов Магомед',
            'password'=>'651865'
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
    ],
];
