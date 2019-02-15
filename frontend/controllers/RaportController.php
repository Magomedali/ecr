<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\web\HttpException;
use frontend\modules\RaportFilter;
use frontend\modules\RaportRegulatoryFilter;
use common\models\Raport;
use common\models\RaportFile;
use common\modules\RaportServiceSaver;
use common\modules\exceptions\{
    InvalidPasswordException,
    EmptyRequiredPropertiesException,
    ValidateErrorsException,
    ErrorRelationEntitiesException,
    ErrorExportTo1C,
    ModelNotFoundException,
    ModelCantUpdateException
};

class RaportController extends Controller{


    public $raportServiceSaver;



    public $command;

    
    public function __construct($id,$module,$config = []){
        
        $this->raportServiceSaver = new RaportServiceSaver(Yii::$app->user->identity);
        parent::__construct($id, $module, $config);
    }


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
                        'actions' => ['index','view','form','read-file','add-files','get-row-consist','get-row-work','get-row-remnant'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'checkShift'=>[
                'class'=>\common\behaviors\CheckShift::className(),
                'actions'=>['form'],
                'errorCallback'=>function($user,$action){
                    
                    $action->controller->command = function(){
                    
                        \Yii::$app->session->setFlash("warning","Предыдущая смена не закрыта. У вас есть неподтвержденные документы за предыдущую смену!");
                        return Yii::$app->response->redirect(['raport/index']);
                    
                    };
                }
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

            if(boolval($user->is_master)){
                return $this->redirect(['site/index']);
            }

            Yii::$app->user->logout();
            return $this->goHome();
        }

        $RaportFilter = new RaportFilter;
        $params = Yii::$app->request->queryParams;
        $params['RaportFilter']['brigade_guid']=$user->brigade_guid;
        $params['RaportFilter']['user_guid']=$user->guid;
        $dataProviderRaport = $RaportFilter->filter($params);


        $RaportRegulatoryFilter = new RaportRegulatoryFilter;
        $params = Yii::$app->request->queryParams;
        $params['RaportRegulatoryFilter']['brigade_guid']=$user->brigade_guid;
        $params['RaportRegulatoryFilter']['user_guid']=$user->guid;
        $dataProviderRaportRegulatory = $RaportRegulatoryFilter->filter($params);


        return $this->render('index',[
            'dataProviderRaport'=>$dataProviderRaport,
            'RaportFilter'=>$RaportFilter,
            'dataProviderRaportRegulatory'=>$dataProviderRaportRegulatory,
            'RaportRegulatoryFilter'=>$RaportRegulatoryFilter,
        ]);

    }


    public function actionView($id){

        $user = Yii::$app->user->identity;
        
        if(!$user->brigade_guid && !$user->is_master){
            Yii::$app->user->logout();
            return $this->goHome();
        }

        if(!(int)$id) 
            throw new \Exception("Документ не найден!",404);

        $q = Raport::find()->where(['id'=>(int)$id]);

        if(!$user->is_master){
            $q->andWhere(['brigade_guid'=>$user->brigade_guid]);
        }

        $model = $q->one();
        if(!isset($model->id)  || ($user->is_master && $user->guid != $model->master_guid))
            throw new \Exception("Документ не найден!",404);

        return $this->render('view',['model'=>$model]);
    }





