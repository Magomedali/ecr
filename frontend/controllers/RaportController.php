<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use common\models\Raport;
use common\models\RaportFile;
use yii\web\UploadedFile;

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
                        'actions' => ['view','form','read-file','add-files','get-row-consist','get-row-work','get-row-remnant'],
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





    public function actionReadFile($id){

        $brigade_guid = Yii::$app->user->identity->brigade_guid;
        if(!$brigade_guid){
            Yii::$app->user->logout();
            return $this->goHome();
        }

        if(!(int)$id) 
            throw new \Exception("Документ не найден!",404);

        $model = RaportFile::find()->innerJoin(['r'=>Raport::tableName()],RaportFile::tableName().".raport_id = r.id")->where([RaportFile::tableName().'.id'=>(int)$id,'r.brigade_guid'=>$brigade_guid])->one();

        if(!isset($model->id))
            throw new \Exception("Документ не найден!",404);

        $filePath = "tmp/".$model['file'];
                                    
        if(!file_exists($filePath)){
            $f = fopen($filePath, "w");
            fwrite($f, $model['file_binary']);
            fclose($f);
        }

        return Yii::$app->response->sendFile($filePath);
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
        $inValidPassword = false;
        $errorsRaport = [];
        $errorsRaportConsist = [];
        $errorsRaportWorks = [];
        $errorsRaportMaterials = [];
        $errorsRaport=[];
        if(isset($post['Raport']) && isset($post['password'])){

            
            if($model->load($post)){
                $password = trim(strip_tags($post['password']));
                if(!Yii::$app->user->identity->validatePassword($password)){
                    Yii::$app->session->setFlash("error","Введен неправильный пароль!");
                    $inValidPassword = true;
                }else{

                    if($model->save(1)){
                        
                        $model->saveRelationEntities();

                        if(count($model->getConsistErrors()) || count($model->getWorksErrors()) || count($model->getMaterialsErrors())){
                            Yii::$app->session->setFlash("error","Рапорт не сохранен. Некорректные данные");
                        }else{
                            Yii::$app->session->setFlash("success","Рапорт отправлен на проверку");

                            //Отправить заявку в 1С
                            $model->sendToConfirmation();

                            return $this->redirect(['site/index']);
                        }
                    }else{
                        Yii::$app->session->setFlash("error","Рапорт не сохранен!");
                    }

                }
            }else{
                Yii::$app->session->setFlash("error","Рапорт не сохранен. Отсутствуют обязательные данные!");
            }
            
            $hasErrors = true;
            $errorsRaportConsist = isset($post['RaportConsist']) ? $post['RaportConsist'] : [];
            $errorsRaportWorks = isset($post['RaportWork']) ? $post['RaportWork'] : [];
            $errorsRaportMaterials = isset($post['RaportMaterial']) ? $post['RaportMaterial'] : [];
            $errorsRaport = isset($post['Raport']) ? $post['Raport'] : [];

            
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




    public function actionAddFiles($id = null){
        $brigade_guid = Yii::$app->user->identity->brigade_guid;
        if(!$brigade_guid){
            Yii::$app->user->logout();
            return $this->goHome();
        }

        $post = Yii::$app->request->post();

        if(!$id && !isset($post['model_id']))
            throw new \Exception("Документ не найден!",404);

        $id = isset($post['model_id']) ? (int)$post['model_id'] : (int)$id;
        
        $model =  Raport::findOne(['id'=>$id,'brigade_guid'=>$brigade_guid]);
        
        if(!isset($model->id))
            throw new \Exception("Документ не найден!",404);

        $files = UploadedFile::getInstancesByName('files');

        if($model->saveFiles($files)){
            Yii::$app->session->setFlash("success","Файлы прикреплены к рапорту");

            //Отправить в 1С
            $model->sendToConfirmation();
            
        }else{
            Yii::$app->session->setFlash("error","Файлы не удалось прикрепить к рапорту");
        }

        return $this->redirect(['raport/view','id'=>$model->id]);
    }




    public function actionGetRowConsist(){

        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $get = Yii::$app->request->get();

            $count = isset($get['count']) ? (int)$get['count'] : 0;

            $ans['html'] = $this->renderPartial("formRowConsist",[
                                                    'count'=>$count
                                                ]);
            return $ans;
        }else{
            return $this->goBack();
        }
        
    }






    public function actionGetRowWork(){

        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $get = Yii::$app->request->get();

            $count = isset($get['count']) ? (int)$get['count'] : 0;

            $ans['html'] = $this->renderPartial("formRowWork",[
                                                    'count'=>$count
                                                ]);
            return $ans;
        }else{
            return $this->goBack();
        }
    }





    public function actionGetRowRemnant(){

        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $get = Yii::$app->request->get();

            $count = isset($get['count']) ? (int)$get['count'] : 0;

            $ans['html'] = $this->renderPartial("formRowRemnant",[
                                                    'count'=>$count
                                                ]);
            return $ans;
        }else{
            return $this->goBack();
        }
    }



}