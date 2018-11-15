<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use common\models\Raport;

class RaportController extends Controller{


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
                        'actions' => ['form'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

	

    public function actionForm($id = null){

        if($id){
           $model =  Raport::findOne($id);
           if(!isset($model->id))
                throw HttpException("Документ не найден!",404); 
       }else{
           $model = new Raport(); 
       }
        

        return $this->render('form',['model'=>$model]);
    }



}