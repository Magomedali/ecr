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
                        'actions' => ['masters','users','technics','lines','works','objects','projects'],
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

            if(!$key){
                $results = User::find()->where(['is_master'=>1])->andWhere("`guid` !=''")->asArray()->all();
            }else{
                $results = User::find()->where(['is_master'=>1])->andWhere("`guid` !=''")->andWhere("`name` LIKE '%{$key}%'")->asArray()->all();//
            }
            
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

            if(!$key){
                $results = User::find()->andWhere("`guid` !=''")->andwhere(['is_master'=>0])->asArray()->all();
            }else{
                $results = User::find()->where("`name` LIKE '%{$key}%'")->andwhere(['is_master'=>0])->andWhere("`guid` !=''")->asArray()->all();//
            }
            
            foreach ($results as $key => $value) {
                $data[] = ['value'=>$value['guid'],'title'=>$value['name'],'ktu'=>$value['ktu']]; 
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

            if(!$key){
                $results = Line::find()->asArray()->all();
            }else{
                $results = Line::find()->where("`name` LIKE '%{$key}%'")->asArray()->all();//
            }
            
            foreach ($results as $key => $value) {
                $data[] = ['value'=>$value['guid'],'title'=>$value['name']]; 
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



}