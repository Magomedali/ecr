<?php 

namespace console\models;

use Yii;
use yii\db\Query;
use common\models\{RaportRegulatory};
use common\dictionaries\ExchangeStatuses;

class RMRaportRegulatoryLoad extends RequestMethod{

	public function repeat(){

		$r = $this->request;

        $method = $r['request'];
        if(!class_exists($method)) return null;
        $method = new $r['request'];
		
		
		echo "Call RMRaportRegulatoryLoad::repeat\n";

		if(!$r->resource_id){
            Yii::info('resource_id dont have','cron');
            return false;
        }

        $model = RaportRegulatory::findOne([$r->resource_id]);
        if(!isset($model->id)){
            Yii::info('RaportRegulatory dont have','cron');
            echo "RaportRegulatory dont have \n";
            return false;
        }
        
        $params = json_decode($r['params_in'],1);

        $method->setParameters($params);
        if($r->send($method)){
            Yii::info('Method executed success','cron');
            echo "Method executed success\n";

            Yii::info($r->params_out,'cron');
            echo $r->params_out,"\n";
            $responce = json_decode($r->params_out,1);
            if($r->result && isset($responce['return']) && isset($responce['return']['guid']) && $responce['return']['guid'] && isset($responce['return']['number']) && $responce['return']['number']){
                $model->guid = $responce['return']['guid'];
                $model->number = $responce['return']['number'];

                if($model->status == ExchangeStatuses::CREATED){
                    $model->status = ExchangeStatuses::IN_CONFIRMING;
                }

                $model->save(1);
                echo "RaportRegulatory on confirmation\n";

                return true;
            }else{
                echo "Responce doesn`t have guid or number\n";
            } 

        }else{
            Yii::info('Method not executed','cron');
            echo "Method not executed \n";

            Yii::info($r->params_out,'cron');
            echo $r->params_out,"\n";
        }

        return false;
	}
}

?>