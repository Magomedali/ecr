<?php 

namespace console\models;

use Yii;
use common\models\Request;

abstract class RequestMethod{

	protected $request;

	public function __construct(Request $req){
		$this->request = $req;
	}


	public abstract function repeat();

}

?>