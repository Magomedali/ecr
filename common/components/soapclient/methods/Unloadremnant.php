<?php

namespace soapclient\methods;
 
 
class unloadremnant extends BaseMethod{

	public $guidmol;


	public function rules(){
		return [
			[['guidmol'],'required'],
			['guidmol','string','min'=>0,'max'=>36],
		];
	}

	


	public function getParameters(){
		return $this->attributes;
	}
}