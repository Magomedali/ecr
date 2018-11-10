<?php
namespace api\soap\test\controllers;

use Yii;
use api\soap\Api;
use yii\console\Controller;
use yii\filters\VerbFilter;
use api\soap\models\Brigade;

use api\soap\models\Responce;
use api\soap\Exceptions\ApiException;


/**
 * Api controller
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
        echo "\n---Method Test\n";


        $answer = Yii::$app->testclient->getClient()->test("test message"); 

        print_r($answer);

        echo "\n---------------\n";
    }


  
    public function actionUnloadbrigade($brigades = null){ 
        echo "\n---Method Test\n";


        $brigades = [
            new Brigade(['guid'=>'1asdasjdhuu32423jkasdfa','name'=>'Бригада 1']),
            new Brigade(['guid'=>'2asdasjdhuu32423jkasdfa','name'=>'Бригада 2']),
            new Brigade(['guid'=>'3asdasjdhuu32423jkasdfa','name'=>'Бригада 4']),
            new Brigade(['guid'=>'4asdasjdhuu32423jkasdfa','name'=>'Бригада 3']),
            new Brigade(['guid'=>'6asdasjdhuu32423jkasdfa','name'=>'Бригада 5'])
        ];


        $answer = Yii::$app->testclient->getClient()->unloadbrigade($brigades); 

        print_r($answer);

        echo "\n---------------\n";
    }




    
    public function actionUnloadworker($workers = null){
        echo "\n---Method Test\n";


        $answer = Yii::$app->testclient->getClient()->test("test message"); 

        print_r($answer);

        echo "\n---------------\n";
    }






    
    public function actionUnloadtechnics($technics = null){
        echo "\n---Method Test\n";


        $answer = Yii::$app->testclient->getClient()->test("test message"); 

        print_r($answer);

        echo "\n---------------\n";
    }






    public function actionUnloadobject($objects = null){
        echo "\n---Method Test\n";


        $answer = Yii::$app->testclient->getClient()->test("test message"); 

        print_r($answer);

        echo "\n---------------\n";
    }





    
    public function actionUnloadboundary($boundaries = null){
        echo "\n---Method Test\n";


        $answer = Yii::$app->testclient->getClient()->test("test message"); 

        print_r($answer);

        echo "\n---------------\n";
    }
    
    public function actionUnloadproject($projects = null){
        echo "\n---Method Test\n";


        $answer = Yii::$app->testclient->getClient()->test("test message"); 

        print_r($answer);

        echo "\n---------------\n";
    }
    
    public function actionUnloadtypeofwork($works = null){
        echo "\n---Method Test\n";


        $answer = Yii::$app->testclient->getClient()->test("test message"); 

        print_r($answer);

        echo "\n---------------\n";
    }

    
    public function actionUnloadline($lines = null){
        echo "\n---Method Test\n";


        $answer = Yii::$app->testclient->getClient()->test("test message"); 

        print_r($answer);

        echo "\n---------------\n";

    }


    public function actionUnloadnomenclature($nomenclatures = null){
        echo "\n---Method Test\n";


        $answer = Yii::$app->testclient->getClient()->test("test message"); 

        print_r($answer);

        echo "\n---------------\n";

    }

    public function actionUnloadraport($raports = null){
        echo "\n---Method Test\n";


        $answer = Yii::$app->testclient->getClient()->test("test message"); 

        print_r($answer);

        echo "\n---------------\n";

    }
}
