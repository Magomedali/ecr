<?php

namespace common\dictionaries;



class DocumentTypes extends Dictionary{
	
	const TYPE_TRANSFER = "Передача материалов";

	const TYPE_RECEIPT_FROM_MOL = "Поступление материалов от другого мол";

	const TYPE_RETURN_TO_STOOCKROOM = "Возврат материалов на склад";

	const TYPE_RECEIPT_FROM_STOOCKROOM = "Поступление материалов со склада";


	protected static $labels = array(
		self::TYPE_TRANSFER=>"Передача материалов",
		self::TYPE_RECEIPT_FROM_MOL=>"Поступление материалов от другого мол",
		self::TYPE_RETURN_TO_STOOCKROOM=>"Возврат материалов на склад",
		self::TYPE_RECEIPT_FROM_STOOCKROOM=>"Поступление материалов со склада",
	); 
 
}