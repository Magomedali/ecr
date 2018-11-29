<?php

namespace soapclient\methods;
 
 
class Unloadremnant extends BaseMethod{

	public $mol_guid;


	public function rules(){
		return [
			[['mol_guid'],'required'],
			['mol_guid','string','min'=>0,'max'=>36],
		];
	}

	


	public function getParameters(){
		return $this->attributes;
	}
}