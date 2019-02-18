<?php

namespace common\modules\notes;

use yii\helpers\Html;

class Note extends NoteDocument{


	public function displayNote(){
		$doc = $this->doc;

		return Html::a('#'.$doc->number."(Открыть)",[$doc->openUrl,'guid'=>$doc->guid,'movement_type'=>$doc->movement_type]);
	}
}