<?php

namespace common\models;

use common\dictionaries\DocumentTypes;

class DocumentReturnFromStoockRoom extends Document{


	public function setType_of_operation(){
		$this->type_of_operation = DocumentTypes::TYPE_RECEIPT_FROM_STOOCKROOM;
	}
}