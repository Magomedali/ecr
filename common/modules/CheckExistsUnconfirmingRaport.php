<?php

namespace common\modules;

use common\models\User;
use common\models\{Raport};

class CheckExistsUnconfirmingRaport{

	protected $user;


	public function __construct(User $user){
		$this->user = $user;
	}

	public function isClosed(){

		if($this->exsistsRaports()) return false;
		
		return true;
	}


	public function exsistsRaports(){

		$raports = Raport::find()
							->where(['user_guid'=>$this->user->guid,'isDeleted'=>0])
							->andFilterWhere(['in','status',Raport::getUnconfirmedStatuses()])
							->all();
		
		return count($raports);
	}
}