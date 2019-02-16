<?php

namespace common\models;

use common\dictionaries\DocumentTypes;

class DocumentReturnToStoockRoom extends Document{

	public function setType_of_operation(){
		$this->type_of_operation = DocumentTypes::TYPE_RETURN_TO_STOOCKROOM;
	}
}