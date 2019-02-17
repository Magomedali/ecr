<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use common\models\RaportRegulatory;
use common\modules\ExportRaportRegulatoryLoad;
use common\modules\CheckCloseShift;

class RaportRegulatoryController extends Controller{


    public $command;

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
                        'actions' => ['index','view','form','get-row-work'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'checkShift'=>[
                'class'=>\common\behaviors\CheckShift::className(),
                'actions'=>['form'],
                'enableNotes'=>true,
                'methods'=>['GET'],
                'errorCallback'=>function($user,$action){
                    
                    $action->controller->command = function(){
                    
                        \Yii::$app->session->setFlash("warning","Предыдущая смена не закрыта или у вас есть неподтвержденные документы!");
                        return Yii::$app->response->redirect(['raport/index']);
                    
                    };
                }
            ],
            'LoadNotes'=>[
                'class'=>\common\behaviors\LoadNotes::className(),
                'actions'=>['index','view'],
                'methods'=>['GET'],
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
        return $this->redirect(['raport/index']);
    }


    public function actionView($id){

        $user = Yii::$app->user->identity;
        if(!$user->brigade_guid && !$user->is_master){
            Yii::$app->user->logout();
            return $this->goHome();
        }

        if(!(int)$id) 
            throw new \Exception("Документ не найден!",404);

        $q = RaportRegulatory::find()->where(['id'=>(int)$id]);

        if(!$user->is_master){
            $q->andWhere(['brigade_guid'=>$user->brigade_guid]);
        }
        $model = $q->one();

        if(!isset($model->id) || ($user->is_master && $user->guid != $model->master_guid))
            throw new \Exception("Документ не найден!",404);

        return $this->render('view',['model'=>$model]);
    }




    public function actionForm($id = null){

        $user = Yii::$app->user->identity;
        if(!$user->brigade_guid && !$user->is_master){
            Yii::$app->user->logout();
            return $this->goHome();
        }

        if($this->command && is_callable($this->command)){
            return call_user_func($this->command);
        }
        
        $post = Yii::$app->request->post();

        if($id || isset($post['model_id'])){
            $id = isset($post['model_id']) ? (int)$post['model_id'] : (int)$id;

            $q = RaportRegulatory::find()->where(['id'=>(int)$id]);

            if(!$user->is_master){
                $q->andWhere(['brigade_guid'=>$user->brigade_guid]);
            }

            $model = $q->one();
            
            if(!isset($model->id) || ($user->is_master && $user->guid != $model->master_guid))
                throw new \Exception("Документ не найден!",404);

            if(!$model->isCanUpdate)
                throw new \Exception("Нет доступа к редактированию документа!",404);

        }else{
           $model = new RaportRegulatory(); 
        }
        


        $hasErrors = false;
        $inValidPassword = false;
        $errorsRaport = [];
        $errorsRaportRegulatoryWork = [];
        $errors = [];

        if(isset($post['RaportRegulatory'])){

            $data = $post;
            if(!boolval($user->is_master)){
                $data['RaportRegulatory']['user_guid']=$user->guid;
                $data['RaportRegulatory']['brigade_guid']=$user->brigade_guid;   
            }
            

            if($model->load($data) && $model->save(1)){
                        
                $model->saveRelationEntities();

                if(count($model->getWorksErrors())){
                    Yii::$app->session->setFlash("error","Рапорт не сохранен. Некорректные данные в табличной части рапорта имеют не корректные данные");
                    Yii::warning("Error when save raport tables data","raportform");
                    Yii::warning(json_encode($model->getWorksErrors()),"raportform");
                    $errors = count($errors) ? $errors : $model->getWorksErrors();
                }else{
                    Yii::$app->session->setFlash("success","Рапорт успешно сохранен");

                    try {
                        //Отправить заявку в 1С
                        if(ExportRaportRegulatoryLoad::export($model)){
                            Yii::$app->session->setFlash("success","Рапорт успешно отправлен на проверку!");  
                        }else{
                            Yii::$app->session->setFlash("error","Ошибка, при отправлении рапорта на проверку");
                        }
                        
                    } catch (\Exception $e) {
                        Yii::$app->session->setFlash("error","Ошибка, при отправлении рапорта на проверку");
                    }
                    

                    return $this->redirect(['raport/index']);
                }
            }else{
                Yii::$app->session->setFlash("error","Рапорт не сохранен. Отсутствуют обязательные данные!");
                Yii::warning("Error when save raport","raportform");
                Yii::warning(json_encode($model->getErrors()),"raportform");
                $errors = $model->getErrors();
            }


            if(count($errors)){
                foreach ($errors as $key => $er) {
                    if(!is_array($er)){
                        Yii::$app->session->setFlash("warning",$er);
                        Yii::warning($key.": ",$er,"raportform");
                    }else{
                        foreach ($er as $key2 => $e) {
                            Yii::$app->session->setFlash("warning",$e);
                            Yii::warning($key2.": ",$e,"raportform");
                        }
                    }
                }
            }
            
            $hasErrors = true;
            $errorsRaport = isset($post['RaportRegulatory']) ? $post['RaportRegulatory'] : [];
            $errorsRaportRegulatoryWork = isset($post['RaportRegulatoryWork']) ? $post['RaportRegulatoryWork'] : [];
        }

        return $this->render('form',[
            'model'=>$model,
            'inValidPassword'=>$inValidPassword,
            'hasErrors'=>$hasErrors,
            'errorsRaportRegulatoryWork'=>$errorsRaportRegulatoryWork,
            'errorsRaport'=>$errorsRaport
        ]);
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



}