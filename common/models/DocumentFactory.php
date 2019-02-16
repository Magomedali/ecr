<?php

namespace common\models;

use common\dictionaries\DocumentTypes;


final class DocumentFactory{

	
	public static function create($type,$params = array()):Document{

		switch ($type) {
			case DocumentTypes::TYPE_TRANSFER:
				return new DocumentTransfer($params);
				break;
			case DocumentTypes::TYPE_RECEIPT_FROM_MOL:
				return new DocumentReceiptFromMol($params);
				break;
			case DocumentTypes::TYPE_RETURN_TO_STOOCKROOM:
				return new DocumentReturnToStoockRoom($params);
				break;
			case DocumentTypes::TYPE_RECEIPT_FROM_STOOCKROOM:
				return new DocumentReturnFromStoockRoom($params);
				break;
		}
	}


}