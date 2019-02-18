<?php 

namespace console\models;

use Yii;
use common\models\Request;

class RMSimple extends RequestMethod{

	public function repeat(){

		$r = $this->request;
		$method = $r['request'];
		if(!class_exists($method)) return null;
		echo "Call Method {$method}\n";
		$method = new $r['request'];



		$params = json_decode($r['params_in'],1);
        $method->setParameters($params);

        if($method->validate() && $r->send($method)){
            Yii::info('Method executed success','cron');
        }else{
            Yii::info('Method not executed','cron');
            Yii::info($r->params_out,'cron');
        }
	}
}

?>