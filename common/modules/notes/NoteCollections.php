<?php

namespace common\modules\notes;

use common\models\Document;

final class NoteCollections{

	/**
	* Note array[]
	*/ 
	protected static $notes = [];

	protected static $count = 0;

	public static function add(Document $doc){

		$note = new Note(['doc'=>$doc]);

		//add note to notes array
		self::$notes[$doc->type_of_operation][] = $note;
		self::$count++;
	}



	public static function getNotes(){
		return self::$notes;
	}



	public static function getCount(){
		return self::$count;
	}


	public static function getDocs(){
		$docs = [];

		foreach (self::$notes as $group => $ns) {
            foreach ($ns as $n) {
                $docs[] = $n->getDoc();
            }
        }

		return $docs;
	}
}