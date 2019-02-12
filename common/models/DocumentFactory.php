<?php

namespace common\models;

final class DocumentFactory{

	const TYPE_TRANSFER = "Передача материалов";

	const TYPE_RECEIPT_FROM_MOL = "Поступление материалов от другого мол";

	const TYPE_RETURN_TO_STOOCKROOM = "Возврат материалов на склад";

	const TYPE_RECEIPT_FROM_STOOCKROOM = "Поступление материалов со склада";


	public static function create($type,$params = array):Document{

		switch ($type) {
			case self::TYPE_TRANSFER:
				return new DocumentTransfer($params);
				break;
			case self::TYPE_RECEIPT_FROM_MOL:
				return new DocumentReceiptFromMol($params);
				break;
			case self::TYPE_RETURN_TO_STOOCKROOM:
				return new DocumentReturnToStoockRoom($params);
				break;
			case self::TYPE_RECEIPT_FROM_STOOCKROOM:
				return new DocumentReturnFromStoockRoom($params);
				break;
			
			default:
				throw new \Exception("Wrong document type");
				break;
		}
	}
}