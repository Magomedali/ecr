<?php

namespace common\modules;

use Yii;
use common\models\User;
use common\models\Request;
use soapclient\methods\GetDocumentData;

/**
 * 
 */
class LoadDocument
{
    

    public static function import($document_guid,$movement_type){

        $method = new GetDocumentData(['document_guid'=>$document_guid,'movement_type'=>$movement_type]);
        
        $document = [];

        if($method->validate()){
            try {

                Yii::warning("Call unload GetDocumentData","GetDocumentData");
                Yii::warning("Parameters","GetDocumentData");
                Yii::warning(json_encode($method->parameters),"GetDocumentData");
                $resp = Yii::$app->webservice1C->send($method);
               
                $resp = json_decode(json_encode($resp),1);

                Yii::warning("Response","GetDocumentData");
                Yii::warning(json_encode($resp),"GetDocumentData");

                $resp = isset($resp['return']) ? $resp['return'] : $resp;

                if(isset($resp['error'])){
                    Yii::warning("GetDocumentData-Error: ".$resp['error'],"GetDocumentData");
                    Yii::$app->session->setFlash("error","Произошла ошибка при запросе документа из 1С : ".$resp['error']);

                    return [];
                }

                if(isset($resp['success']) && boolval($resp['success']) && count($resp)){
                    $items = $resp;
                    
                    \yii\helpers\ArrayHelper::isAssociative($items) ? $documents = $items : $documents = reset($items);
                    
                    return $documents;
                }

            }catch (\SoapFault $e) {
                Yii::warning("SoapFault: ".$e->getMessage(),"GetDocumentData");
                Yii::$app->session->setFlash("error","Произошла ошибка при запросе документа из 1С : ".$e->getMessage());
            }catch (\Exception $e) {
                Yii::warning("Exception: ".$e->getMessage(),"GetDocumentData");
                Yii::$app->session->setFlash("error","Произошла ошибка при запросе документа из 1С : ".$e->getMessage());
            }
        }

        return $documents;
    }
    
}
?>