<?php

namespace soapclient\methods;
 
use yii\base\Model;
 
abstract class BaseMethod extends Model{


	public function save(){
		return true;
	}
}