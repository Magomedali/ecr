<?php

namespace backend\controllers;

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
                        'roles' => ['superadmin','administrator'],
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




    public function actionView($id = null){

        $get = Yii::$app->request->get();
        if(!(int)$id  && !isset($get['guid'])) 
            throw new \Exception("Документ не найден!",404);

        if((int)$id)
            $model = Raport::findOne(['id'=>(int)$id]);
        else
            $model = Raport::findOne(['guid'=>$get['guid']]);


        if(!isset($model->id))
            throw new \Exception("Документ не найден!",404);

        return $this->render('view',['model'=>$model]);
    }





    public function actionReadFile($id){

        if(!(int)$id) 
            throw new \Exception("Документ не найден!",404);

        $model = RaportFile::findOne((int)$id);

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

        $post = Yii::$app->request->post();

        if($id || isset($post['model_id'])){
            $id = isset($post['model_id']) ? (int)$post['model_id'] : (int)$id;

            $model =  Raport::findOne(['id'=>$id]);
            if(!isset($model->id))
                throw new \Exception("Документ не найден!",404);
        }else{
           throw new \Exception("Ошибка в запросе",404);
        }
        
        $hasErrors = false;
        $errorsRaport = [];
        $errorsRaportConsist = [];
        $errorsRaportWorks = [];
        $errorsRaportMaterials = [];
        $errorsRaport=[];
        if(isset($post['Raport'])){

            if($model->load($post) && $model->save(1)){

                $model->saveRelationEntities();

                if(count($model->getConsistErrors()) || count($model->getWorksErrors()) || count($model->getMaterialsErrors())){
                    Yii::$app->session->setFlash("error","Рапорт не сохранен. Некорректные данные в табличной части рапорта");
                    Yii::warning("Error when save raport tables data","raportform");
                    Yii::warning(json_encode($model->getConsistErrors()),"unloadremnant");
                    Yii::warning(json_encode($model->getWorksErrors()),"unloadremnant");
                    Yii::warning(json_encode($model->getMaterialsErrors()),"unloadremnant");
                }else{
                    Yii::$app->session->setFlash("success","Рапорт успешно сохранен");

                    //Отправить заявку в 1С
                    if($model->sendToConfirmation()){
                        Yii::$app->session->setFlash("success","Рапорт успешно отправлен на проверку!");  
                    }else{
                        Yii::$app->session->setFlash("error","Ошибка, при отправлении рапорта на проверку");
                    }

                    return $this->redirect(['user/view','guid'=>$model->user_guid]);
                }

            }else{
                Yii::$app->session->setFlash("error","Рапорт не сохранен. Обнаружены ошибки при проверке данных!");
                Yii::warning("Error when save raport","raportform");
                Yii::warning(json_encode($model->getErrors()),"unloadremnant");
            }
            
            $hasErrors = true;
            $errorsRaport = isset($post['Raport']) ? $post['Raport'] : [];
            $errorsRaportConsist = isset($post['RaportConsist']) ? $post['RaportConsist'] : [];
            $errorsRaportWorks = isset($post['RaportWork']) ? $post['RaportWork'] : [];
            $errorsRaportMaterials = isset($post['RaportMaterial']) ? $post['RaportMaterial'] : [];
        }


        return $this->render('form',[
            'model'=>$model,
            'hasErrors'=>$hasErrors,
            'errorsRaportConsist'=>$errorsRaportConsist,
            'errorsRaportWorks'=>$errorsRaportWorks,
            'errorsRaportMaterials'=>$errorsRaportMaterials,
            'errorsRaport'=>$errorsRaport
        ]);
    }




    public function actionAddFiles($id = null){
        
        $post = Yii::$app->request->post();

        if(!$id && !isset($post['model_id']))
            throw new \Exception("Документ не найден!",404);

        $id = isset($post['model_id']) ? (int)$post['model_id'] : (int)$id;
        
        $model =  Raport::findOne(['id'=>$id]);
        
        if(!isset($model->id))
            throw new \Exception("Документ не найден!",404);

        $files = UploadedFile::getInstancesByName('files');

        if($model->saveFiles($files)){
            Yii::$app->session->setFlash("success","Файлы прикреплены к рапорту");

            //Отправить в 1С
            if($model->sendToConfirmation()){
                Yii::$app->session->setFlash("success","Файлы прикреплены к рапорту");  
            }else{
                Yii::$app->session->setFlash("error","Файлы не удалось отправть в 1С");
            }
            
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