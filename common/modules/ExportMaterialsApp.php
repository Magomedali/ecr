<?php

namespace common\modules;

use Yii;
use yii\db\Query;
use common\models\MaterialsApp;
use common\models\MaterialsAppItem;
use common\models\Request;
use common\dictionaries\ExchangeStatuses;
use soapclient\methods\ApplicationForMaterials;

class ExportMaterialsApp{


	public static function export(MaterialsApp $model){
        
		$params = [
			'guid'=>$model['guid'],
			'date'=>date("Y-m-d\TH:i:s",strtotime($model['created_at'])),
			'status'=>ExchangeStatuses::getLabels($model['status']),
			'mol_guid'=>$model['user_guid'],
            'master_guid'=>$model['master_guid'],
			'warehouse_guid'=>$model['stockroom_guid'],
            'comment'=>''
		];

		$params['materials'] = (new Query())->select([
									'nomenclature_guid',
									'count'
								])
							->from(MaterialsAppItem::tableName())
							->where(['isDeleted'=>0,'material_app_id'=>$model['id']])
							->all();

		try {
			$method = new ApplicationForMaterials();
			$request = new Request([
                'request'=>get_class($method),
                'params_in'=>json_encode($params),
                'resource_id'=>$model['id'],
                'actor_id'=>Yii::$app->user->id
            ]);


            $method->setParameters($params);

            if(!$request->validate()){
                Yii::warning("Request validate error","ExportMaterialsApp");
                Yii::warning($request->getErrors(),"ExportMaterialsApp");
                return false; 
            }

            Yii::$app->db->createCommand()->update(Request::tableName(),['completed'=>1,'completed_at'=>date("Y-m-d\TH:i:s",time())],"`resource_id`=:resource_id AND `request`=:request AND  completed=0")
                ->bindValue(":request",$request->request)
                ->bindValue(":resource_id",$model['id'])
                ->execute();

            if($request->send($method)){
                $responce = json_decode($request->params_out,1);

                $responce = isset($responce['return']) ? $responce['return'] : $responce;
                if(isset($responce['error'])){
                	Yii::warning("Error","ExportMaterialsApp");
                	Yii::warning($responce['error'],"ExportMaterialsApp");
                	Yii::$app->session->setFlash("warning","Ошибка при попытке выгрузить заявку в 1С");
                	Yii::$app->session->setFlash("error",$responce['error']);
                }

                if($request->result && isset($responce['guid']) && $responce['guid'] && isset($responce['number']) && $responce['number']){

                    $model->guid = $responce['guid'];
                    $model->number = $responce['number'];

                    $model->status = ExchangeStatuses::IN_CONFIRMING;
               		
                	Yii::$app->session->setFlash("info","Заявка выгружена в 1С");
                    return $model->save(1);
                }
            }
            
        } catch (\Exception $e) {
            Yii::warning($e->getMessage(),'api');
        }
        
        return false;
	}
}