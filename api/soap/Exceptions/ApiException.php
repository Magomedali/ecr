<?php

namespace api\soap\Exceptions;

use api\soap\models\Responce;

/**
 * Class Exception
 *
 */
class ApiException extends \yii\base\Exception
{
	public $error;
	public $errorMessage;
	public $errorUserMessage;


    public function __construct($message = "", $name = "", $errorUserMessage="", $code = 0, \Exception $previous = null){

        $this->error = $name;
        $this->errorMessage = $message;
        $this->errorUserMessage = $message;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'SOAP Server Api Exception';
    }


    /**
     * @return string the user-friendly name of this exception
     */
    public function getResponce()
    {
        return new Responce(['success'=>false,'error'=>$this->error,'errorMessage'=>$this->errorMessage,'errorUserMessage'=>$this->errorUserMessage]);
    }
}
