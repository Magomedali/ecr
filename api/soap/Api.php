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
use common\models\RemnantsPackage;
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
        	
        	$responce = self::$method($params); 
        
        }catch(ApiException $e) {
        
            $responce = $e->getResponce();
        
        }catch(Exception $e){
            $message = YII_DEBUG ? $e->getMessage() : "Error on Server";
            $responce = new Responce(['success'=>false,'error'=>"ServerError",'errorMessage'=>$message]);
        }
        //self::log(json_encode($params));
        //$responce = new Responce(['success'=>true,'error'=>"ServerError",'errorMessage'=>"Test"]);

        if(0){
            //rpc
            return $responce;
        }else{
            //document
            $r = new \stdClass();
            $r->returns = $responce;
            return $r;
        }
	}




	/**
	* @return Responce
	*/
	public static function test($msg){
		return new Responce(['success'=>true,'error'=>json_encode($msg)]);
	}






    /**
     * unload Brigades
     * @param api\soap\models\Brigade[] $brigades
     * @return api\soap\models\Responce
     */
    public static function unloadbrigade($data){
        self::log("Called Method 'unloadbrigade'");
        self::log("Parameter Type:".gettype($data));
        self::log("Parameter Value:".json_encode($data));

        $Type = "Brigade";
        $data = json_decode(json_encode($data),1);
        if(!is_array($data) || !isset($data[$Type])){
            throw new ApiExceptionWrongType();
        }

        if(!array_key_exists(0, $data[$Type])){
            $data[$Type] =  [$data[$Type]];
        }

        $responce = new Responce();
        $erros = [];
        foreach ($data[$Type] as $key => $item) {
            $brigade = new Brigade();
            self::log("Parameter Value:".gettype($item));
            
            //stdObject to array
            $arBrigade = json_decode(json_encode($item),1);
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
    public static function unloadworker($data){   
        self::log("Called Method 'unloadworker'");
        self::log("Parameter Type:".gettype($data));
        self::log("Parameter Value:".json_encode($data));

        $Type = "Worker";
        $data = json_decode(json_encode($data),1);
        if(!is_array($data) || !isset($data[$Type])){
            throw new ApiExceptionWrongType();
        }

        if(!array_key_exists(0, $data[$Type])){
            $data[$Type] =  [$data[$Type]];
        }

        $responce = new Responce();
        $erros = [];
        foreach ($data[$Type] as $key => $item) {
            $worker = new User();
            
            //stdObject to array
            $arWorker = json_decode(json_encode($item),1);
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
    public static function unloadtechnic($data){   
        self::log("Called Method 'unloadtechnic'");
        self::log("Parameter Type:".gettype($data));
        self::log("Parameter Value:".json_encode($data));

        $Type = "Technic";
        $data = json_decode(json_encode($data),1);
        if(!is_array($data) || !isset($data[$Type])){
            throw new ApiExceptionWrongType();
        }

        if(!array_key_exists(0, $data[$Type])){
            $data[$Type] =  [$data[$Type]];
        }

        $responce = new Responce();
        $erros = [];
        foreach ($data[$Type] as $key => $item) {
            $model = new Technic();
            //stdObject to array
            $arData = json_decode(json_encode($item),1);
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
    public static function unloadobject($data){   
        self::log("Called Method 'unloadobject'");
        self::log("Parameter Type:".gettype($data));
        self::log("Parameter Value:".json_encode($data));

        $Type = "Objects";
        $data = json_decode(json_encode($data),1);
        if(!is_array($data) || !isset($data[$Type])){
            throw new ApiExceptionWrongType();
        }

        if(!array_key_exists(0, $data[$Type])){
            $data[$Type] =  [$data[$Type]];
        }

        $responce = new Responce();
        $erros = [];
        foreach ($data[$Type] as $key => $item) {
            $model = new Objects();
            
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
    public static function unloadboundary($data){   
        self::log("Called Method 'unloadboundary'");
        self::log("Parameter Type:".gettype($data));
        self::log("Parameter Value:".json_encode($data));

        $Type = "Boundary";
        $data = json_decode(json_encode($data),1);
        if(!is_array($data) || !isset($data[$Type])){
            throw new ApiExceptionWrongType();
        }

        if(!array_key_exists(0, $data[$Type])){
            $data[$Type] =  [$data[$Type]];
        }

        $responce = new Responce();
        $erros = [];
        foreach ($data[$Type] as $key => $item) {
            $model = new Boundary();
            
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
    public static function unloadproject($data){   
        self::log("Called Method 'unloadproject'");
        self::log("Parameter Type:".gettype($data));
        self::log("Parameter Value:".json_encode($data));

        $Type = "Project";
        $data = json_decode(json_encode($data),1);
        if(!is_array($data) || !isset($data[$Type])){
            throw new ApiExceptionWrongType();
        }

        if(!array_key_exists(0, $data[$Type])){
            $data[$Type] =  [$data[$Type]];
        }

        $responce = new Responce();
        $erros = [];
        foreach ($data[$Type] as $key => $item) {
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
                $model->saveRelationEntities();
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
     * unload TypeOfWork
     * @param api\soap\models\TypeOfWork[] $works
     * @return api\soap\models\Responce
     */
    public static function unloadtypeofwork($data){   
        self::log("Called Method 'unloadtypeofwork'");
        self::log("Parameter Type:".gettype($data));
        self::log("Parameter Value:".json_encode($data));

        $Type = "TypeOfWork";
        $data = json_decode(json_encode($data),1);
        if(!is_array($data) || !isset($data[$Type])){
            throw new ApiExceptionWrongType();
        }

        if(!array_key_exists(0, $data[$Type])){
            $data[$Type] =  [$data[$Type]];
        }

        $responce = new Responce();
        $erros = [];
        foreach ($data[$Type] as $key => $item) {
            $model = new TypeOfWork();
            
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
     * unload lines
     * @param api\soap\models\Line[] $lines
     * @return api\soap\models\Responce
     */
    public static function unloadline($data){   
        self::log("Called Method 'unloadline'");
        self::log("Parameter Type:".gettype($data));
        self::log("Parameter Value:".json_encode($data));

        $Type = "Line";
        $data = json_decode(json_encode($data),1);
        if(!is_array($data) || !isset($data[$Type])){
            throw new ApiExceptionWrongType();
        }

        if(!array_key_exists(0, $data[$Type])){
            $data[$Type] =  [$data[$Type]];
        }

        $responce = new Responce();
        $erros = [];
        foreach ($data[$Type] as $key => $item) {
            $model = new Line();
            
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
     * unload nomenclatures
     * @param api\soap\models\Nomenclature[] $nomenclatures
     * @return api\soap\models\Responce
     */
    public static function unloadnomenclature($data){   
        self::log("Called Method 'unloadnomenclature'");
        self::log("Parameter Type:".gettype($data));
        self::log("Parameter Value:".json_encode($data));

        $Type = "Nomenclature";
        $data = json_decode(json_encode($data),1);
        if(!is_array($data) || !isset($data[$Type])){
            throw new ApiExceptionWrongType();
        }

        if(!array_key_exists(0, $data[$Type])){
            $data[$Type] =  [$data[$Type]];
        }

        $responce = new Responce();
        $erros = [];
        foreach ($data[$Type] as $key => $item) {
            $model = new Nomenclature();
            
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
     * unload RemnantsPackage
     * @param api\soap\models\RemnantsPackage[] $remnants
     * @return api\soap\models\Responce
     */
    public static function unloadremnant($data){   
        self::log("Called Method 'unloadremnant'");
        self::log("Parameter Type:".gettype($data));
        self::log("Parameter Value:".json_encode($data));

        $Type = "RemnantsPackage";
        $data = json_decode(json_encode($data),1);
        if(!is_array($data) || !isset($data[$Type])){
            throw new ApiExceptionWrongType();
        }

        if(!array_key_exists(0, $data[$Type])){
            $data[$Type] =  [$data[$Type]];
        }

        $responce = new Responce();
        $erros = [];
        foreach ($data[$Type] as $key => $item) {
            $model = new RemnantsPackage();
            
            //stdObject to array
            $arData = json_decode(json_encode($item),1);
            $params = ['RemnantsPackage'=>$arData];

            if(!$model->load($params) || !$model->save(1)){
                if(isset($arData['brigade_guid'])){
                   $erros[$arData['brigade_guid']] = json_encode($model->getErrors());
                }
                $responce->success = false;
            }else{


                $model->saveRelationEntities();
                
                $tablePartsErrors = [];
                if(count($model->getItemsErrors())){
                    $tablePartsErrors['items'] = json_encode($model->getItemsErrors());
                }

                $responce->success = true;
                $erros = $tablePartsErrors;
            }
        }

        if(count($erros)){
            $responce->success = false;
            $responce->errorsExtend = $erros;
        }

        return $responce;
    }




    /**
     * unload raports
     * @param api\soap\models\Raport[] $raports
     * @return api\soap\models\Responce
     */
    public static function unloadraport($data){   
        self::log("Called Method 'unloadraport'");
        self::log("Parameter Type:".gettype($data));
        self::log("Parameter Value:".json_encode($data));

        $Type = "Raport";
        $data = json_decode(json_encode($data),1);
        if(!is_array($data) || !isset($data[$Type])){
            throw new ApiExceptionWrongType();
        }

        if(!array_key_exists(0, $data[$Type])){
            $data[$Type] =  [$data[$Type]];
        }

        $responce = new Responce();
        $erros = [];
        foreach ($data[$Type] as $key => $item) {
            $model = new Raport();
            
            //stdObject to array
            $arData = json_decode(json_encode($item),1);
            $params = ['Raport'=>$arData];

            if(!$model->load($params) || !$model->save(1)){
                if(isset($arData['guid'])){
                   $erros[$arData['guid']] = json_encode($model->getErrors());
                }
                $responce->success = false;
                $responce->errorsExtend = $erros;
            }else{
                $model->saveRelationEntities();
                $tablePartsErrors = [];
                if(count($model->getMaterialsErrors())){
                    $tablePartsErrors['materials'] = json_encode($model->getMaterialsErrors());
                }

                if(count($model->getConsistErrors())){
                    $tablePartsErrors['consist'] = json_encode($model->getConsistErrors());
                }

                if(count($model->getWorksErrors())){
                    $tablePartsErrors['works'] = json_encode($model->getWorksErrors());
                }

                $responce->success = true;
                $responce->errorsExtend = $tablePartsErrors;
            }
        }

        return $responce;
    }


}