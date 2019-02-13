<?php
namespace common\modules\notes;

use yii\base\BaseObject;
use common\models\Document;

abstract class NoteDocument extends BaseObject implements Note{

	protected $doc;


	public function setDoc(Document $doc){
		$this->doc = $doc;
	}


	public function getDoc(){
		return $doc;
	}

}