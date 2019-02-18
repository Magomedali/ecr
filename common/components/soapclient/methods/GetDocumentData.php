<?php

namespace soapclient\methods;
 
 
class GetDocumentData extends BaseMethod{

	public $document_guid;
	public $movement_type;


	public function rules(){
		return [
			[['document_guid','movement_type'],'required'],
		];
	}


	public function setParameters(array $parameters){
		$this->attributes = $parameters;
	}


	public function getParameters(){
		return $this->attributes;
	}
}