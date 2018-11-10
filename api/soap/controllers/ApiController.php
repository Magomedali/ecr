<?php
namespace api\soap\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use api\soap\models\Brigade;
use api\soap\models\Responce;
use api\soap\Exceptions\ApiException;
use api\soap\Api;

/**
 * Api controller
 */
class ApiController extends Controller
{   


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
    

    /**
     * @inheritdoc
     */
    public function exec($method,$params){
        
        $methodName = str_replace(__CLASS__."::", "", $method);

        return Api::exec($methodName,$params);
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
     * unload Brigades
     * @param api\soap\models\Brigade[] $brigades
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadbrigade($brigades){   
        return $this->exec(__METHOD__,$brigades);
    }




    /**
     * unload workers
     * @param api\soap\models\Worker[] $workers
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadworker($workers){   
        return $this->exec(__METHOD__,$workers);
    }






    /**
     * unload technics
     * @param api\soap\models\Technic[] $technics
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadtechnics($technics){   
        return $this->exec(__METHOD__,$technics);
    }






    /**
     * unload objects
     * @param api\soap\models\Objects[] $objects
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadobject($objects){   
        return $this->exec(__METHOD__,$objects);
    }





    /**
     * unload boundary
     * @param api\soap\models\Boundary[] $boundaries
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadboundary($boundaries){   
        return $this->exec(__METHOD__,$boundaries);
    }

    




    /**
     * unload projects
     * @param api\soap\models\Project[] $projects
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadproject($projects){   
        return $this->exec(__METHOD__,$projects);
    }




    /**
     * unload typeofworks
     * @param api\soap\models\TypeOfWork[] $works
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadtypeofwork($works){   
        return $this->exec(__METHOD__,$works);
    }





    /**
     * unload lines
     * @param api\soap\models\Line[] $lines
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadline($lines){   
        return $this->exec(__METHOD__,$lines);
    }




    /**
     * unload nomenclatures
     * @param api\soap\models\Nomenclature[] $nomenclatures
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadnomenclature($nomenclatures){   
        return $this->exec(__METHOD__,$nomenclatures);
    }





    /**
     * unload raports
     * @param api\soap\models\Raport[] $raports
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadraport($raports){   
        return $this->exec(__METHOD__,$raports);
    }
}