    public function actionReadFile($id){

        $user = Yii::$app->user->identity;
        
        if(!$user->brigade_guid && !$user->is_master){
            Yii::$app->user->logout();
            return $this->goHome();
        }

        if(!(int)$id) 
            throw new \Exception("Документ не найден!",404);

        $q = RaportFile::find()->innerJoin(['r'=>Raport::tableName()],RaportFile::tableName().".raport_id = r.id")->where([RaportFile::tableName().'.id'=>(int)$id]);

        if(!$user->is_master){
            $q->andWhere(['r.brigade_guid'=>$user->brigade_guid]);
        }
        
        $model = $q->one();

        if(!isset($model->id) || ($user->is_master && $user->guid != $model->master_guid))
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

        if(!$this->raportServiceSaver->userCan($id)){
            Yii::$app->user->logout();
            return $this->goHome();
        }

        if($this->command && is_callable($this->command)){
            return call_user_func($this->command);
        }

        $post = Yii::$app->request->post();
        try {
            $model = $this->raportServiceSaver->getForm($post,$id);
        } catch (ModelNotFoundException $e) {
            Yii::$app->session->setFlash("error","Документ не найден.");
            return $this->redirect(['raport/index']);
        } catch (ModelCantUpdateException $e) {
            Yii::$app->session->setFlash("error","Документ неьзя редактировать.");
            return $this->redirect(['raport/index']);
        } catch (\Exception $e) {
            Yii::$app->session->setFlash("error","Ошибка при обработке запроса, обратитесь в тех. поддержку!");
            return $this->redirect(['raport/index']);
        }
        
        
        $hasErrors = false;
        $inValidPassword = false;
        $errorsRaport = [];
        $errorsRaportConsist = [];
        $errorsRaportWorks = [];
        $errorsRaportMaterials = [];
        $errors = [];


        if(isset($post['Raport'])){
            try{
            
                $this->raportServiceSaver->save($post);
                Yii::$app->session->setFlash("success","Рапорт успешно отправлен на проверку!");
                return $this->redirect(['raport/index']);
            
            }catch(InvalidPasswordException $e){
            
                Yii::$app->session->setFlash("error","Введен неправильный пароль!");
                $inValidPassword = true;
            
            }catch(EmptyRequiredPropertiesException $e){
                $inValidPassword = true;
                Yii::$app->session->setFlash("error","Рапорт не сохранен. Отсутствуют обязательные данные!");

            }catch(ValidateErrorsException $e){

                Yii::$app->session->setFlash("error","Рапорт не сохранен. Неправильный формат данных!");
                Yii::warning("Error when save raport","raportform");
                Yii::warning(json_encode($model->getErrors()),"raportform");
                $errors = $model->getErrors();

            }catch(ErrorRelationEntitiesException $e){
                
                Yii::$app->session->setFlash("error","Рапорт не сохранен. Некорректные данные в табличной части рапорта имеют не корректные данные");
                Yii::warning("Error when save raport tables data","raportform");
                Yii::warning(json_encode($model->getConsistErrors()),"raportform");
                Yii::warning(json_encode($model->getWorksErrors()),"raportform");
                Yii::warning(json_encode($model->getMaterialsErrors()),"raportform");
                $errors = count($errors) ? $errors : $model->getConsistErrors();
                $errors = count($errors) ? $errors : $model->getWorksErrors();
                $errors = count($errors) ? $errors : $model->getMaterialsErrors();
                
            }catch(ErrorExportTo1C $e){

                Yii::$app->session->setFlash("success","Рапорт успешно сохранен!");
                Yii::$app->session->setFlash("error","Ошибка, при отправлении рапорта на проверку");
                return $this->redirect(['raport/index']);
                
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
            $errorsRaport = isset($post['Raport']) ? $post['Raport'] : [];
            $errorsRaportConsist = isset($post['RaportConsist']) ? $post['RaportConsist'] : [];
            $errorsRaportWorks = isset($post['RaportWork']) ? $post['RaportWork'] : [];
            $errorsRaportMaterials = isset($post['RaportMaterial']) ? $post['RaportMaterial'] : [];
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
        $user = Yii::$app->user->identity;
        if(!$user->brigade_guid && !$user->is_master){
            Yii::$app->user->logout();
            return $this->goHome();
        }

        $post = Yii::$app->request->post();

        if(!$id && !isset($post['model_id']))
            throw new \Exception("Документ не найден!",404);

        $id = isset($post['model_id']) ? (int)$post['model_id'] : (int)$id;
        
        $q = Raport::find()->where(['id'=>(int)$id]);

        if(!$user->is_master){
            $q->andWhere(['brigade_guid'=>$user->brigade_guid]);
        }
        $model = $q->one();

        if(!isset($model->id)  || ($user->is_master && $user->guid != $model->master_guid))
            throw new \Exception("Документ не найден!",404);

        $files = UploadedFile::getInstancesByName('files');

        if($model->saveFiles($files)){
            Yii::$app->session->setFlash("success","Файлы прикреплены к рапорту");

            //Отправить в 1С
            if($model->sendToConfirmation()){
                Yii::$app->session->setFlash("success","Рапорт успешно отправлен на проверку!");  
            }else{
                Yii::$app->session->setFlash("error","Ошибка, при отправлении рапорта на проверку");
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