<?php

namespace api\soap\Exceptions;

use api\soap\models\Responce;

/**
 * Class Exception
 *
 */
class ApiExceptionMethodNotExists extends ApiException
{
	
	public function __construct($message = "MethodNotExists", $name = "Method not founded on server handler class", $errorUserMessage="", $code = 0, \Exception $previous = null){
        parent::__construct($message, $name,$errorUserMessage,$code, $previous);
    }
}
