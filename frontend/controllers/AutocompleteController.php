<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use common\models\User;
use common\models\Technic;
use common\models\TypeOfWork;
use common\models\Line;
use common\models\Objects;
use common\models\Boundary;
use common\models\Project;
use soapclient\methods\Calcsquare;

class AutocompleteController extends Controller{


	/**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['masters','users','technics','lines','works','objects','projects','calcsquare'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

	public function actionMasters(){

        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $data = [];
            $get = Yii::$app->request->get();
            $key = isset($get['key']) ? trim(strip_tags($get['key'])) : null;

            $query = User::find()->where(['is_master'=>1])->andWhere("`guid` is not null");
            if($key){
                $query->andWhere("`name` LIKE '%{$key}%'");
            }
            
            $results = $query->asArray()->all();

            foreach ($results as $key => $value) {
                $data[] = ['value'=>$value['guid'],'title'=>$value['name']]; 
            }
            
            return ['data'=>$data];
        }else{
            return $this->redirect(['site/index']);
        } 
    }



    public function actionUsers(){

        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $data = [];
            $get = Yii::$app->request->get();
            $key = isset($get['key']) ? trim(strip_tags($get['key'])) : null;
            $users_extends = isset($get['users_extends']) && is_array($get['users_extends']) ? $get['users_extends'] : array();

            $query = (new Query())->select(['u.guid','u.name','u.ktu'])
                                ->from(['u'=>User::tableName()])
                                //->leftJoin(['t'=>Technic::tableName()], "u.technic_guid = t.guid")
                                ->where(['u.is_master'=>0])
                                ->andWhere("u.`guid` is not null");
            if($key){
                $query->andWhere("u.`name` LIKE '%{$key}%'");
            }

            if(count($users_extends)){
                $notIn = array();
                foreach ($users_extends as $guid) {
                    if (!$guid) continue;

                    $notIn[] = "'{$guid}'";
                }
                if(count($notIn)){
                    $notIn = implode(",", $notIn);
                    $query->andWhere("u.`guid` NOT IN ($notIn)");
                }
            }

            $results = $query->all();

            foreach ($results as $key => $value) {
                $data[] = [
                    'value'=>$value['guid'],
                    'title'=>$value['name'],
                    'ktu'=>$value['ktu']
                ]; 
            }
            
            return ['data'=>$data];
        }else{
            return $this->redirect(['site/index']);
        } 
    }





    public function actionTechnics(){

        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $data = [];
            $get = Yii::$app->request->get();
            $key = isset($get['key']) ? trim(strip_tags($get['key'])) : null;

            if(!$key){
                $results = Technic::find()->asArray()->all();
            }else{
                $results = Technic::find()->where("`name` LIKE '%{$key}%'")->asArray()->all();//
            }
            
            foreach ($results as $key => $value) {
                $data[] = ['value'=>$value['guid'],'title'=>$value['name']]; 
            }
            
            return ['data'=>$data];
        }else{
            return $this->redirect(['site/index']);
        } 
    }


    public function actionLines(){

        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $data = [];
            $get = Yii::$app->request->get();
            $key = isset($get['key']) ? trim(strip_tags($get['key'])) : null;

            $query = Line::find();

            if($key){
                $query->where("`name` LIKE '%{$key}%'");//
            }
            
            $results = $query->asArray()->all();//

            foreach ($results as $key => $value) {
                $data[] = [
                    'value'=>$value['guid'],
                    'title'=>$value['name'],
                    'is_countable'=>$value['is_countable'],
                    'hint_count'=>$value['hint_count'],
                    'hint_length'=>$value['hint_length'],
                ]; 
            }
            
            return ['data'=>$data];
        }else{
            return $this->redirect(['site/index']);
        } 
    }






    public function actionWorks(){

        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $data = [];
            $get = Yii::$app->request->get();
            $key = isset($get['key']) ? trim(strip_tags($get['key'])) : null;

            if(!$key){
                $results = TypeOfWork::find()->asArray()->all();
            }else{
                $results = TypeOfWork::find()->where("`name` LIKE '%{$key}%'")->asArray()->all();//
            }
            
            foreach ($results as $key => $value) {
                $data[] = ['value'=>$value['guid'],'title'=>$value['name']]; 
            }
            
            return ['data'=>$data];
        }else{
            return $this->redirect(['site/index']);
        } 
    }







    public function actionObjects(){

        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $data = [];
            $get = Yii::$app->request->get();
            $key = isset($get['key']) ? trim(strip_tags($get['key'])) : null;


            $query = (new Query())->select(['o.*','b.name as boundary_name'])->from(['o'=>Objects::tableName()])
                        ->leftJoin(['b'=>Boundary::tableName()]," o.boundary_guid = b.guid ");
            if($key){
                $query = $query->where("o.`name` LIKE '%{$key}%'");
            }

            $results = $query->all();

            foreach ($results as $key => $value) {
                $data[] = ['value'=>$value['guid'],'title'=>$value['name'],'boundary_guid'=>$value['boundary_guid'],'boundary_name'=>$value['boundary_name']]; 
            }
            
            return ['data'=>$data];
        }else{
            return $this->redirect(['site/index']);
        } 
    }






    public function actionProjects(){

        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $data = [];
            $get = Yii::$app->request->get();
            $key = isset($get['key']) ? trim(strip_tags($get['key'])) : null;
            $object_guid = isset($get['object_guid']) ? trim(strip_tags($get['object_guid'])) : null;

            $query = (new Query())->select(['p.*'])->from(['p'=>Project::tableName()]);

            if($object_guid){
                $query->innerJoin(['po'=>Project::tableNameRelObjects()]," po.project_guid = p.guid ")->andWhere(['po.object_guid'=>$object_guid]);
            }

            if($key){
                $query = $query->andWhere("p.`name` LIKE '%{$key}%'");
            }

            $results = $query->all();
            
            foreach ($results as $key => $value) {
                $data[] = ['value'=>$value['guid'],'title'=>$value['name']]; 
            }
            
            return ['data'=>$data];
        }else{
            return $this->redirect(['site/index']);
        }
    }






    public function actionCalcsquare(){
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            
            $get = Yii::$app->request->get();
            $line_guid = isset($get['line_guid']) ? trim(strip_tags($get['line_guid'])) : null;
            $length = isset($get['length']) ? trim(strip_tags($get['length'])) : null;
            $count = isset($get['count']) ? trim(strip_tags($get['count'])) : null;
            $result = false;
            $error = $errorMessage = null;
            $responce = null;
            if($length && $line_guid){
                try {
                    
                    $method = new Calcsquare([
                        'lineguid'=>$line_guid,
                        'length'=>$length,
                        'count'=>$count
                    ]);
                    if($method->validate()){
                        $responce = Yii::$app->webservice1C->send($method);

                        $responce = json_decode(json_encode($responce),1);
                        if(isset($responce['return']) && isset($responce['return']['success']) && boolval($responce['return']['success']) && isset($responce['return']['result']) && $responce['return']['result']){
                            $result = sprintf("%.3f",$responce['return']['result']);
                        }
                    }else{
                        $error = "ModelValidateError";
                        $errorMessage = $method->getErrors();
                    }

                }catch(\SoapFault $e){
                    $error = "SoapFault";
                    $errorMessage = $e->getMessage();
                }catch(\Exception $e){
                    $error = "ServerError";
                    $errorMessage = $e->getMessage();
                }
            }
            return [
                'result'=>$result,
                'error'=>$error,
                'errorMessage'=>$errorMessage,
                'responce'=>$responce
            ];
        }else{
            return $this->redirect(['site/index']);
        }
    }

}