<?php

namespace soapclient\methods;
 
 
class TotalOutput extends BaseMethod{

	public $brigade_guid;
	public $date;


	public function rules(){
		return [
			[['brigade_guid','date'],'required'],
			[['date'],'filter','filter'=>function($v){ return $v ? date("Y-m-d",strtotime($v)) : date("Y-m-d");}],
			[['date'],'default','value'=>date("Y-m-d",time())],
		];
	}

	


	public function getParameters(){
		return $this->attributes;
	}
}