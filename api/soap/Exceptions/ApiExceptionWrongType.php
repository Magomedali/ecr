<?php

namespace api\soap\Exceptions;

use api\soap\models\Responce;

/**
 * Class Exception
 *
 */
class ApiExceptionWrongType extends ApiException
{

	public function __construct($message = "WrontType", $name = "Parameter has wrong type", $errorUserMessage="", $code = 0, \Exception $previous = null){
        parent::__construct($message, $name,$errorUserMessage,$code, $previous);
    }
}
