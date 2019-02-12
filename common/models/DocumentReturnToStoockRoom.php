<?php

namespace common\models;


class DocumentReturnToStoockRoom extends Document{

	public function setMovement_type(){
		$this->movement_type = DocumentFactory::TYPE_RETURN_TO_STOOCKROOM;
	}
}