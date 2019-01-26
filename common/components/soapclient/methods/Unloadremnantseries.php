<?php

namespace soapclient\methods;
 
 
class Unloadremnantseries extends BaseMethod{

	public $guidmol;


	public function rules(){
		return [
			[['guidmol'],'required'],
		];
	}

	


	public function getParameters(){
		return [
			'guidmol'=>$this->guidmol
		];
	}
}