<?php

namespace api\soap;

use Yii;
use Exception;
use api\soap\models\Responce;
use api\soap\Exceptions\ApiException;
use api\soap\Exceptions\ApiExceptionMethodNotExists;
use api\soap\Exceptions\ApiExceptionWrongType;

use common\models\Brigade;
use common\models\Technic;
use common\models\User;

class Api{


    public static function log($msg){
        Yii::info($msg,'api');
    }

	/**
	* @return Responce
	*/
	public static function exec($method,$params){

		try {
        	
        	if(!method_exists(__CLASS__, $method))
        		throw new ApiExceptionMethodNotExists();
        	
        	return self::$method($params); 
        
        }catch(ApiException $e) {
        
            return $e->getResponce();
        
        }catch(Exception $e){
            return new Responce(['success'=>false,'error'=>"ServerError",'errorMessage'=>$e->getMessage()]);
        }

	}




	/**
	* @return Responce
	*/
	public static function test($msg){
		return new Responce(['success'=>true,'error'=>$msg]);
	}






    /**
     * unload Brigades
     * @param api\soap\models\Brigade[] $brigades
     * @return api\soap\models\Responce
     */
    public static function unloadbrigade($brigades){
        
        self::log("Called Method 'unloadbrigade'");
        self::log("Parameter Type:".gettype($brigades));
        self::log("Parameter Value:".json_encode($brigades));

        if(!is_array($brigades)){
            throw new ApiExceptionWrongType();
        }

        $responce = new Responce();
        $erros = [];
        foreach ($brigades as $key => $br) {
            $brigade = new Brigade();
            self::log("Parameter Value:".gettype($br));
            
            //stdObject to array
            $arBrigade = json_decode(json_encode($br),1);
            $params = ['Brigade'=>$arBrigade];

            if(!$brigade->load($params) || !$brigade->save(1)){
                if(isset($arBrigade['guid'])){
                   $erros[$arBrigade['guid']] = json_encode($brigade->getErrors());
                }
                $responce->success = false;
            }else{
                $responce->success = true;
            }
        }

        if(count($erros)){
            $responce->success = false;
            $responce->errorsExtend = $erros;
        }

        return $responce;
    }





    /**
     * unload workers
     * @param api\soap\models\Worker[] $workers
     * @return api\soap\models\Responce
     */
    public static function unloadworker($workers){   
        self::log("Called Method 'unloadworker'");
        self::log("Parameter Type:".gettype($workers));
        self::log("Parameter Value:".json_encode($workers));

        if(!is_array($workers)){
            throw new ApiExceptionWrongType();
        }

        $responce = new Responce();
        $erros = [];
        foreach ($workers as $key => $wr) {
            $worker = new User();
            self::log("Parameter Value:".gettype($wr));
            
            //stdObject to array
            $arWorker = json_decode(json_encode($wr),1);
            $params = ['User'=>$arWorker];

            if(!$worker->load($params) || !$worker->save(1)){
                if(isset($arWorker['guid'])){
                   $erros[$arWorker['guid']] = json_encode($worker->getErrors());
                }
                $responce->success = false;
            }else{
                $responce->success = true;
            }
        }

        if(count($erros)){
            $responce->success = false;
            $responce->errorsExtend = $erros;
        }

        return $responce;
    }






    /**
     * unload technics
     * @param api\soap\models\Technic[] $technics
     * @return api\soap\models\Responce
     */
    public static function unloadtechnics($technics){   
        self::log("Called Method 'unloadbrigade'");
        self::log("Parameter Type:".gettype($technics));
        self::log("Parameter Value:".json_encode($technics));

        if(!is_array($technics)){
            throw new ApiExceptionWrongType();
        }

        $responce = new Responce();
        $erros = [];
        foreach ($technics as $key => $br) {
            $model = new Technic();
            self::log("Parameter Value:".gettype($br));
            
            //stdObject to array
            $arData = json_decode(json_encode($br),1);
            $params = ['Technic'=>$arData];

            if(!$model->load($params) || !$model->save(1)){
                if(isset($arData['guid'])){
                   $erros[$arData['guid']] = json_encode($model->getErrors());
                }
                $responce->success = false;
            }else{
                $responce->success = true;
            }
        }

        if(count($erros)){
            $responce->success = false;
            $responce->errorsExtend = $erros;
        }

        return $responce;
    }






    /**
     * unload objects
     * @param api\soap\models\Objects[] $objects
     * @return api\soap\models\Responce
     */
    public static function unloadobject($objects){   
        $responce = new Responce(['success'=>true]);
        return $responce;
    }





    /**
     * unload boundary
     * @param api\soap\models\Boundary[] $boundaries
     * @return api\soap\models\Responce
     */
    public static function unloadboundary($boundaries){   
        $responce = new Responce(['success'=>true]);
        return $responce;
    }

    




    /**
     * unload projects
     * @param api\soap\models\Project[] $projects
     * @return api\soap\models\Responce
     */
    public static function unloadproject($projects){   
        $responce = new Responce(['success'=>true]);
        return $responce;
    }




    /**
     * @return api\soap\models\Responce
     */
    public static function unloadtypeofwork($works){   
        $responce = new Responce(['success'=>true]);
        return $responce;
    }





    /**
     * @return api\soap\models\Responce
     */
    public static function unloadline($lines){   
        $responce = new Responce(['success'=>true]);
        return $responce;
    }




    /**
     * @return api\soap\models\Responce
     */
    public static function unloadnomenclature($nomenclatures){   
        $responce = new Responce(['success'=>true]);
        return $responce;
    }





    /**
     * @return api\soap\models\Responce
     */
    public static function unloadraport($raports){   
        $responce = new Responce(['success'=>true]);
        return $responce;
    }

}