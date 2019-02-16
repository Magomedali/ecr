<?php

namespace common\models;

use common\dictionaries\DocumentTypes;

class DocumentTransfer extends Document{

	public function setType_of_operation(){
		$this->type_of_operation = DocumentTypes::TYPE_TRANSFER;
	}
}