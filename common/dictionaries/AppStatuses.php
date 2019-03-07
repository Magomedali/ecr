<?php

namespace common\dictionaries;


class AppStatuses extends Dictionary{
	
	const CREATED = 1;
	const IN_CONFIRMING = 2;
	const CONFIRMED= 3;
	const COMPLETED = 4;
	const DELETED = 5;


	protected static $labels = array(
		self::CREATED=>"Создан",
		self::IN_CONFIRMING=>"На подтверждении",
		self::CONFIRMED=>"Принят",
		self::COMPLETED=>"Выполнено",
		self::DELETED=>"Не принят",
	);


	protected static $transferValue = array(
		self::CREATED=>"Создан",
		self::IN_CONFIRMING=>"Создан",
		self::CONFIRMED=>"Принят",
		self::COMPLETED=>"Выполнено",
		self::DELETED=>"НеПринят",
	);


	public static $notification = array(
		self::CREATED=>"default",
		self::IN_CONFIRMING=>"info",
		self::CONFIRMED=>"success",
		self::COMPLETED=>"success",
		self::DELETED=>"danger",
	);


	public static function getTransferValue($code = null){
		if($code && array_key_exists($code, static::$transferValue))
            return static::$transferValue[$code];
        return $code === null ? static::$transferValue : "";
	}


	public static function getCodeByTransferValue($str = null){
        if($str == null) return null;
        $str = mb_strtolower($str);
        
        $nls = static::normalizeTransferValue();
        
        return array_search($str, $nls);
    }


    public static function normalizeTransferValue(){
        $labels = [];
        foreach (static::$transferValue as $key => $value) {
            $labels[$key] = mb_strtolower($value);
        }
        return $labels;
    }
}