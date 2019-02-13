<?php

namespace common\modules;

use common\models\User;
use common\models\{Raport,Setting};
use common\models\RaportRegulatory;

class CheckCloseShift{

	protected $date;


	protected $user;

	public function __construct(User $user){
		$this->user = $user;

		$this->date = Setting::getStartShiftTime();
	}

	public function isClosed(){

		if($this->exsistsRaports()) return false;
		
		if($this->exsistsRaportsRegulatory()) return false;
		
		return true;
	}


	public function exsistsRaports(){

		$raports = Raport::find()
							->where(['user_guid'=>$this->user->guid,'isDeleted'=>0])
							->andFilterWhere(['in','status',Raport::getUnconfirmedStatuses()])
							->andFilterWhere(["<",'created_at',date("Y-m-d\TH:i:s",strtotime($this->date))])
							->all();
		
		return count($raports);
	}



	public function exsistsRaportsRegulatory(){

		$results = RaportRegulatory::find()
							->where(['user_guid'=>$this->user->guid,'isDeleted'=>0])
							->andFilterWhere(['in','status',Raport::getUnconfirmedStatuses()])
							->andFilterWhere(["<",'created_at',date("Y-m-d\TH:i:s",strtotime($this->date))])
							->all();
		
		return count($results);
	}
}