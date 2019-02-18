<?php

namespace soapclient\methods;
 
 
class UpdateStatusDocument extends BaseMethod{

	public $document_guid;
	public $movement_type;
	public $status;
	public $comment;

	public function rules(){
		return [
			[['document_guid','movement_type','status','comment'],'required'],
		];
	}

	
	public function setParameters(array $parameters){
		$this->attributes = $parameters;
	}


	public function getParameters(){
		return $this->attributes;
	}
}