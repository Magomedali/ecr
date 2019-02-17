<?php

namespace common\modules\notes;

use common\modules\ImportListOfDocuments;
use common\models\Raport;
use common\models\{User,Document,DocumentFactory};

final class NoteInit{


	public static function init(User $user){

		//Получаем документы из 1С, которые нужно подтвердить или отклонить
		$docs = ImportListOfDocuments::import($user->guid);
		
		if(!count($docs) || NoteCollections::getCount()) return null;

		foreach ($docs as $key => $doc) {
			if(!isset($doc['type_of_operation'])) return null;	
			$docModel = DocumentFactory::create($doc['type_of_operation'],$doc);
			if($docModel instanceof Document){
				NoteCollections::add($docModel);
			}
		}
	}

}