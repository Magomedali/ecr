<?php

namespace common\dictionaries;



class RaportStatuses extends Dictionary{
	
	const CREATED = 1;
	const IN_CONFIRMING = 2;
	const CONFIRMED= 3;
	const DELETED = 4;

	protected static $labels = array(
		self::CREATED=>"Создана",
		self::IN_CONFIRMING=>"На подтверждении",
		self::CONFIRMED=>"Подтвержден",
		self::DELETED=>"На удалении",
	); 
}