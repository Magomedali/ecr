<?php

namespace common\modules;

use Yii;
use common\models\User;
use common\models\Request;
use soapclient\methods\TotalOutput;


class ImportTotalOutput{
	
	public static function import($brigade_guid,$date){

		$method = new TotalOutput(['brigade_guid'=>$brigade_guid,'date'=>$date]);
		
		$totals = [];

		if($method->validate()){
            try {

                Yii::warning("Call unload TotalOutput","TotalOutput");
                Yii::warning("Parameters","TotalOutput");
                Yii::warning(json_encode($method->parameters),"TotalOutput");
                $resp = Yii::$app->webservice1C->send($method);
                $resp = json_decode(json_encode($resp),1);

                Yii::warning("Response","TotalOutput");
                Yii::warning(json_encode($resp),"TotalOutput");

                $resp = isset($resp['return']) ? $resp['return'] : $resp;

                if(isset($resp['error'])){
                	Yii::warning("TotalOutput-Error: ".$resp['error'],"TotalOutput");
                	Yii::$app->session->setFlash("error","Произошла ошибка при выгрузке отчета из 1С : ".$resp['error']);

                	return [];
                }

                if(isset($resp['success']) && boolval($resp['success']) && isset($resp['totals'])){
                    $items = $resp['totals'];
                    
                    \yii\helpers\ArrayHelper::isAssociative($items) ? $totals[] = $items : $totals = $items;
                    
                    return $totals;
                }

            }catch (\SoapFault $e) {
                Yii::warning("SoapFault: ".$e->getMessage(),"TotalOutput");
                Yii::$app->session->setFlash("error","Произошла ошибка при выгрузке отчета из 1С : ".$e->getMessage());
            }catch (\Exception $e) {
                Yii::warning("Exception: ".$e->getMessage(),"TotalOutput");
                Yii::$app->session->setFlash("error","Произошла ошибка при выгрузке отчета из 1С : ".$e->getMessage());
            }
        }

        return $totals;
	}
	
}
?>