<?php

namespace common\modules;

use Yii;
use common\models\User;
use common\models\Request;
use soapclient\methods\FormOfListOfDocuments;

/**
 * 
 */
class ImportListOfDocuments
{
	

	public static function import($mol_guid){

		$method = new FormOfListOfDocuments(['mol_guid'=>$mol_guid]);
		
		$documents = [];

		if($method->validate()){
            try {

                Yii::warning("Call unload FormOfListOfDocuments","FormOfListOfDocuments");
                Yii::warning("Parameters","FormOfListOfDocuments");
                Yii::warning(json_encode($method->parameters),"FormOfListOfDocuments");
                $resp = Yii::$app->webservice1C->send($method);
                $resp = json_decode(json_encode($resp),1);

                Yii::warning("Response","FormOfListOfDocuments");
                Yii::warning(json_encode($resp),"FormOfListOfDocuments");

                $resp = isset($resp['return']) ? $resp['return'] : $resp;

                if(isset($resp['error'])){
                	Yii::warning("FormOfListOfDocuments-Error: ".$resp['error'],"FormOfListOfDocuments");
                	Yii::$app->session->setFlash("error","Произошла ошибка при запросе документов из 1С : ".$resp['error']);

                	return [];
                }

                print_r($resp);
                exit;

                if(isset($resp['remnant'])){
                    $items = $resp['remnant'];
                    
                    \yii\helpers\ArrayHelper::isAssociative($items) ? $documents[] = $items : $documents = $items;
                    
                    return $documents;
                }

            }catch (\SoapFault $e) {
                Yii::warning("SoapFault: ".$e->getMessage(),"FormOfListOfDocuments");
                Yii::$app->session->setFlash("error","Произошла ошибка при запросе документов из 1С : ".$e->getMessage());
            }catch (\Exception $e) {
                Yii::warning("Exception: ".$e->getMessage(),"FormOfListOfDocuments");
                Yii::$app->session->setFlash("error","Произошла ошибка при запросе документов из 1С : ".$e->getMessage());
            }
        }

        return $documents;
	}
	
}
?>