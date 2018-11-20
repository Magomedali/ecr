<?php

$params = require(__DIR__ . '/params.php');

$cred = $params['SoapApi']['api_credentials'];

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'H5WJcheZiDbtBSN2s-XiiaaGTvzQMlfo',
        ],
        'testclient' => [
            'class' => 'api\soap\test\TestClient',
            'wsdl' => 'http://localhost:8082/ecr/api/soap/web/wsdl.php',
            'username'=>key($cred),
            'password'=>reset($cred)
        ],
    ],
];

return $config;
