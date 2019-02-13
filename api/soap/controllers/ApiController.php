<?php
namespace api\soap\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use api\soap\models\Brigade;
use api\soap\models\Responce;
use api\soap\Exceptions\ApiException;
use api\soap\Exceptions\ApiExceptionWrongType;
use api\soap\Exceptions\ApiExceptionNotAuthenticated;
use api\soap\Api;

/**
 * Api controller
 */
class ApiController extends Controller
{   
    protected $authenticated = true;

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'index' => [
                'class'=>'api\soap\Action',
                'serviceOptions'=>[
                    'disableWsdlMode'=>false
                ]
            ]
        ];
    }
    
    


    public function authenticate($header)
    {   
        $params = Yii::$app->params;
        if(is_array($params) && isset($params['SoapApi']) && isset($params['SoapApi']['api_credentials']) && is_array($params['SoapApi']['api_credentials'])){
            
            $username = key($params['SoapApi']['api_credentials']);
            $password = reset($params['SoapApi']['api_credentials']);

            if($header->username == $username && $header->password == $password)
                $this->authenticated = true;

        }
    }
   






    /**
     * @inheritdoc
     */
    protected function isAuth()
    {
        return $this->authenticated && 1;
    }



    /**
     * @inheritdoc
     */
    public function exec($method,$params){
        if($this->isAuth()){
            $methodName = str_replace(__CLASS__."::", "", $method);
            return Api::exec($methodName,$params);
        }else{
            $responce = new Responce(['success'=>false,'error'=>"AuthenticateError",'errorMessage'=>'didn`t set username and password or wrong values']);
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
    }
    



    /**
     * unload Brigades
     * @param api\soap\models\Brigade[] $brigades
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadbrigade($brigades){
        $type = 'brigades';
        $brigades = json_decode(json_encode($brigades),1); 
        if(!isset($brigades[$type]))
            return new ApiExceptionWrongType("WrongType","Packet doesn`t have parameter 'brigades'");
    
        
        return $this->exec(__METHOD__,$brigades[$type]);
    }


    /**
     * Stest method
     * @param string $msg
     * @return api\soap\models\Responce
     * @soap
     */
    public function test($msg){   
        return $this->exec(__METHOD__,$msg);
    }


    /**
     * Добавление и изменение информации о пользователе
     * *Замечание для реквизита status |
     * status int    nullable  values : 10 - Восстановление пользователя, 0 - Для архивирования пользователя
     * @param api\soap\models\Worker[] $workers 
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadworker($workers){   
        $type = 'workers';
        $workers = json_decode(json_encode($workers),1); 
        if(!isset($workers[$type]))
            return new ApiExceptionWrongType("WrongType","Packet doesn`t have parameter 'workers'");

        return $this->exec(__METHOD__,$workers[$type]);
    }






    /**
     * unload technics
     * @param api\soap\models\Technic[] $technics
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadtechnic($technics){   
        $type = 'technics';
        $technics = json_decode(json_encode($technics),1); 
        if(!isset($technics[$type]))
            return new ApiExceptionWrongType("WrongType","Packet doesn`t have parameter 'technics'");

        return $this->exec(__METHOD__,$technics[$type]);
    }






    /**
     * unload objects
     * @param api\soap\models\Objects[] $objects
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadobject($objects){   
        $type = 'objects';
        $objects = json_decode(json_encode($objects),1); 
        if(!isset($objects[$type]))
            return new ApiExceptionWrongType("WrongType","Packet doesn`t have parameter 'objects'");

        return $this->exec(__METHOD__,$objects[$type]);
    }





    /**
     * unload boundary
     * @param api\soap\models\Boundary[] $boundaries
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadboundary($boundaries){   
        $type = 'boundaries';
        $boundaries = json_decode(json_encode($boundaries),1); 
        if(!isset($boundaries[$type]))
            return new ApiExceptionWrongType("WrongType","Packet doesn`t have parameter 'boundaries'");

        return $this->exec(__METHOD__,$boundaries[$type]);
    }

    




    /**
     * unload projects
     * @param api\soap\models\Project[] $projects
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadproject($projects){   
        
        $type = 'projects';
        $projects = json_decode(json_encode($projects),1); 
        if(!isset($projects[$type]))
           return new ApiExceptionWrongType("WrongType","Packet doesn`t have parameter 'projects'");

        return $this->exec(__METHOD__,$projects[$type]);
    }




    /**
     * unload typeofworks
     * @param api\soap\models\TypeOfWork[] $works
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadtypeofwork($works){   
        $type = 'works';
        $works = json_decode(json_encode($works),1); 
        if(!isset($works[$type]))
            return new ApiExceptionWrongType("WrongType","Packet doesn`t have parameter 'works'");

        return $this->exec(__METHOD__,$works[$type]);
    }





    /**
     * unload lines
     * @param api\soap\models\Line[] $lines
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadline($lines){   
        $type = 'lines';
        $lines = json_decode(json_encode($lines),1); 
        if(!isset($lines[$type]))
            return new ApiExceptionWrongType("WrongType","Packet doesn`t have parameter 'lines'");

        return $this->exec(__METHOD__,$lines[$type]);
    }




    /**
     * unload nomenclatures
     * @param api\soap\models\Nomenclature[] $nomenclatures
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadnomenclature($nomenclatures){   
        $type = 'nomenclatures';
        $nomenclatures = json_decode(json_encode($nomenclatures),1); 
        if(!isset($nomenclatures[$type]))
            return new ApiExceptionWrongType("WrongType","Packet doesn`t have parameter 'nomenclatures'");

        return $this->exec(__METHOD__,$nomenclatures[$type]);
    }



    /**
     * unload stockrooms
     * @param api\soap\models\StockRoom[] $stockrooms
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadstockroom($stockrooms){   
        $type = 'stockrooms';
        $stockrooms = json_decode(json_encode($stockrooms),1); 
        if(!isset($stockrooms[$type]))
            return new ApiExceptionWrongType("WrongType","Packet doesn`t have parameter 'stockrooms'");

        return $this->exec(__METHOD__,$stockrooms[$type]);
    }


    /**
     * unload raports
     * @param api\soap\models\Raport[] $raports
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadraport($raports){   
        $type = 'raports';
        $raports = json_decode(json_encode($raports),1); 
        if(!isset($raports[$type]))
            return new ApiExceptionWrongType("WrongType","Packet doesn`t have parameter 'raports'");

        return $this->exec(__METHOD__,$raports[$type]);
    }


    /**
     * unload RemnantsPackage
     * @param api\soap\models\RemnantsPackage[] $remnants
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadremnant($remnants){   
        $type = 'remnants';
        $remnants = json_decode(json_encode($remnants),1); 
        if(!isset($remnants[$type]))
            return new ApiExceptionWrongType("WrongType","Packet doesn`t have parameter 'remnants'");

        return $this->exec(__METHOD__,$remnants[$type]);
    }


    /**
     * unload Setting
     * @param api\soap\models\Setting $setting
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadsettings($setting){
        $type = 'setting';
        $setting = json_decode(json_encode($setting),1);
        if(!isset($setting[$type]))
            return new ApiExceptionWrongType("WrongType","Packet doesn`t have parameter 'setting'");

        return $this->exec(__METHOD__,$setting[$type]);
    }


    /**
     * unload regulatoryraports
     * @param api\soap\models\RaportRegulatory[] $regulatoryraports
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadraportregulatory($regulatoryraports){   
        $type = 'regulatoryraports';
        $regulatoryraports = json_decode(json_encode($regulatoryraports),1); 
        if(!isset($regulatoryraports[$type]))
            return new ApiExceptionWrongType("WrongType","Packet doesn`t have parameter 'regulatoryraports'");

        return $this->exec(__METHOD__,$regulatoryraports[$type]);
    }



    /**
     * unload projectstandards
     * @param api\soap\models\ProjectStandard[] $projectstandards
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadprojectstandard($projectstandards){   
        $type = 'projectstandards';
        $projectstandards = json_decode(json_encode($projectstandards),1); 
        if(!isset($projectstandards[$type]))
            return new ApiExceptionWrongType("WrongType","Packet doesn`t have parameter 'projectstandards'");

        return $this->exec(__METHOD__,$projectstandards[$type]);
    }



    /**
     * update application status
     * @param api\soap\models\ApplicationStatus $applicationstatus
     * @return api\soap\models\Responce
     * @soap
     */
    public function updateapplicationstatus($applicationstatus){   
        $type = 'applicationstatus';
        $applicationstatus = json_decode(json_encode($applicationstatus),1); 
        if(!isset($applicationstatus[$type]))
            return new ApiExceptionWrongType("WrongType","Packet doesn`t have parameter 'applicationstatus'");

        return $this->exec(__METHOD__,$applicationstatus[$type]);
    }
}
