<?php

namespace common\modules;

use Yii;
use common\models\User;
use common\models\Request;
use soapclient\methods\Unloadremnantseries;

/**
 * 
 */
class ImportRemnantsWithSeries
{
	

	public static function import($guidmol){

		$method = new Unloadremnantseries(['guidmol'=>$guidmol]);
		
		$remnants = [];

		if($method->validate()){
            try {

                Yii::warning("Call unload Unloadremnantseries","Unloadremnantseries");
                Yii::warning("Parameters","Unloadremnantseries");
                Yii::warning(json_encode($method->parameters),"Unloadremnantseries");
                $resp = Yii::$app->webservice1C->send($method);
                $resp = json_decode(json_encode($resp),1);

                Yii::warning("Response","Unloadremnantseries");
                Yii::warning(json_encode($resp),"Unloadremnantseries");

                $resp = isset($resp['return']) ? $resp['return'] : $resp;

                if(isset($resp['error'])){
                	Yii::warning("Unloadremnantseries-Error: ".$resp['error'],"Unloadremnantseries");
                	Yii::$app->session->setFlash("error","Произошла ошибка при запросе остатков из 1С : ".$resp['error']);

                	return [];
                }

                if(isset($resp['remnant'])){
                    $items = $resp['remnant'];
                    
                    \yii\helpers\ArrayHelper::isAssociative($items) ? $remnants[] = $items : $remnants = $items;
                    
                    return $remnants;
                }

            }catch (\SoapFault $e) {
                Yii::warning("SoapFault: ".$e->getMessage(),"Unloadremnantseries");
                Yii::$app->session->setFlash("error","Произошла ошибка при запросе остатков из 1С : ".$e->getMessage());
            }catch (\Exception $e) {
                Yii::warning("Exception: ".$e->getMessage(),"Unloadremnantseries");
                Yii::$app->session->setFlash("error","Произошла ошибка при запросе остатков из 1С : ".$e->getMessage());
            }
        }

        return $remnants;
	}
	
}
?>