<?php

namespace common\modules;

use Yii;
use Exception;
use yii\db\Query;
use common\models\Request;
use common\models\{Raport,RaportConsist,RaportWork,RaportMaterial,RaportFile};
use common\dictionaries\ExchangeStatuses;
use soapclient\methods\RaportLoad;

class ExportRaportLoad{

    const ID_PREFIX = "RS_";

	public static function export(Raport $model){
        
		if(!$model->id) return false;

        try {
            $method = new RaportLoad();
            $params = $model->getAttributes(null,[
                'id',
                'status',
                'isDeleted',
                'version_id',
                'number'
            ]);
            
            $params['id_site'] = self::ID_PREFIX.$model->id;
            $params['status'] = $model->statusTitle;
            
            $params['works'] = (new Query)->select(['work_guid','line_guid','mechanized','length','count','percent_save','squaremeter'])->from(RaportWork::tableName())->where(['raport_id'=>$model->id])->all();

            $params['consist'] = (new Query)->select(['user_guid','technic_guid'])->from(RaportConsist::tableName())->where(['raport_id'=>$model->id])->all();
            
            $materials = (new Query)->select(['nomenclature_guid','spent as count'])->from(RaportMaterial::tableName())->where(['raport_id'=>$model->id])->all();
            
            if(is_array($materials) && count($materials)){
                $params['materials'] = $materials;
            }
            
            
            $user = Yii::$app->user->identity;

            $files = (new Query)->select(['file_binary as file','file_type as type','file_name'])->from(RaportFile::tableName())->where(['raport_id'=>$model->id])->all();

            $minFiles = [];
            foreach ($files as $key => $f) {
                 $minFiles[$key]['type'] = $f['type'];
                 $minFiles[$key]['file_name'] = $f['file_name'];
            } 

            $params['files'] = $minFiles;

            $request = new Request([
                'request'=>get_class($method),
                'params_in'=>json_encode($params),
                'resource_id'=>$model->id,
                'actor_id'=>$user->id
            ]);

            $params['files'] = $files;

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
                        
                        return $model->save(1);
                }
            }
                 
            
        } catch (\Exception $e) {
            Yii::warning($e->getMessage(),'api');
            throw $e;
        }
        
        return false;
	}
}