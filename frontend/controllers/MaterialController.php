<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use common\models\MaterialsApp;
use frontend\modules\MaterialAppFilter;

use common\modules\ImportListOfDocuments;
use common\modules\TransferMaterials;
use common\modules\CheckCloseShift;
use common\dictionaries\{AppStatuses,DocumentTypes};
use common\base\Controller;


use common\modules\notes\NoteCollections;
use common\services\MaterialSaverService;
use common\modules\exceptions\{
    InvalidPasswordException,
    EmptyRequiredPropertiesException,
    ValidateErrorsException,
    ErrorRelationEntitiesException,
    ErrorExportTo1C,
    ModelNotFoundException,
    ModelCantUpdateException,
    UserNotMasterException
};

class MaterialController extends Controller{


    protected $user;
    
    public $command;

    protected $materialSaverService;

    protected $brigade_guid;


    public function __construct($id,$module,$config = []){
        
        $this->user = Yii::$app->user->identity;
        $this->materialSaverService = new MaterialSaverService($this->user);
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
                        'actions' => ['index','view','open','form','get-row-material'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'checkShift'=>[
                'class'=>\common\behaviors\CheckShift::className(),
                'actions'=>['form'],
                'methods'=>['GET'],
                'errorCallback'=>function($user,$action){
                    
                    $action->controller->command = function(){
                    
                        \Yii::$app->session->setFlash("warning","Предыдущая смена не закрыта. У вас есть неподтвержденные документы за предыдущую смену!");
                        return Yii::$app->response->redirect(['material/index']);
                    
                    };
                },
                'exceptCondition'=>function(){
                    $get = Yii::$app->request->get();
                    $post = Yii::$app->request->post();
                    return (isset($get['id']) && (int)$get['id']) || (isset($post['model_id']) && (int)$post['model_id']);
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
        $user = Yii::$app->user->identity;
        if(!$user->brigade_guid){
            Yii::$app->user->logout();
            return $this->goHome();
        }


        $modelFilters = new MaterialAppFilter;
        $params = Yii::$app->request->queryParams;
        $params['MaterialAppFilter']['user_guid']=$user->guid;
        $params['MaterialAppFilter']['statusCode']=[AppStatuses::IN_CONFIRMING,AppStatuses::CREATED,AppStatuses::CONFIRMED];
        
        $dataProvider = $modelFilters->filter($params);

        $documents = NoteCollections::getDocs();

        $remnants = $user->getActualBrigadeRemnants();
        
        $unExportedDocs = TransferMaterials::getActualTransfersFromUser(Yii::$app->user->id);

        return $this->render('index',[
            'dataProvider'=>$dataProvider,
            'modelFilters'=>$modelFilters,
            'documents'=>$documents,
            'remnants'=>$remnants,
            'unExportedDocs'=>$unExportedDocs,
        ]);

    }


    public function actionView($id){

        if(!(int)$id) 
            throw new \Exception("Документ не найден!",404);

        $model = MaterialAppFilter::findOne(['id'=>(int)$id,'user_guid'=>$this->user->guid]);

        if(!isset($model->id))
            throw new \Exception("Документ не найден!",404);

        return $this->render('view',['model'=>$model]);
    }



    public function actionOpen($id){

        if(!(int)$id) 
            throw new \Exception("Документ не найден!",404);

        if(!$this->materialSaverService->userCan($id)){
            Yii::$app->user->logout();
            return $this->goHome();
        }
        $post = Yii::$app->request->post();
        try {
            $model = $this->materialSaverService->getForm($post,$id);
        } catch (ModelNotFoundException $e) {
            Yii::$app->session->setFlash("error","Документ не найден.");
            return $this->redirect(['site/index']);
        } catch (ModelCantUpdateException $e) {
            Yii::$app->session->setFlash("error","Документ нельзя редактировать.");
            return $this->redirect(['site/index']);
        } catch (\Exception $e) {
            Yii::$app->session->setFlash("error","Ошибка при обработке запроса, обратитесь в тех. поддержку!");
            return $this->redirect(['site/index']);
        }

        if(isset($post['new_status']) && (int)$post['new_status']){
            try {
                $this->materialSaverService->changeStatus((int)$post['new_status']);
                Yii::$app->session->setFlash("success","Статус заявки успешно изменен!");
                return $this->redirect(['site/index']);
            }catch(UserNotMasterException $e){
                Yii::$app->session->setFlash("error","Недостаточно парв!");
                Yii::$app->user->logout();
                return $this->goHome();
            }catch(ValidateErrorsException $e){
                Yii::$app->session->setFlash("error","Неправильный формат данных");
                Yii::warning("Error when save raport","materialChangeStatusform");
                Yii::warning(json_encode($model->getErrors()),"materialChangeStatusform");
            }catch(ErrorExportTo1C $e){
                Yii::$app->session->setFlash("success","Статус заявки успешно изменен!");
                Yii::$app->session->setFlash("error","Ошибка, при отправлении изменения статуса в 1С");
                return $this->redirect(['site/index']);
            }catch (\Exception $e) {
                Yii::$app->session->setFlash("error","Ошибка при обработке запроса, обратитесь в тех. поддержку!");
                return $this->redirect(['site/index']);
            }
        }

        if(!isset($model->id))
            throw new \Exception("Документ не найден!",404);

        return $this->render('view',['model'=>$model]);
    }




    public function actionForm($id = null){

        if(!$this->materialSaverService->userCan($id)){
            Yii::$app->user->logout();
            return $this->goHome();
        }

        if($this->command && is_callable($this->command)){
            return call_user_func($this->command);
        }

        $post = Yii::$app->request->post();
        try {
            $model = $this->materialSaverService->getForm($post,$id);
        } catch (ModelNotFoundException $e) {
            Yii::$app->session->setFlash("error","Документ не найден.");
            return $this->redirect(['material/index']);
        } catch (ModelCantUpdateException $e) {
            Yii::$app->session->setFlash("error","Документ нельзя редактировать.");
            return $this->redirect(['material/index']);
        } catch (\Exception $e) {
            Yii::$app->session->setFlash("error","Ошибка при обработке запроса, обратитесь в тех. поддержку!");
            return $this->redirect(['material/index']);
        }


        $hasErrors = false;
        $inValidPassword = false;
        $errorsMaterialsApp=[];
        $errorsMaterialsAppItem = [];
        $errors = [];
        if(isset($post['MaterialsApp'])){

            try {
                $this->materialSaverService->save($post);
                if($model->status == AppStatuses::DELETED){
                    Yii::$app->session->setFlash("success","Заявка успешно отменена!");
                }else{
                    Yii::$app->session->setFlash("success","Заявка успешно отправлена на проверку!");
                }
                
                return $this->redirect(['material/index']);
            }catch(InvalidPasswordException $e){
            
                Yii::$app->session->setFlash("error","Введен неправильный пароль!");
                $inValidPassword = true;
            
            }catch(EmptyRequiredPropertiesException $e){
                $inValidPassword = true;
                Yii::$app->session->setFlash("error","Рапорт не сохранен. Отсутствуют обязательные данные!");

            }catch(ValidateErrorsException $e){
                Yii::$app->session->setFlash("error","Возникла ошибка при сохранении заявки. Заявка не сохранена!");
                Yii::warning("Error when save raport","materialform");
                Yii::warning(json_encode($model->getErrors()),"materialform");
                $errors = $model->getErrors();
            }catch(ErrorRelationEntitiesException $e){
                
                Yii::$app->session->setFlash("error","Заявка не сохранена. Некорректные данные в табличной части заявки имеют не корректные данные");
                Yii::warning("Error when save raport tables data","materialform");
                Yii::warning(json_encode($model->getItemsErrors()),"materialform");
                $errors = $model->getItemsErrors();
                
            }catch(ErrorExportTo1C $e){
                Yii::$app->session->setFlash("success","Заявка успешно сохранена!");
                Yii::$app->session->setFlash("error","Ошибка, при отправлении заявки на проверку");
                return $this->redirect(['material/index']);
            }
                
            if(count($errors)){
                foreach ($errors as $key => $er) {
                    if(!is_array($er)){
                        Yii::$app->session->setFlash("warning",$er);
                        Yii::warning($key.": ",$er,"materialform");
                    }else{
                        foreach ($er as $key2 => $e) {
                            Yii::$app->session->setFlash("warning",$e);
                            Yii::warning($key2.": ",$e,"materialform");
                        }
                    }
                }
            }

            $hasErrors = true;
            $errorsMaterialsApp = isset($post['MaterialsApp']) ? $post['MaterialsApp'] : [];
            $errorsMaterialsAppItem = isset($post['MaterialsAppItem']) ? $post['MaterialsAppItem'] : [];
        }


        return $this->render('form',[
            'model'=>$model,
            'hasErrors'=>$hasErrors,
            'errors'=>$errors,
            'inValidPassword'=>$inValidPassword,
            'errorsMaterialsApp'=>$errorsMaterialsApp,
            'errorsMaterialsAppItem'=>$errorsMaterialsAppItem
        ]);
    }




    public function actionGetRowMaterial(){

        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $get = Yii::$app->request->get();

            $count = isset($get['count']) ? (int)$get['count'] : 0;

            $ans['html'] = $this->renderPartial("formRowMaterial",[
                                                    'count'=>$count
                                                ]);
            return $ans;
        }else{
            return $this->goBack();
        }
        
    }



}