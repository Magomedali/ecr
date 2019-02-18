<?php

namespace common\models;

use yii\helpers\ArrayHelper;
use common\dictionaries\DocumentTypes;
use common\modules\TransferMaterials;

class DocumentTransfer extends Document{


	public $openUrl = 'document/open';


	public function setType_of_operation(){
		$this->type_of_operation = DocumentTypes::TYPE_TRANSFER;
	}


	public function getStructuredMaterials(){
        if(!is_array($this->materials) || !count($this->materials)) return [];

        if(ArrayHelper::isAssociative($this->materials)){
            $this->materials =  [$this->materials];
        }

        $structured = [];
        foreach ($this->materials as $key => $m) {
            $structured[$m['nomenclature_guid']][$m['series_guid']]['count'] = $m['count'];
        }

        return $structured;
    }

}