<?php

namespace soapclient\methods;

 
class ApplicationForMaterials extends BaseMethod{

	protected $parameters = [];


	public function setParameters(array $parameters){
		$this->parameters = $parameters;
	}


	public function getParameters(){
		

		$parameters = $this->parameters;

		return [
			'document'=>$parameters
		];
	}

}