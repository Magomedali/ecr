<?php
namespace api\soap\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use api\soap\models\Brigade;
use api\soap\models\Responce;


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
    
    
    public function actionTest(){
        
        $id = "api/index";
        
        $url = Yii::$app->getUrlManager();
        
        $url->enablePrettyUrl = true;
        
        print_r($url->createAbsoluteUrl($id));
        exit;
    }
    
    
    /**
     * Stest method
     * @param string $msg
     * @return string
     * @soap
     */
    public function test($msg)
    {   
        $return = "Leeee ".$msg;
        return $return;
    }


    /**
     * unload Brigades
     * @param api\soap\models\Brigade[] $brigades
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadbrigade($brigades)
    {   
        $responce = new Responce(['success'=>false]);
        return $responce;
    }




    /**
     * unload workers
     * @param api\soap\models\Worker[] $workers
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadworker($workers)
    {   
        $responce = new Responce(['success'=>true]);
        return $responce;
    }






    /**
     * unload technics
     * @param api\soap\models\Technic[] $technics
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadtechnics($technics)
    {   
        $responce = new Responce(['success'=>true]);
        return $responce;
    }






    /**
     * unload objects
     * @param api\soap\models\Objects[] $objects
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadobject($objects)
    {   
        $responce = new Responce(['success'=>true]);
        return $responce;
    }



    /**
     * unload boundary
     * @param api\soap\models\Boundary[] $boundaries
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadboundary($boundaries)
    {   
        $responce = new Responce(['success'=>true]);
        return $responce;
    }

    

    /**
     * unload projects
     * @param api\soap\models\Project[] $projects
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadproject($projects)
    {   
        $responce = new Responce(['success'=>true]);
        return $responce;
    }




    /**
     * unload typeofworks
     * @param api\soap\models\TypeOfWork[] $works
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadtypeofwork($works)
    {   
        $responce = new Responce(['success'=>true]);
        return $responce;
    }





    /**
     * unload lines
     * @param api\soap\models\Line[] $lines
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadline($lines)
    {   
        $responce = new Responce(['success'=>true]);
        return $responce;
    }




    /**
     * unload nomenclatures
     * @param api\soap\models\Nomenclature[] $nomenclatures
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadnomenclature($nomenclatures)
    {   
        $responce = new Responce(['success'=>true]);
        return $responce;
    }



    /**
     * unload raports
     * @param api\soap\models\Raport[] $raports
     * @return api\soap\models\Responce
     * @soap
     */
    public function unloadraport($raports)
    {   
        $responce = new Responce(['success'=>true]);
        return $responce;
    }
}
