<?php

namespace common\models;


class DocumentReturnFromStoockRoom extends Document{

	public function setMovement_type(){
		$this->movement_type = DocumentFactory::TYPE_RECEIPT_FROM_STOOCKROOM;
	}
}