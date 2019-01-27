<?php

namespace common\modules;

use Yii;
use yii\db\Query;
use common\models\Request;
use common\modules\TransferMaterials;
use soapclient\methods\TransferOfMaterials;

class ExportTransferMaterials{


	public static function export(TransferMaterials $model){
        
		$params = [
			'date'=>date("Y-m-d\TH:i:s",strtotime($model['created_at'])),
			'status'=>"Создан",
			'mol_guid'=>$model['mol_guid'],
            'mol_guid_recipient'=>$model['mol_guid_recipient'],
			'comment'=>$model['comment']
		];

		$params['materials'] = $model->materials;

		try {
			$method = new TransferOfMaterials();
			$request = new Request([
                'request'=>get_class($method),
                'params_in'=>json_encode($params),
                'resource_id'=>null,
                'actor_id'=>Yii::$app->user->id
            ]);


            $method->setParameters($params);

            if(!$request->validate()){
                Yii::warning("Request validate error","TransferOfMaterials");
                Yii::warning($request->getErrors(),"TransferOfMaterials");
                return false; 
            }

            

            if($request->send($method)){
                $responce = json_decode($request->params_out,1);

                $responce = isset($responce['return']) ? $responce['return'] : $responce;
                if(isset($responce['error'])){
                	Yii::warning("Error","TransferOfMaterials");
                	Yii::warning($responce['error'],"TransferOfMaterials");
                	Yii::$app->session->setFlash("warning","Ошибка при попытке отправить документ на проверку в 1С");
                	Yii::$app->session->setFlash("error",$responce['error']);
                }

                if($request->result && isset($responce['success']) && $responce['success']){
                	Yii::$app->session->setFlash("success","Документ отправлен на подтверждение в 1С");
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