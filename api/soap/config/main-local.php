<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'H5WJcheZiDbtBSN2s-XiiaaGTvzQMlfo',
        ],
        'testclient' => [
            'class' => 'api\soap\test\TestClient',
            // 'wsdl' => 'http://lk.web-ali.ru/api/soap/web/wsdl.xml',
            'wsdl' => 'http://api.ecr:8081/wsdl.xml'
        ],
    ],
];

return $config;
