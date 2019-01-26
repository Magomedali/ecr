<?php

namespace common\dictionaries;



class ExchangeStatuses extends Dictionary{
	
	const CREATED = 1;
	const IN_CONFIRMING = 2;
	const CONFIRMED= 3;
	const DELETED = 4;

	protected static $labels = array(
		self::CREATED=>"Создан",
		self::IN_CONFIRMING=>"На подтверждении",
		self::CONFIRMED=>"Принят",
		self::DELETED=>"Отклонен",
	); 



	public static $notification = array(
		self::CREATED=>"default",
		self::IN_CONFIRMING=>"info",
		self::CONFIRMED=>"success",
		self::DELETED=>"danger",
	); 
}