<?php

namespace soapclient\methods;
 
 
class FormOfListOfDocuments extends BaseMethod{

	public $mol_guid;


	public function rules(){
		return [
			[['mol_guid'],'required'],
		];
	}

	
	public function setParameters(array $parameters){
		$this->attributes = $parameters;
	}

	public function getParameters(){
		return [
			'mol_guid'=>$this->mol_guid
		];
	}
}