<?php 

namespace console\models;

use Yii;
use common\models\Request;
use soapclient\methods\RaportLoad;
use soapclient\methods\RaportRegulatoryLoad;
use soapclient\methods\ApplicationForMaterials;
use soapclient\methods\TransferOfMaterials;

class RequestMethodFactory{

	public static function make(Request $req){

		if(!class_exists($req['request'])) return null;
		
		switch ($req['request']) {
			case RaportLoad::className():
				return new RMRaportLoad($req);
				break;
			case RaportRegulatoryLoad::className():
				return new RMRaportRegulatoryLoad($req);
				break;
			case ApplicationForMaterials::className():
				return new RMApplicationForMaterials($req);
				break;
			default:
				return new RMSimple($req);
				break;
		}
	}

}

?>