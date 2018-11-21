<?php

namespace soapclient\methods;
 
 
class Useraccountload extends BaseMethod{

	public $guid;

	public $password;

	public function rules(){
		return [
			[['guid','password'],'required'],
			['guid','string','min'=>36,'max'=>36],
			[['password'],'string']
		];
	}



}