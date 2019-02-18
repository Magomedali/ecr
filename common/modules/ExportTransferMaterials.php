<?php

namespace common\modules;

use Yii;
use yii\db\Query;
use common\models\{Request,Document};
use common\modules\TransferMaterials;
use soapclient\methods\TransferOfMaterials;
use common\dictionaries\ExchangeStatuses;

class ExportTransferMaterials{


	public static function export(TransferMaterials $model){
        
		$params = [
            'guid'=>$model->guid,
			'date'=>date("Y-m-d\TH:i:s",strtotime($model['date'])),
			'status'=>$model['status'],
			'mol_guid'=>$model['mol_guid'],
            'mol_guid_recipient'=>$model['mol_guid_recipient'],
			'comment'=>$model['comment']
		];

		$params['materials'] = $model->materials;
        $user_id = Yii::$app->user->id;
        
		try {
			$method = new TransferOfMaterials();
			
            $request = null;
            $req_params = [
                'request'=>get_class($method),
                'params_in'=>json_encode($params),
                'resource_id'=>$model->guid,
                'user_id'=>$user_id,
                'actor_id'=>$user_id
            ];

            if($model->requestId){
                $request = Request::findOne($model->requestId);
            }elseif($model->guid){
                //Предыдущие запросы по этому документы закрываем
                Yii::$app->db->createCommand()->update(Request::tableName(),['completed'=>1,'completed_at'=>date("Y-m-d\TH:i:s",time())],"`resource_id`=:resource_id AND `request`=:request AND  completed=0")
                ->bindValue(":request",$request->request)
                ->bindValue(":resource_id",$model->guid)
                ->execute();
            }

            if(!isset($request->id) || !$request->load(['Request'=>$req_params])){
                $request = new Request($req_params);
            }
            

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