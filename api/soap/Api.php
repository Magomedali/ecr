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
use common\models\TypeOfWork;
use common\models\Line;
use common\models\Nomenclature;
use common\models\Boundary;
use common\models\Objects;
use common\models\Project;
use common\models\Remnant;
use common\models\Raport;

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

            $message = YII_DEBUG ? $e->getMessage() : "Error on Server";
            return new Responce(['success'=>false,'error'=>"ServerError",'errorMessage'=>$message]);
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
    public static function unloadtechnic($technics){   
        self::log("Called Method 'unloadtechnic'");
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
        self::log("Called Method 'unloadobject'");
        self::log("Parameter Type:".gettype($objects));
        self::log("Parameter Value:".json_encode($objects));

        if(!is_array($objects)){
            throw new ApiExceptionWrongType();
        }

        $responce = new Responce();
        $erros = [];
        foreach ($objects as $key => $item) {
            $model = new Objects();
            self::log("Parameter Value:".gettype($item));
            
            //stdObject to array
            $arData = json_decode(json_encode($item),1);
            $params = ['Objects'=>$arData];

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
     * unload boundary
     * @param api\soap\models\Boundary[] $boundaries
     * @return api\soap\models\Responce
     */
    public static function unloadboundary($boundaries){   
        self::log("Called Method 'unloadboundary'");
        self::log("Parameter Type:".gettype($boundaries));
        self::log("Parameter Value:".json_encode($boundaries));

        if(!is_array($boundaries)){
            throw new ApiExceptionWrongType();
        }

        $responce = new Responce();
        $erros = [];
        foreach ($boundaries as $key => $item) {
            $model = new Boundary();
            self::log("Parameter Value:".gettype($item));
            
            //stdObject to array
            $arData = json_decode(json_encode($item),1);
            $params = ['Boundary'=>$arData];

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
     * unload projects
     * @param api\soap\models\Project[] $projects
     * @return api\soap\models\Responce
     */
    public static function unloadproject($projects){   
        self::log("Called Method 'unloadproject'");
        self::log("Parameter Type:".gettype($projects));
        self::log("Parameter Value:".json_encode($projects));

        if(!is_array($projects)){
            throw new ApiExceptionWrongType();
        }

        $responce = new Responce();
        $erros = [];
        foreach ($projects as $key => $item) {
            $model = new Project();
            self::log("Parameter Value:".gettype($item));
            
            //stdObject to array
            $arData = json_decode(json_encode($item),1);
            $params = ['Project'=>$arData];

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
     * @return api\soap\models\Responce
     */
    public static function unloadtypeofwork($works){   
        self::log("Called Method 'unloadtypeofwork'");
        self::log("Parameter Type:".gettype($works));
        self::log("Parameter Value:".json_encode($works));

        if(!is_array($works)){
            throw new ApiExceptionWrongType();
        }

        $responce = new Responce();
        $erros = [];
        foreach ($works as $key => $item) {
            $model = new TypeOfWork();
            self::log("Parameter Value:".gettype($item));
            
            //stdObject to array
            $arData = json_decode(json_encode($item),1);
            $params = ['TypeOfWork'=>$arData];

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
     * @return api\soap\models\Responce
     */
    public static function unloadline($lines){   
        self::log("Called Method 'unloadline'");
        self::log("Parameter Type:".gettype($lines));
        self::log("Parameter Value:".json_encode($lines));

        if(!is_array($lines)){
            throw new ApiExceptionWrongType();
        }

        $responce = new Responce();
        $erros = [];
        foreach ($lines as $key => $item) {
            $model = new Line();
            self::log("Parameter Value:".gettype($item));
            
            //stdObject to array
            $arData = json_decode(json_encode($item),1);
            $params = ['Line'=>$arData];

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
     * @return api\soap\models\Responce
     */
    public static function unloadnomenclature($nomenclatures){   
        self::log("Called Method 'unloadnomenclature'");
        self::log("Parameter Type:".gettype($nomenclatures));
        self::log("Parameter Value:".json_encode($nomenclatures));

        if(!is_array($nomenclatures)){
            throw new ApiExceptionWrongType();
        }

        $responce = new Responce();
        $erros = [];
        foreach ($nomenclatures as $key => $item) {
            $model = new Nomenclature();
            self::log("Parameter Value:".gettype($item));
            
            //stdObject to array
            $arData = json_decode(json_encode($item),1);
            $params = ['Nomenclature'=>$arData];

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
     * @return api\soap\models\Responce
     */
    public static function unloadremnant($remnants){   
        self::log("Called Method 'unloadremnant'");
        self::log("Parameter Type:".gettype($remnants));
        self::log("Parameter Value:".json_encode($remnants));

        if(!is_array($remnants)){
            throw new ApiExceptionWrongType();
        }

        $responce = new Responce();
        $erros = [];
        foreach ($remnants as $key => $item) {
            $model = new Remnant();
            self::log("Parameter Value:".gettype($item));
            
            //stdObject to array
            $arData = json_decode(json_encode($item),1);
            $params = ['Remnant'=>$arData];

            if(!$model->load($params) || !$model->save(1)){
                if(isset($arData['brigade_guid'])){
                   $erros[$arData['brigade_guid']] = json_encode($model->getErrors());
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
     * @return api\soap\models\Responce
     */
    public static function unloadraport($raports){   
        self::log("Called Method 'unloadraport'");
        self::log("Parameter Type:".gettype($raports));
        self::log("Parameter Value:".json_encode($raports));

        if(!is_array($raports)){
            throw new ApiExceptionWrongType();
        }

        $responce = new Responce();
        $erros = [];
        foreach ($raports as $key => $item) {
            $model = new Raport();
            self::log("Parameter Value:".gettype($item));
            
            //stdObject to array
            $arData = json_decode(json_encode($item),1);
            $params = ['Raport'=>$arData];

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


}