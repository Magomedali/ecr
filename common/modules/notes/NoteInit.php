<?php

namespace common\modules\notes;

use common\modules\ImportListOfDocuments;
use common\models\Raport;
use common\models\User;

final class NoteInit{


	public static function init(User $user){

		//Получаем документы из 1С, которые нужно подтвердить или отклонить
		$docs = ImportListOfDocuments::import($user->guid);
		// print_r($docs);
		// exit;
	}

}