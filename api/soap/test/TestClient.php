<?php

namespace api\soap\test;

use Yii;
use SoapClient;
use yii\base\Component;
use api\soap\test\requests\BaseRequest;

class TestClient extends Component
{
    public $wsdl = '';
    public $username = '';
    public $password = '';
 
    /**
     * @var SoapClient
     */
    private $client;
 
    public function init()
    {
        $this->createSoapClient();
        parent::init();
    }
 
    public function send(BaseRequest $request)
    {
        $method = pathinfo(str_replace('\\', '/', get_class($request)), PATHINFO_BASENAME);
        return @call_user_func_array([$this->client, $method], [$request]);
    }


    public function getClient(){
    	return $this->client;
    }
 
    protected function createSoapClient()
    {
        $wsdl = Yii::getAlias($this->wsdl);

        $this->client = new SoapClient($wsdl, [
            'trace' => 1,
            //'compression' => SOAP_COMPRESSION_ACCEPT,
            // 'login' => $this->username,
            // 'password' => $this->password,
            'exceptions' => 1,
            //'cache_wsdl' =>  WSDL_CACHE_MEMORY,
        ]);

    }
}