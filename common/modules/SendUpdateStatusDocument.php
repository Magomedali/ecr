<?php

namespace common\modules;

use Yii;
use common\models\Document;
use common\models\Request;
use soapclient\methods\UpdateStatusDocument;

class SendUpdateStatusDocument{


	public static function export($params = array()){

		try {
			$method = new UpdateStatusDocument($params);
			$request = new Request([
                'request'=>get_class($method),
                'params_in'=>json_encode($params),
                'resource_id'=>null,
                'actor_id'=>Yii::$app->user->id
            ]);

            if(!$request->validate()){
                Yii::warning("Request validate error","UpdateStatusDocument");
                Yii::warning($request->getErrors(),"UpdateStatusDocument");
                return false; 
            }

            if($request->send($method)){
                $responce = json_decode($request->params_out,1);

                $responce = isset($responce['return']) ? $responce['return'] : $responce;
                if(isset($responce['error'])){
                	Yii::warning("Error","UpdateStatusDocument");
                	Yii::warning($responce['error'],"UpdateStatusDocument");
                	Yii::$app->session->setFlash("error",$responce['error']);
                }

                if($request->result && isset($responce['success']) && $responce['success']){
                    return true;
                }
            }
            
        } catch (\Exception $e) {
            Yii::warning($e->getMessage(),'api');
            Yii::$app->session->setFlash("error",$e->getMessage());
        }
        
        return false;
	}
}