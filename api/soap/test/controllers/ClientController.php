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
        echo "\n---Start testing";
        $this->actionTest();
        $this->actionUnloadbrigade();
        $this->actionUnloadtechnic();
        $this->actionUnloadline();
        $this->actionUnloadnomenclature();
        $this->actionUnloadtypeofwork();
        $this->actionUnloadboundary();
        $this->actionUnloadobject();
        $this->actionUnloadproject();
        $this->actionUnloadworker();
        $this->actionUnloadremnant();
        $this->actionUnloadraport();

        echo "\n---Finish testing";
    }

    


    public function actionTest(){
        echo "\n---Method Test : ";


        $answer = Yii::$app->testclient->getClient()->test("test message"); 

        $result = isset($answer->success) && $answer->success ? "true" : "false";
        echo $result;
    }


  
    

    


  
    public function actionUnloadbrigade(){
        echo "\n---Method Unloadbrigade : ";

        $model = new \common\models\Brigade();

        $par = [
            ['guid'=>'1asdasjdhuu32423jkasdfa','name'=>'Бригада100'],
            ['guid'=>'2asdasjdhuu32423jkasdfa','name'=>'Бригадbh2']
        ];

        $answer = Yii::$app->testclient->getClient()->unloadbrigade($par); 


        $result = isset($answer->success) && $answer->success ? "true" : "false";
        echo $result;
    }




    
    public function actionUnloadworker(){
        echo "\n---Method Unloadworker : ";

        $par = [
            'guid'=>'10sdasjdhuu32423jkasdfa',
            'brigade_guid'=>'1asdasjdhuu32423jkasdfa',
            'technic_guid'=>'1asdasjdhuu32413jkasdfa',
            'name'=>'Бригада1',
            'ktu'=>1.2,
            'is_master'=>true
        ];

        $answer = Yii::$app->testclient->getClient()->unloadworker([$par]); 


        $result = isset($answer->success) && $answer->success ? "true" : "false";
        echo $result;
    }






    
    public function actionUnloadtechnic(){
        echo "\n---Method Unloadtechnic : ";
        $par = [
            [
                'guid'=>'1asdasjdhuu32413jkasdfa',
                'name'=>'Газель07',
                'marka'=>"Газель",
                'number'=>"soccer"
            ],
            [

                'guid'=>'2asdasjdhuu32413jkasdfa',
                'name'=>'Газель09',
                'marka'=>"Газель",
                'number'=>"soccer"
            ]
        ];


        $answer = Yii::$app->testclient->getClient()->unloadtechnic($par); 


        $result = isset($answer->success) && $answer->success ? "true" : "false";
        echo $result;
    }






    public function actionUnloadobject(){
        echo "\n---Method Unloadobject : ";
        $par = [
            [
                'guid'=>'1asdasjdhuu32413jkasdfa',
                'name'=>'Газель07',
                'boundary_guid'=>"2asdasjdhuu32413jkasdfa"
            ],
            [

                'guid'=>'2asdasjdhuu32413jkasdfa',
                'name'=>'Газель09',
                'boundary_guid'=>"1asdasjdhuu32413jkasdfa"
            ]
        ];


        $answer = Yii::$app->testclient->getClient()->unloadobject($par); 


        $result = isset($answer->success) && $answer->success ? "true" : "false";
        echo $result;
    }





    
    public function actionUnloadboundary(){
        echo "\n---Method Unloadboundary : ";

        $par = [
            [
                'guid'=>'1asdasjdhuu32413jkasdfa',
                'name'=>'Граница 1'
            ],
            [

                'guid'=>'2asdasjdhuu32413jkasdfa',
                'name'=>'Граница 2',
            ]
        ];


        $answer = Yii::$app->testclient->getClient()->unloadboundary($par); 


        $result = isset($answer->success) && $answer->success ? "true" : "false";
        echo $result;
    }



    
    public function actionUnloadproject(){
        echo "\n---Method Unloadproject : ";

        $par = [
            [
                'guid'=>'1asdasjdhuu32413jkasdfa',
                'name'=>'Проект 1',
                'objects_guids'=>[
                    '1asdasjdhuu32413jkasdfa',
                ]
            ],
            [
                'guid'=>'2asdasjdhuu32413jkasdfa',
                'name'=>'Проект 2',
                'objects_guids'=>[
                ]
            ]
        ];


        $answer = Yii::$app->testclient->getClient()->unloadproject($par); 


        $result = isset($answer->success) && $answer->success ? "true" : "false";
        echo $result;
    }
    



    public function actionUnloadtypeofwork(){
        echo "\n---Method Unloadtypeofwork : ";

        $par = [
            [
                'guid'=>'1asdasjdhuu32413jkasdfa',
                'name'=>'Работа2',
            ],
            [

                'guid'=>'2asdasjdhuu32413jkasdfa',
                'name'=>'Работа1',
            ]
        ];

        $answer = Yii::$app->testclient->getClient()->unloadtypeofwork($par); 


        $result = isset($answer->success) && $answer->success ? "true" : "false";
        echo $result;
    }

    



    public function actionUnloadline(){
        echo "\n---Method Unloadline : ";

        $par = [
            [
                'guid'=>'1asdasjdhuu32413jkasdfa',
                'name'=>'Линия 1.1.1',
            ],
            [

                'guid'=>'2asdasjdhuu32413jkasdfa',
                'name'=>'Линия 1.1.2',
            ]
        ];

        $answer = Yii::$app->testclient->getClient()->unloadline($par); 

        $result = isset($answer->success) && $answer->success ? "true" : "false";
        echo $result;
    }






    public function actionUnloadnomenclature(){

        echo "\n---Method Unloadnomenclature : ";

        $par = [
            [
                'guid'=>'1asdasjdhuu32413jkasdfa',
                'name'=>'абота2',
            ],
            [

                'guid'=>'2asdasjdhuu32413jkasdfa',
                'name'=>'Работа1',
            ]
        ];

        $answer = Yii::$app->testclient->getClient()->unloadnomenclature($par); 

        $result = isset($answer->success) && $answer->success ? "true" : "false";
        echo $result;
    }



    public function actionUnloadremnant(){
        
        echo "\n---Method Unloadremnant : ";

        $par = [
            [
                'brigade_guid'=>'1asdasjdhuu32423jkasdfa',
                'nomenclature_guid'=>'1asdasjdhuu32413jkasdfa',
                'count'=>10
            ],
            [
                'brigade_guid'=>'2asdasjdhuu32423jkasdfa',
                'nomenclature_guid'=>'1asdasjdhuu32413jkasdfa',
                'count'=>12
            ]
        ];

        

        $answer = Yii::$app->testclient->getClient()->unloadremnant($par); 

        $result = isset($answer->success) && $answer->success ? "true" : "false";
        echo $result;
    }



    public function actionUnloadraport(){

        echo "\n---Method Unloadraport : ";

        $par = [
            [
                'guid'=>'2asdasjdhuu32423jkasdfa',
                'number'=>'1asdasjdhuu32413jkasdfa',
                'status'=>"ывфывлжд",
                'created_at'=>date("Y-m-d\TH:i:s"),
                'starttime'=>date("H:i:s",time()-3600),
                'endtime'=>date("H:i:s"),
                'temperature_start'=>10.1,
                'temperature_end'=>10.3,
                'surface_temperature_start'=>10.2,
                'surface_temperature_end'=>10.5,
                'airhumidity_start'=>10.1,
                'airhumidity_end'=>2.2,
                'brigade_guid'=>"1asdasjdhuu32423jkasdfa",
                'object_guid'=>"1asdasjdhuu32413jkasdfa",
                'boundary_guid'=>"1asdasjdhuu32413jkasdfa",
                'project_guid'=>"1asdasjdhuu32413jkasdfa",
                'master_guid'=>"10sdasjdhuu32423jkasdfa",
                'comment'=>"Тест",
                'materials'=>[],
                'works'=>[],
                'consist'=>[],
            ]
        ];

        $answer = Yii::$app->testclient->getClient()->unloadraport($par); 

        $result = isset($answer->success) && $answer->success ? "true" : "false";
        echo $result;
    }
}
