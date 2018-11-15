<?php


return [
    'components' => [
        'testclient' => [
            'class' => 'api\soap\test\TestClient',
            'wsdl' => 'http://lk.web-ali.ru/api/soap/web/wsdl.xml'
        ]
    ]
];
