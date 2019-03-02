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

        // $m = \common\models\User::findOne(['guid'=>'e9d9bbf7-5271-11e7-80ec-000c29a0432f','is_master'=>1]);
        // print_r($m->attributes);
        // exit;

        echo "\n---Start testing\n";
        $this->actionTest();
        $this->actionUnloadbrigade();
        $this->actionUnloadtechnic();
        $this->actionUnloadworker();
        $this->actionUnloadline();
        $this->actionUnloadnomenclature();
        $this->actionUnloadstockroom();
        $this->actionUnloadtypeofwork();
        $this->actionUnloadboundary();
        $this->actionUnloadobject();
        $this->actionUnloadproject();
        $this->actionUnloadremnant();
        $this->actionUnloadraport();
        $this->actionUnloadraportregulatory();
        $this->actionUnloadsettings();
        $this->actionUnloadprojectstandard();

        echo "\n---Finish testing\n\n";
    }

    


    public function actionTest(){
        $answer = Yii::$app->testclient->getClient()->test("test message"); 

        $result =isset($answer->returns) && isset($answer->returns->success) && $answer->returns->success ? "true" : "false";
        echo $result;
        echo " : ---Method Test\n";
    }


  
    

    


  
    public function actionUnloadbrigade(){
        

        $model = new \common\models\Brigade();

        $par = [
            ['guid'=>'1asdasjdhuu32423jkasdfa','name'=>'Бригада100'],
            ['guid'=>'c6fb11c4-3476-418b-85cc-299e34ad58c4','name'=>'BORUM 06 Ноздренков А.И.'],
            ['guid'=>'57fec609-3104-11e9-8154-005056b47a2e','name'=>'15 fevr']
        ];



        $par['brigades'] = $par;
        $answer = Yii::$app->testclient->getClient()->unloadbrigade($par); 


        $result =isset($answer->returns) && isset($answer->returns->success) && $answer->returns->success ? "true" : "false";
        echo $result;
        echo " : ---Method Unloadbrigade\n";
    }




    
    public function actionUnloadworker(){
        
        $par = [
            [
                'guid'=>'209c80df-68f2-11e8-8117-005056b47a2e',
                'name'=>'Абрамов Михаил Сергеевич',
                'is_master'=>true,
                'login'=>'master1',
                'password'=>'12345qwE',
            ],
            [
                'guid'=>'e9d9bbf7-5271-11e7-80ec-000c29a0432f',
                'name'=>'Акопян Вираб Робертович',
                'is_master'=>true,
                'login'=>'master2',
                'password'=>'12345qwE',
            ],
            [
                'guid'=>'07b7112a-40af-11e8-8114-005056b47a2e',
                'brigade_guid'=>'c6fb11c4-3476-418b-85cc-299e34ad58c4',
                'name'=>'Мишуров Андрей Николаевич',
                'login'=>'test',
                'password'=>'12345qwE',
                'ktu'=>0.85,
                'is_master'=>false,
                'status'=>1
            ],
            [
                'guid'=>'4eed6dd1-28b6-11e7-80e9-000c29a0432f',
                'brigade_guid'=>'c6fb11c4-3476-418b-85cc-299e34ad58c4',
                'technic_guid'=>'ac22777f-cd5c-11e8-8122-005056b47a2e',
                'name'=>'Джалолов Сухроб Рустамович',
                'ktu'=>0.9,
                'is_master'=>false,
                'status'=>0
            ],
            [
                'guid'=>'be2e6d76-2128-11e7-80e5-000c29a0432f',
                'brigade_guid'=>'c6fb11c4-3476-418b-85cc-299e34ad58c4',
                'technic_guid'=>'ac22777f-cd5c-11e8-8122-005056b47a2e',
                'name'=>'Ноздренков Александр Иванович',
                'ktu'=>1,
                'is_master'=>false,
                'login'=>'nozdrenkovai',
                'password'=>'12345qwE',
                'status'=>1
            ],
            [
                'guid'=>'01a373e5-40af-11e8-8114-005056b47a2e',
                'name'=>'Бондарчук Геннадий Владимирович',
                'brigade_guid'=>'57fec609-3104-11e9-8154-005056b47a2e',
                'technic_guid'=>'c5947304-d200-11e8-8124-005056b47a2e',
                'is_master'=>false,
                'ktu'=>0,
                'login'=>'bondarchukgv',
                'password'=>'12345qwE'
            ]
        ];



        $par['workers'] = $par;
        $answer = Yii::$app->testclient->getClient()->unloadworker($par); 


        $result =isset($answer->returns) && isset($answer->returns->success) && $answer->returns->success ? "true" : "false";
        echo $result;
        echo " : ---Method Unloadworker\n";
        print_r($answer);
    }






    
    public function actionUnloadtechnic(){
        
        $par = [
            [
                'guid'=>'baf1abf2-cd5c-11e8-8122-005056b47a2e',
                'name'=>'о004хх77 audi',
                'marka'=>"audi",
                'number'=>"О004ХХ77"
            ],
            [

                'guid'=>'ac22777f-cd5c-11e8-8122-005056b47a2e',
                'name'=>'с999ст lada',
                'marka'=>"lada",
                'number'=>"С999СТ"
            ],
            [

                'guid'=>'4300123d-c189-11e8-811d-005056b47a2e',
                'name'=>'Р663СВ Газель',
                'marka'=>"Газель",
                'number'=>"Р663СВ"
            ],
            [
                'guid'=>'c5947304-d200-11e8-8124-005056b47a2e',
                'name'=>'А 321 ТВ 197 ГАЗ 2705',
                'marka'=>'ГАЗ 2705',
                'number'=>'А321ТВ197'
            ]
        ];


        $par['technics'] = $par;

        $answer = Yii::$app->testclient->getClient()->unloadtechnic($par); 


        $result =isset($answer->returns) && isset($answer->returns->success) && $answer->returns->success ? "true" : "false";
        echo $result;
        echo " : ---Method Unloadtechnic\n";
    }






    public function actionUnloadobject(){
        
        $par = [
            [
                'guid'=>'49a16abb-3f28-11e8-8114-005056b47a2e',
                'name'=>'Капитальный ремонт а/д «Трасса «Кавказ» - Нестеров...',
                'boundary_guid'=>"be8cea0a-3352-11e8-8113-005056b47a2e",
                'master_guid'=>'e9d9bbf7-5271-11e7-80ec-000c29a0432f'
            ],
            [

                'guid'=>'9d5ad0dc-3f28-11e8-8114-005056b47a2e',
                'name'=>'Реконструкция автомобильной дороги «Назрань-Грозный» км 11+000 – км 22+000',
                'boundary_guid'=>"be8cea0a-3352-11e8-8113-005056b47a2e"
            ],
            [

                'guid'=>'ce376d9c-3f28-11e8-8114-005056b47a2e',
                'name'=>'Реконструкция автомобильной дороги «Назрань – Малгобек – Нижний Курп - Терек» км 9+000 – км 22+000',
                'boundary_guid'=>"be8cea0a-3352-11e8-8113-005056b47a2e",
                'master_guid'=>'e9d9bbf7-5271-11e7-80ec-000c29a0432f'
            ],
            [

                'guid'=>'ef1449ab-3f28-11e8-8114-005056b47a2e',
                'name'=>'Реконструкция автомобильной дороги «Назрань – Малгобек – Нижний Курп - Терек» км 32+000 – км 42+000',
                'boundary_guid'=>"be8cea0a-3352-11e8-8113-005056b47a2e",
                'master_guid'=>'e9d9bbf7-5271-11e7-80ec-000c29a0432f'
            ],
            [

                'guid'=>'1939b9ef-a6c1-11e8-811a-005056b47a2e',
                'name'=>'МКАД Внутренняя сторона км 26- км 27',
                'boundary_guid'=>"eb59ee3e-a50c-11e8-811a-005056b47a2e"
            ],
            [

                'guid'=>'6dca691c-a6cd-11e8-811a-005056b47a2e',
                'name'=>'Калужское шоссе',
                'boundary_guid'=>"024d3173-16c5-11e8-8112-005056b47a2e",
                'master_guid'=>'209c80df-68f2-11e8-8117-005056b47a2e'
            ]
        ];


        $par['objects'] = $par;
        $answer = Yii::$app->testclient->getClient()->unloadobject($par); 


        $result =isset($answer->returns) && isset($answer->returns->success) && $answer->returns->success ? "true" : "false";
        echo $result;
        echo " : ---Method Unloadobject\n";
       // echo "\n\n\n", json_encode($answer),"\n\n\n";
    }





    
    public function actionUnloadboundary(){
        

        $par = [
            [
                'guid'=>'be8cea0a-3352-11e8-8113-005056b47a2e',
                'name'=>'Ингушетия'
            ],
            [

                'guid'=>'eb59ee3e-a50c-11e8-811a-005056b47a2e',
                'name'=>'МКАД',
            ],
            [

                'guid'=>'96214a45-324a-11e8-8113-005056b47a2e',
                'name'=>'Базы',
            ],
            [

                'guid'=>'8a925f04-16c4-11e8-8112-005056b47a2e',
                'name'=>'ВАО',
            ],
            [

                'guid'=>'914db38b-16c4-11e8-8112-005056b47a2e',
                'name'=>'ГМС',
            ],
            [

                'guid'=>'ba2b9a17-16c4-11e8-8112-005056b47a2e',
                'name'=>'ЗАО',
            ],
            [

                'guid'=>'c0c03d78-16c4-11e8-8112-005056b47a2e',
                'name'=>'ЗелАО',
            ],
            [

                'guid'=>'024d3173-16c5-11e8-8112-005056b47a2e',
                'name'=>'ЮЗАО',
            ],
        ];


        $par['boundaries'] = $par;
        $answer = Yii::$app->testclient->getClient()->unloadboundary($par); 


        $result =isset($answer->returns) && isset($answer->returns->success) && $answer->returns->success ? "true" : "false";
        echo $result;
        echo " : ---Method Unloadboundary\n";
    }



    
    public function actionUnloadproject(){
        

        $par = [
            [
                'guid'=>'231ca414-3f28-11e8-8114-005056b47a2e',
                'name'=>'1-КИ от 06.04.18',
                'objects_guids'=>[
                    '49a16abb-3f28-11e8-8114-005056b47a2e',
                    '9d5ad0dc-3f28-11e8-8114-005056b47a2e',
                    'ce376d9c-3f28-11e8-8114-005056b47a2e',
                    'ef1449ab-3f28-11e8-8114-005056b47a2e',
                ]
            ],
            [
                'guid'=>'2d835e00-a6cd-11e8-811a-005056b47a2e',
                'name'=>'Помощь МАДИ-Практик',
                'objects_guids'=>[
                    '6dca691c-a6cd-11e8-811a-005056b47a2e'
                ]
            ]
        ];

        $par['projects'] = $par;
        $answer = Yii::$app->testclient->getClient()->unloadproject($par); 


        $result =isset($answer->returns) && isset($answer->returns->success) && $answer->returns->success ? "true" : "false";
        echo $result;
        echo " : ---Method Unloadproject\n";
        //echo "\n",$answer->returns->errorMessage;
    }
    



    public function actionUnloadtypeofwork(){

        $par = [
            [
                'guid'=>'1cd95cf7-201f-11e8-8112-005056b47a2e',
                'name'=>'Закраска разметки',
                'is_regulatory'=>1,
                'nomenclatures_guid'=>[
                    'c0dbfe08-d8d0-11e6-80f1-005056b47a2e',
                    'c2643f0a-0278-11e7-80f9-005056b47a2e'
                ]
            ],
            [
                'guid'=>'2483a64e-201f-11e8-8112-005056b47a2e',
                'name'=>'Зачистка разметки',
                'is_regulatory'=>1,
                'nomenclatures_guid'=>[
                    'c0dbfe08-d8d0-11e6-80f1-005056b47a2e'
                ]
            ],
            [
                'guid'=>'2c8fe8b0-201f-11e8-8112-005056b47a2e',
                'name'=>'Разметка готовыми формами',
                'is_regulatory'=>0,
                'nomenclatures_guid'=>[
                    'c2643f0a-0278-11e7-80f9-005056b47a2e',
                    'c0dbfe08-d8d0-11e6-80f1-005056b47a2e',
                ]
            ],
            [
                'guid'=>'34e264ea-201f-11e8-8112-005056b47a2e',
                'name'=>'Разметка краской',
            ],
            [
                'guid'=>'3c327040-201f-11e8-8112-005056b47a2e',
                'name'=>'Разметка термопластиком',
            ],
            [
                'guid'=>'4327f0f2-201f-11e8-8112-005056b47a2e',
                'name'=>'Разметка холодным пластиком',
                'is_regulatory'=>0,
            ],
            [
                'guid'=>'126d493f-201f-11e8-8112-005056b47a2e',
                'name'=>'Демаркировка разметки',
            ]
        ];


        $par['works'] = $par;
        $answer = Yii::$app->testclient->getClient()->unloadtypeofwork($par); 


        $result =isset($answer->returns) && isset($answer->returns->success) && $answer->returns->success ? "true" : "false";
        echo $result;


        echo " : ---Method Unloadtypeofwork\n";
    }

    



    public function actionUnloadline(){
        

        $par = [
            [
                'guid'=>'5590841e-64c6-4f45-aa81-0f3da62e0ff6',
                'name'=>'1.1 Контуры островков безопасности. Сплошная, ширина 10 см, цвет Белый',
                'is_countable'=>0,
                'hint_count'=>'Подсказка кол',
                'hint_length'=>'Подсказка ленг'
            ],
            [

                'guid'=>'2asdasjdhuu32413jkasdfa',
                'name'=>'Линия 1.1.2',
                'is_countable'=>true,
                'hint_length'=>'Подсказка коунт',
                'hint_count'=>'Подсказка длина2'
            ]
        ];


        $par['lines'] = $par;
        $answer = Yii::$app->testclient->getClient()->unloadline($par); 

        $result =isset($answer->returns) && isset($answer->returns->success) && $answer->returns->success ? "true" : "false";
        echo $result;
        echo " : ---Method Unloadline\n";
    }






    public function actionUnloadnomenclature(){

        $par = [
            [
                'guid'=>'c0dbfe08-d8d0-11e6-80f1-005056b47a2e',
                'name'=>'Термоклапан ЗМЗ - 405',
                'unit'=>'кг'
            ],
            [

                'guid'=>'c2643f0a-0278-11e7-80f9-005056b47a2e',
                'name'=>'Термопистолет Makita',
                'unit'=>'кг'
            ],
            [

                'guid'=>'f99973a7-8706-11e7-8106-005056b47a2e',
                'name'=>'Теплообменник д. 1138',
                'unit'=>'кг'
            ],
            [

                'guid'=>'9bf987a6-b234-11e6-80e8-005056b47a2e',
                'name'=>'Теплоизоляция 400',
            ],
            
            [
                'guid'=>'0b8b5fbc-30de-11e8-8113-005056b47a2e',
                'name'=>'Эмаль, цвет жёлтый (АК-511) ',
                'unit'=>'кг'
            ],
            [
                'guid'=>'4d4cb38a-a096-11e8-811a-005056b47a2e',
                'name'=>'МСШ 500-900 (Сферастек Закупной) ',
                'unit'=>'кг'
            ],
            [
                'guid'=>'08070b75-9baa-11e8-811a-005056b47a2e',
                'name'=>'Термопластик белый (Пластидор-Т Закупной)',
                'unit'=>'кг'
            ]
        ];


        $par['nomenclatures'] = $par;
        $answer = Yii::$app->testclient->getClient()->unloadnomenclature($par); 

        $result =isset($answer->returns) && isset($answer->returns->success) && $answer->returns->success ? "true" : "false";
        echo $result;
        echo " : ---Method Unloadnomenclature\n";
    }





    public function actionUnloadstockroom(){

        
        $par = [
            [
                'guid'=>'97dcbcf1-1632-11e8-8112-005056b47a2e',
                'name'=>'Склад №1 Центральный  Радиальная',
            ],
            [

                'guid'=>'60bd8468-1d55-11e9-814a-005056b47a2e',
                'name'=>'Склад №2 Разметка Сигнальный (без ордера)',
            ],
            [

                'guid'=>'c61993f3-090b-11e9-8141-005056b47a2e',
                'name'=>'Склад транзитный',
            ],
            [

                'guid'=>'505bae57-1d5d-11e9-814a-005056b47a2e',
                'name'=>'Склад №2 Разметка Сигнальный БЕЗ ОРДЕРА',
            ]
        ];


        $par['stockrooms'] = $par;
        $answer = Yii::$app->testclient->getClient()->unloadstockroom($par); 

        // print_r(Yii::$app->testclient->getClient()->__getFunctions());

        $result =isset($answer->returns) && isset($answer->returns->success) && $answer->returns->success ? "true" : "false";
        echo $result;
        echo " : ---Method Unloadstockroom\n";
    }




    public function actionUnloadremnant(){
        

        $par = [
            [
                'user_guid'=>'be2e6d76-2128-11e7-80e5-000c29a0432f',
                'items'=>[
                        'nomenclature_guid'=>'c0dbfe08-d8d0-11e6-80f1-005056b47a2e',
                        'count'=>12
                ]
            ]
        ];

        $par['remnants'] = $par;
        $answer = Yii::$app->testclient->getClient()->unloadremnant($par); 

        $result =isset($answer->returns) && isset($answer->returns->success) && $answer->returns->success ? "true" : "false";
        echo $result;
        echo " : ---Method Unloadremnant\n";
        //echo "\n\n\n", json_encode($answer),"\n\n\n";
    }



    public function actionUnloadraport(){

        

        $par = [
            [
                'guid'=>'2asdasjdhuu32423jkasdfa',
                'number'=>'10000029',
                'status'=>"На подтверждении",
                'created_at'=>date("Y-m-d\TH:i:s"),
                'starttime'=>date("H:i:s",time()-3600),
                'endtime'=>date("H:i:s"),
                'temperature_start'=>10.1,
                'temperature_end'=>10.3,
                'surface_temperature_start'=>10.2,
                'surface_temperature_end'=>10.5,
                'airhumidity_start'=>10.1,
                'airhumidity_end'=>2.2,
                'brigade_guid'=>"c6fb11c4-3476-418b-85cc-299e34ad58c4",
                'object_guid'=>"49a16abb-3f28-11e8-8114-005056b47a2e",
                'boundary_guid'=>"be8cea0a-3352-11e8-8113-005056b47a2e",
                'project_guid'=>"231ca414-3f28-11e8-8114-005056b47a2e",
                'master_guid'=>"e9d9bbf7-5271-11e7-80ec-000c29a0432f",
                'user_guid'=>"be2e6d76-2128-11e7-80e5-000c29a0432f",
                'comment'=>"Тест",
                'materials'=>[
                    [
                        'nomenclature_guid'=>'f99973a7-8706-11e7-8106-005056b47a2e',
                        'was'=>11,
                        'spent'=>2,
                        'rest'=>8
                    ],
                    [
                        'nomenclature_guid'=>'c2643f0a-0278-11e7-80f9-005056b47a2e',
                        'was'=>14,
                        'spent'=>2,
                        'rest'=>12
                    ],
                    [
                        'nomenclature_guid'=>'c0dbfe08-d8d0-11e6-80f1-005056b47a2e',
                        'was'=>13,
                        'spent'=>2,
                        'rest'=>11
                    ]
                ],
                'works'=>[
                    [
                        'work_guid'=>'1cd95cf7-201f-11e8-8112-005056b47a2e',
                        'line_guid'=>'5590841e-64c6-4f45-aa81-0f3da62e0ff6',
                        'mechanized'=>true,
                        'length'=>10,
                        'count'=>2.3,
                        'squaremeter'=>222.2
                    ],
                    [
                        'work_guid'=>'1cd95cf7-201f-11e8-8112-005056b47a2e',
                        'line_guid'=>'5590841e-64c6-4f45-aa81-0f3da62e0ff6',
                        'mechanized'=>false,
                        'length'=>20,
                        'count'=>2.3,
                        'squaremeter'=>222.2
                    ]
                ],
                'consist'=>[
                    [
                        'technic_guid'=>'ac22777f-cd5c-11e8-8122-005056b47a2e',
                        'user_guid'=>'be2e6d76-2128-11e7-80e5-000c29a0432f',
                    ],
                    [
                        'technic_guid'=>'baf1abf2-cd5c-11e8-8122-005056b47a2e',
                        'user_guid'=>'07b7112a-40af-11e8-8114-005056b47a2e',
                    ]
                ],
            ],
            [
                'guid'=>'1asdasjdhuu32423jkasdfa',
                'number'=>'10000029',
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
                'brigade_guid'=>"c6fb11c4-3476-418b-85cc-299e34ad58c4",
                'object_guid'=>"1939b9ef-a6c1-11e8-811a-005056b47a2e",
                'boundary_guid'=>"eb59ee3e-a50c-11e8-811a-005056b47a2e",
                'project_guid'=>"2d835e00-a6cd-11e8-811a-005056b47a2e",
                'master_guid'=>"e9d9bbf7-5271-11e7-80ec-000c29a0432f",
                'user_guid'=>"be2e6d76-2128-11e7-80e5-000c29a0432f",
                'comment'=>"Тест",
                'materials'=>[
                    [
                        'nomenclature_guid'=>'f99973a7-8706-11e7-8106-005056b47a2e',
                        'was'=>11,
                        'spent'=>2,
                        'rest'=>8
                    ],
                    [
                        'nomenclature_guid'=>'c2643f0a-0278-11e7-80f9-005056b47a2e',
                        'was'=>14,
                        'spent'=>2,
                        'rest'=>12
                    ],
                    [
                        'nomenclature_guid'=>'c0dbfe08-d8d0-11e6-80f1-005056b47a2e',
                        'was'=>13,
                        'spent'=>2,
                        'rest'=>11
                    ]
                ],
                'works'=>[
                    [
                        'work_guid'=>'1cd95cf7-201f-11e8-8112-005056b47a2e',
                        'line_guid'=>'5590841e-64c6-4f45-aa81-0f3da62e0ff6',
                        'mechanized'=>true,
                        'length'=>10,
                        'count'=>2.3,
                        'squaremeter'=>222.2
                    ],
                    [
                        'work_guid'=>'1cd95cf7-201f-11e8-8112-005056b47a2e',
                        'line_guid'=>'5590841e-64c6-4f45-aa81-0f3da62e0ff6',
                        'mechanized'=>false,
                        'length'=>30,
                        'count'=>2.3,
                        'squaremeter'=>222.2
                    ]
                ],
                'consist'=>[
                    [
                        'technic_guid'=>'4300123d-c189-11e8-811d-005056b47a2e',
                        'user_guid'=>'4eed6dd1-28b6-11e7-80e9-000c29a0432f',
                    ],
                    [
                        'technic_guid'=>'baf1abf2-cd5c-11e8-8122-005056b47a2e',
                        'user_guid'=>'4eed6dd1-28b6-11e7-80e9-000c29a0432f',
                    ]
                ],
            ]
        ];

        $par['raports'] = $par;
        $answer = Yii::$app->testclient->getClient()->unloadraport($par); 

        $result =isset($answer->returns) && isset($answer->returns->success) && $answer->returns->success ? "true" : "false";
        echo $result;
        echo " : ---Method Unloadraport\n";
        //echo "\n\n\n", json_encode($answer),"\n\n\n";
    }



    public function actionUnloadraportregulatory(){
        

        $par = [
            [
                'guid'=>'2asdasjdhuu32423jkasdfa',
                'number'=>'10000029',
                'status'=>"На подтверждении",
                'created_at'=>date("Y-m-d\TH:i:s"),
                'starttime'=>date("H:i:s",time()-3600),
                'endtime'=>date("H:i:s"),
                
                'brigade_guid'=>"c6fb11c4-3476-418b-85cc-299e34ad58c4",
                'master_guid'=>"e9d9bbf7-5271-11e7-80ec-000c29a0432f",
                'user_guid'=>"07b7112a-40af-11e8-8114-005056b47a2e",
                'comment'=>"Тест",
                
                'works'=>[
                    [
                        'work_guid'=>'1cd95cf7-201f-11e8-8112-005056b47a2e',
                        'user_guid'=>'be2e6d76-2128-11e7-80e5-000c29a0432f',
                        'hours'=>2.3
                    ],
                    [
                        'work_guid'=>'1cd95cf7-201f-11e8-8112-005056b47a2e',
                        'user_guid'=>'07b7112a-40af-11e8-8114-005056b47a2e',
                        'hours'=>2.3
                    ]
                ]
            ],
            [
                'guid'=>'1asdasjdhuu32423jkasdfa',
                'number'=>'10000029',
                'status'=>"ывфывлжд",
                'created_at'=>date("Y-m-d\TH:i:s"),
                'starttime'=>date("H:i:s",time()-3600),
                'endtime'=>date("H:i:s"),
                'brigade_guid'=>"c6fb11c4-3476-418b-85cc-299e34ad58c4",
                'master_guid'=>"e9d9bbf7-5271-11e7-80ec-000c29a0432f",
                'user_guid'=>"07b7112a-40af-11e8-8114-005056b47a2e",
                'comment'=>"Тест",
                'works'=>[
                    [
                        'work_guid'=>'1cd95cf7-201f-11e8-8112-005056b47a2e',
                        'user_guid'=>'4eed6dd1-28b6-11e7-80e9-000c29a0432f',
                        'hours'=>2.3
                    ],
                    [
                        'work_guid'=>'1cd95cf7-201f-11e8-8112-005056b47a2e',
                        'user_guid'=>'4eed6dd1-28b6-11e7-80e9-000c29a0432f',
                        'hours'=>2.3
                    ]
                ],
            ]
        ];

        $par['regulatoryraports'] = $par;
        $answer = Yii::$app->testclient->getClient()->unloadraportregulatory($par); 

        $result =isset($answer->returns) && isset($answer->returns->success) && $answer->returns->success ? "true" : "false";
        echo $result;
        echo " : ---Method unloadraportregulatory\n";
        //echo "\n\n\n", json_encode($answer),"\n\n\n";
    }



    public function actionUnloadsettings(){


        $par = [
            'shift_start_hours'=>date("H:i:s",time()-3600)
        ];


        $par['setting'] = $par;

        $answer = Yii::$app->testclient->getClient()->unloadsettings($par); 


        $result =isset($answer->returns) && isset($answer->returns->success) && $answer->returns->success ? "true" : "false";
        echo $result;
        echo " : ---Method Unloadsettings\n";
        //echo "\n\n\n", json_encode($answer),"\n\n\n";
    }


    public function actionUnloadprojectstandard(){
        $par = [
            [
                'project_guid'=>'231ca414-3f28-11e8-8114-005056b47a2e',
                'typeofwork_guid'=>'1cd95cf7-201f-11e8-8112-005056b47a2e',
                'standard'=>2.13
            ]
        ];


        $params['projectstandards'] = $par;

        $answer = Yii::$app->testclient->getClient()->unloadprojectstandard($params); 


        $result =isset($answer->returns) && isset($answer->returns->success) && $answer->returns->success ? "true" : "false";
        echo $result;
        echo " : ---Method unloadprojectstandard\n";
        echo "\n\n\n", json_encode($answer),"\n\n\n";
    }









    public function actionUpdateAppStatus(){
        $par = [
                'guid'=>'a4acc7f8-2f7c-11e9-8153-005056b47a2e',
                'status'=>'Отклонен'
        ];
        
        $par['applicationstatus'] = $par;

        $answer = Yii::$app->testclient->getClient()->updateapplicationstatus($par); 


        $result =isset($answer->returns) && isset($answer->returns->success) && $answer->returns->success ? "true" : "false";
        echo $result;
        echo " : ---Method Unloadsettings\n";
        echo "\n\n\n", json_encode($answer),"\n\n\n";
    }
}
