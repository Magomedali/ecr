<?php

namespace api\soap\Exceptions;

use api\soap\models\Responce;

/**
 * Class Exception
 *
 */
class ApiExceptionNotAuthenticated extends ApiException
{

	public function __construct($message = "AuthenticateError", $name = "didn`t set username and password or wrong values", $errorUserMessage="", $code = 0, \Exception $previous = null){
        parent::__construct($message, $name,$errorUserMessage,$code, $previous);
    }
}
