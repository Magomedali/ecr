<?php

namespace soapclient;

use Yii;
use SoapClient;
use SoapHeader;
use yii\base\Component;
use soapclient\methods\BaseMethod;

class SClient extends Component
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
 



    public function send(BaseMethod $method)
    {
        $methodName = pathinfo(str_replace('\\', '/', get_class($method)), PATHINFO_BASENAME);
        
        return @call_user_func_array([$this->client, $methodName], [$method]);
    }


    
 

    protected function createSoapClient(){
        $wsdl = Yii::getAlias($this->wsdl);

        $this->client = new SoapClient($wsdl, [
            'trace' => 1,
            //'compression' => SOAP_COMPRESSION_ACCEPT,
            'login' => $this->username,
            'password' => $this->password,
            //'exceptions' => 1,
            //'cache_wsdl' =>  WSDL_CACHE_MEMORY,
        ]);

        // $AuthHeader = new \stdClass();
        // $AuthHeader->username = $this->username;
        // $AuthHeader->password = $this->password;
        
        // $Headers = new SoapHeader($wsdl, 'authenticate', $AuthHeader);
        // $this->client->__setSoapHeaders($Headers);
    }
}