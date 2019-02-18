<?php

namespace soapclient\methods;
 
 
class Unloadremnant extends BaseMethod{

	public $guidmol;


	public function rules(){
		return [
			[['guidmol'],'required'],
			['guidmol','string','min'=>0,'max'=>36],
		];
	}

	public function setParameters(array $parameters){
		$this->attributes = $parameters;
	}


	public function getParameters(){
		return $this->attributes;
	}
}