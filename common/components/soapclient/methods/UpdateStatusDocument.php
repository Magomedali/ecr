<?php

namespace soapclient\methods;
 
 
class UpdateStatusDocument extends BaseMethod{

	public $document_guid;
	public $movement_type;
	public $status;
	public $comment;

	public function rules(){
		return [
			[['document_guid','movement_type','status'],'required'],
			['comment','safe'],
			['comment','default','value'=>null]
		];
	}

	
	public function setParameters(array $parameters){
		$this->attributes = $parameters;
	}


	public function getParameters(){
		return $this->attributes;
	}
}