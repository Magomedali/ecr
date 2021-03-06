<?php

namespace api\soap;

use Yii;
use Exception;
use yii\helpers\ArrayHelper;
use api\soap\models\Responce;
use api\soap\Exceptions\ApiException;
use api\soap\Exceptions\ApiExceptionMethodNotExists;
use api\soap\Exceptions\ApiExceptionWrongType;

use common\dictionaries\{ExchangeStatuses,AppStatuses};
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
use common\models\StockRoom;
use common\models\Setting;
use common\models\RaportRegulatory;
use common\models\{ProjectStandard,MaterialsApp};


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
        Yii::info($responce->toString(),"return");
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

        if(ArrayHelper::isAssociative($data[$Type])){
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

        if(ArrayHelper::isAssociative($data[$Type])){
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

        if(ArrayHelper::isAssociative($data[$Type])){
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

        if(ArrayHelper::isAssociative($data[$Type])){
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

        if(ArrayHelper::isAssociative($data[$Type])){
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

        if(ArrayHelper::isAssociative($data[$Type])){
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

        if(ArrayHelper::isAssociative($data[$Type])){
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
                $model->saveRelationWithNomenclatures();
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

        if(ArrayHelper::isAssociative($data[$Type])){
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

        if(ArrayHelper::isAssociative($data[$Type])){
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
     * unload stockrooms
     * @param api\soap\models\StockRoom[] $stockrooms
     * @return api\soap\models\Responce
     */
    public static function unloadstockroom($data){   
        self::log("Called Method 'unloadstockroom'");
        self::log("Parameter Type:".gettype($data));
        self::log("Parameter Value:".json_encode($data));

        $Type = "StockRoom";
        $data = json_decode(json_encode($data),1);
        if(!is_array($data) || !isset($data[$Type])){
            throw new ApiExceptionWrongType();
        }

        if(ArrayHelper::isAssociative($data[$Type])){
            $data[$Type] =  [$data[$Type]];
        }

        $responce = new Responce();
        $erros = [];
        foreach ($data[$Type] as $key => $item) {
            $model = new StockRoom();
            
            //stdObject to array
            $arData = json_decode(json_encode($item),1);
            $params = ['StockRoom'=>$arData];

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

        if(ArrayHelper::isAssociative($data[$Type])){
            $data[$Type] =  [$data[$Type]];
        }

        $responce = new Responce();
        $erros = [];
        foreach ($data[$Type] as $key => $item) {
            $model = new RemnantsPackage();
            
            //stdObject to array
            $arData = json_decode(json_encode($item),1);
            $params = ['RemnantsPackage'=>$arData];

            if(!$model->load($params) || !$model->savePackage()){
                if(isset($arData['user_guid'])){
                   $erros[$arData['user_guid']] = json_encode($model->getErrors());
                }
                $responce->success = false;
            }else{
                
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

        if(ArrayHelper::isAssociative($data[$Type])){
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
                $responce->error = "ValidationError";
                $responce->errorMessage = "Wrong data in the header";
                $responce->errorUserMessage = "Ошибка, при валидации даных основной части!";
                $responce->errorsExtend = $erros;
            }else{
                $responce->success = true;
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

                
                if(count($tablePartsErrors)){
                    $responce->success = false;
                    $responce->error = "ErrorInRelationData";
                    $responce->errorMessage = "Wrong data in the table data";
                    $responce->errorUserMessage = "Ошибка, при валидации даных в табличной части!";
                    $responce->errorsExtend = $tablePartsErrors;
                }
                
            }
        }

        return $responce;
    }





    /**
     * unload regulatoryraports
     * @param api\soap\models\RaportRegulatory[] $regulatoryraports
     * @return api\soap\models\Responce
     */
    public static function unloadraportregulatory($data){   
        self::log("Called Method 'unloadraportregulatory'");
        self::log("Parameter Type:".gettype($data));
        self::log("Parameter Value:".json_encode($data));

        $Type = "RaportRegulatory";
        $data = json_decode(json_encode($data),1);
        if(!is_array($data) || !isset($data[$Type])){
            throw new ApiExceptionWrongType();
        }

        if(ArrayHelper::isAssociative($data[$Type])){
            $data[$Type] =  [$data[$Type]];
        }

        $responce = new Responce();
        $erros = [];
        foreach ($data[$Type] as $key => $item) {
            $model = new RaportRegulatory();
            
            //stdObject to array
            $arData = json_decode(json_encode($item),1);
            $params = ['RaportRegulatory'=>$arData];

            if(!$model->load($params) || !$model->save(1)){
                if(isset($arData['guid'])){
                   $erros[$arData['guid']] = json_encode($model->getErrors());
                }
                $responce->success = false;
                $responce->error = "ValidationError";
                $responce->errorMessage = "Wrong data in the header";
                $responce->errorUserMessage = "Ошибка, при валидации даных основной части!";
                $responce->errorsExtend = $erros;
            }else{
                $responce->success = true;
                $model->saveRelationEntities();
                
                $tablePartsErrors = [];

                if(count($model->getWorksErrors())){
                    $tablePartsErrors['works'] = json_encode($model->getWorksErrors());
                }

                if(count($tablePartsErrors)){
                    $responce->success = false;
                    $responce->error = "ErrorInRelationData";
                    $responce->errorMessage = "Wrong data in the table data";
                    $responce->errorUserMessage = "Ошибка, при валидации даных в табличной части!";
                    $responce->errorsExtend = $tablePartsErrors;
                }
                
            }
        }

        return $responce;
    }




    /**
     * unload setting
     * @param api\soap\models\Setting $setting
     * @return api\soap\models\Responce
     */
    public static function unloadsettings($data){   
        self::log("Called Method 'unloadsettings'");
        self::log("Parameter Type:".gettype($data));
        self::log("Parameter Value:".json_encode($data));


        $Type = "Setting";
        $data = json_decode(json_encode($data),1);
        if(!is_array($data)){
            throw new ApiExceptionWrongType();
        }
        
        $responce = new Responce();
        $erros = [];
        $model = new Setting();
            
        $params = ['Setting'=>$data];

        if(!$model->load($params) || !$model->save()){
            $erros[] = json_encode($model->getErrors());
            $responce->success = false;
        }else{
            $responce->success = true;
        }


        if(count($erros)){
            $responce->success = false;
            $responce->errorsExtend = $erros;
        }
        return $responce;
    }




    public static function updateapplicationstatus($data){
        self::log("Called Method 'updateapplicationstatus'");
        self::log("Parameter Type:".gettype($data));
        self::log("Parameter Value:".json_encode($data));


        $Type = "ApplicationStatus";
        $data = json_decode(json_encode($data),1);
        if(!is_array($data)  || !isset($data['guid']) || !isset($data['status'])){
            throw new ApiExceptionWrongType();
        }
        
        $responce = new Responce();
        $erros = [];
        $model = MaterialsApp::findOne(['guid'=>$data['guid']]);
        
        if((!isset($model->id) || !$model->id) && isset($data['id_site']) && (int)$data['id_site']){
            $model = MaterialsApp::findOne(['id'=>(int)$data['id_site']]);
        }

        if(!isset($model->id) || !$model->id){
            $responce->success = false;
            $responce->error = "MaterialsAppNotFounded";
            $responce->errorMessage = "Not founded materials app by guid and by id_site";
            $responce->errorUserMessage = "Заявка не найдена!";
            return $responce;
        }

        $model->setStatusFromTransferValue($data['status']);
        $model->status = $model->status <= AppStatuses::CREATED ? AppStatuses::IN_CONFIRMING : $model->status;
        
        if(!$model->save(1)){
            $erros[] = json_encode($model->getErrors());
            $responce->success = false;
        }else{
            $responce->success = true;
        }


        if(count($erros)){
            $responce->success = false;
            $responce->errorsExtend = $erros;
        }
        return $responce;
    }


    /**
     * unload projectstandards
     * @param api\soap\models\ProjectStandard $projectstandards
     * @return api\soap\models\Responce
     */
    public static function unloadprojectstandard($data){   
        self::log("Called Method 'unloadprojectstandard'");
        self::log("Parameter Type:".gettype($data));
        self::log("Parameter Value:".json_encode($data));


        $Type = "ProjectStandard";
        $data = json_decode(json_encode($data),1);
        if(!is_array($data) || !isset($data[$Type])){
            throw new ApiExceptionWrongType();
        }

        if(ArrayHelper::isAssociative($data[$Type])){
            $data[$Type] =  [$data[$Type]];
        }
        
        $responce = new Responce();
        $erros = [];
        foreach ($data[$Type] as $key => $item) {
            $model = new ProjectStandard();
            
            //stdObject to array
            $arData = json_decode(json_encode($item),1);
            $params = ['ProjectStandard'=>$arData];

            if(!$model->load($params) || !$model->save(1)){
                $erros = $model->getErrors();
                
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