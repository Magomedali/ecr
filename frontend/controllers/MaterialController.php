<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use common\models\Raport;
use common\models\RaportFile;

use frontend\modules\RaportFilter;

use yii\web\UploadedFile;

class MaterialController extends Controller{


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
                        'actions' => ['index','view','form'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

	
    public function beforeAction($action){
        if(defined('YII_DEBUG') && YII_DEBUG){
            Yii::$app->assetManager->forceCopy = true;
        }
        return parent::beforeAction($action);
    }



    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {   
        $user = Yii::$app->user->identity;
        if(!$user->brigade_guid){
            Yii::$app->user->logout();
            return $this->goHome();
        }

        
        return $this->render('index',[

        ]);

    }


    public function actionView($id){

        $brigade_guid = Yii::$app->user->identity->brigade_guid;
        if(!$brigade_guid){
            Yii::$app->user->logout();
            return $this->goHome();
        }

        if(!(int)$id) 
            throw new \Exception("Документ не найден!",404);

        $model = Raport::findOne(['id'=>(int)$id,'brigade_guid'=>$brigade_guid]);

        if(!isset($model->id))
            throw new \Exception("Документ не найден!",404);

        return $this->render('view',['model'=>$model]);
    }








    public function actionForm($id = null){


        $brigade_guid = Yii::$app->user->identity->brigade_guid;
        if(!$brigade_guid){
            Yii::$app->user->logout();
            return $this->goHome();
        }


        $post = Yii::$app->request->post();

        if($id || isset($post['model_id'])){
            $id = isset($post['model_id']) ? (int)$post['model_id'] : (int)$id;

            $model =  Raport::findOne(['id'=>$id,'brigade_guid'=>$brigade_guid]);
            if(!isset($model->id))
                throw new \Exception("Документ не найден!",404);

            if(!$model->isCanUpdate)
                throw new \Exception("Нет доступа к редактированию документа!",404);

        }else{
           $model = new Raport(); 
        }
        
        $hasErrors = false;
        $errorsRaport=[];

        if(isset($post['Raport']) && isset($post['password'])){

        
        }


        return $this->render('form',[
            'model'=>$model,
            'inValidPassword'=>$inValidPassword,
            'hasErrors'=>$hasErrors,
            'errorsRaportConsist'=>$errorsRaportConsist,
            'errorsRaportWorks'=>$errorsRaportWorks,
            'errorsRaportMaterials'=>$errorsRaportMaterials,
            'errorsRaport'=>$errorsRaport
        ]);
    }




}