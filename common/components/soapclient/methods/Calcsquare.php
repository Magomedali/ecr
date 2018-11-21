<?php

namespace soapclient\methods;
 
 
class Calcsquare extends BaseMethod{



	public function rules(){
		return [
			[['lineguid','length','count'],'required'],
			['lineguid','string','min'=>36,'max'=>36],
			[['length','count'],'number']
		];
	}

	
}