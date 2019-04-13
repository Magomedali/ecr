<?php

namespace common\modules;

use Yii;
use Exception;
use yii\db\Query;
use common\models\Request;
use common\models\{RaportRegulatory,RaportRegulatoryWork};
use common\dictionaries\ExchangeStatuses;
use soapclient\methods\RaportRegulatoryLoad;


class ExportRaportRegulatoryLoad{


	public static function export(RaportRegulatory $model){
        
		if(!$model->id) return false;

        try {
            $method = new RaportRegulatoryLoad();
            $params = $model->getAttributes(null,[
                'id',
                'isDeleted',
                'version_id',
                'number',
                'status'
            ]);

            $params['id_site'] = $model->id;
            
            $params['status'] = $model->statusTitle;
            
            $params['works'] = (new Query)->select(['work_guid','user_guid','hours'])
                                        ->from(RaportRegulatoryWork::tableName())
                                        ->where(['raport_regulatory_id'=>$model->id])
                                        ->all();

            $user = Yii::$app->user->identity;

            $request = new Request([
                'request'=>get_class($method),
                'params_in'=>json_encode($params),
                'resource_id'=>$model->id,
                'actor_id'=>$user->id
            ]);


            $method->setParameters($params);

            if(!$request->validate()){
                Yii::error("Request validate error","api");
                Yii::error($request->getErrors(),"api");
                return false; 
            }
            
            Yii::$app->db->createCommand()->update(Request::tableName(),['completed'=>1,'completed_at'=>date("Y-m-d\TH:i:s",time())],"`resource_id`=:resource_id AND `request`=:request AND  completed=0")
                ->bindValue(":request",$request->request)
                ->bindValue(":resource_id",$model->id)
                ->execute();

            if($request->send($method)){
                $responce = json_decode($request->params_out,1);

                if($request->result && isset($responce['return']) && isset($responce['return']['guid']) && $responce['return']['guid'] && isset($responce['return']['number']) && $responce['return']['number']){
                    
                    $model->guid = $responce['return']['guid'];
                    $model->number = $responce['return']['number'];

                    if($model->status == ExchangeStatuses::CREATED){
                        $model->status = ExchangeStatuses::IN_CONFIRMING;
                    }
                    $model->save(1);
                    return true;
                }
            }
                 
            
        } catch (\Exception $e) {
            Yii::warning($e->getMessage(),'api');
        }
        
        return false;
	}
}