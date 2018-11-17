<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\Technic;
use common\models\TypeOfWork;
use common\models\Line;
use common\models\Objects;
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
                $results = User::find()->asArray()->all();
            }else{
                $results = User::find()->where(['is_master'=>1])->andWhere("`name` LIKE '%{$key}%'")->asArray()->all();//
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
                $results = User::find()->asArray()->all();
            }else{
                $results = User::find()->where("`name` LIKE '%{$key}%'")->asArray()->all();//
            }
            
            foreach ($results as $key => $value) {
                $data[] = ['value'=>$value['guid'],'title'=>$value['name']]; 
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

            if(!$key){
                $results = Objects::find()->asArray()->all();
            }else{
                $results = Objects::find()->where("`name` LIKE '%{$key}%'")->asArray()->all();//
            }
            
            foreach ($results as $key => $value) {
                $data[] = ['value'=>$value['guid'],'title'=>$value['name']]; 
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

            if(!$key){
                $results = Project::find()->asArray()->all();
            }else{
                $results = Project::find()->where("`name` LIKE '%{$key}%'")->asArray()->all();//
            }
            
            foreach ($results as $key => $value) {
                $data[] = ['value'=>$value['guid'],'title'=>$value['name']]; 
            }
            
            return ['data'=>$data];
        }else{
            return $this->redirect(['site/index']);
        } 
    }



}