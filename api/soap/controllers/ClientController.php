<?php
namespace api\soap\controllers;

use Yii;
use yii\base\Controller;
use yii\filters\VerbFilter;
use api\soap\models\Brigade;
use api\soap\models\Responce;
use api\soap\Exceptions\ApiException;
use api\soap\Api;

use api\soap\test\requests\Test;
/**
 * Client controller
 */
class ClientController extends Controller
{   


    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            
        ];
    }
    

    
    
    public function actionIndex(){
        echo "salam";
    }

    
    public function actionTest($msg = null){
        echo "<br>---Method Test<br>";

        $msg = "console tester";
        
        $answer = Yii::$app->testclient->getClient()->test("test message"); 

        print_r($answer);
    }


  
    public function actionUnloadbrigade($brigades = null){

        $model = new \common\models\Brigade();

        $par = ['guid'=>'1asdasjdhuu32423jkasdfa','name'=>'Бригада100'];

        if($model->load(['Brigade'=>$par]) && $model->save(1)){

        }
        

    }




    
    public function actionUnloadworker($workers = null){  
    }






    
    public function actionUnloadtechnics($technics = null){ 
    }






    public function actionUnloadobject($objects = null){ 
    }





    
    public function actionUnloadboundary($boundaries = null){  
    }
    
    public function actionUnloadproject($projects = null){ 
    }
    
    public function actionUnloadtypeofwork($works = null){  
    }

    
    public function actionUnloadline($lines = null){

    }


    public function actionUnloadnomenclature($nomenclatures = null){

    }

    public function actionUnloadraport($raports = null){

    }
}
