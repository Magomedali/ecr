<?php

namespace soapclient\methods;
 
 
interface MethodInterface{


	public function getParameters();


	public function setParameters(array $params);
}