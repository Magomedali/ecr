<?php

namespace soapclient\methods;
 
 
class UpdateStatusDocument extends BaseMethod{

	public $document_guid;
	public $movement_type;
	public $status;

	public function rules(){
		return [
			[['document_guid','movement_type','status'],'required'],
		];
	}

	


	public function getParameters(){
		return $this->attributes;
	}
}