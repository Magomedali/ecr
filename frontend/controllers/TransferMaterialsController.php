<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use common\modules\TransferMaterials;
use common\modules\ExportTransferMaterials;
use frontend\modules\MaterialAppFilter;

use common\models\{Raport,Request};
use common\dictionaries\ExchangeStatuses;
use soapclient\methods\TransferOfMaterials;
use common\modules\LoadDocument;
use common\models\Document;
use common\services\TransferMaterialsSaverService;
use common\modules\exceptions\{
    InvalidPasswordException,
    EmptyRequiredPropertiesException,
    ValidateErrorsException,
    ErrorRelationEntitiesException,
    ErrorExportTo1C,
    ModelNotFoundException,
    ModelCantUpdateException
};

class TransferMaterialsController extends Controller{


    protected $user;

    public $command;

    protected $brigade_guid;

    public $transferMaterialsSaverService;

    public function __construct($id,$module,$config = []){
        
        $this->transferMaterialsSaverService = new TransferMaterialsSaverService(Yii::$app->user->identity);
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
                        'actions' => ['view','form','open'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'CheckExsistsUnconfirmingRaport'=>[
                'class'=>\common\behaviors\CheckExsistsUnconfirmingRaport::className(),
                'actions'=>['form'],
                'errorCallback'=>function($user,$action){
                    
                    $action->controller->command = function(){
                    
                        \Yii::$app->session->setFlash("warning","У вас есть неподтвержденные рапорта. При наличии неподтвержденных рапортов, нельзя создать документ передачи материалов на другого мол!!!");
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
                'actions'=>['view'],
            ]
        ];
    }


    /**
     *
     * @return mixed
     */
    public function actionIndex()
    {   
        return $this->redirect(['material/index']);
    }
	
    public function beforeAction($action){
        
        if(defined('YII_DEBUG') && YII_DEBUG){
            Yii::$app->assetManager->forceCopy = true;
        }

        $this->user = Yii::$app->user->identity;
        $this->brigade_guid = isset($this->user->brigade_guid) ? $this->user->brigade_guid : null;
        if(!$this->brigade_guid){
            Yii::$app->user->logout();
            return $this->goHome();
        }

        return parent::beforeAction($action);
    }



    


    public function actionView($id){

        if(!(int)$id) 
            throw new \Exception("Документ не найден!",404);

        $model = MaterialAppFilter::findOne(['id'=>(int)$id,'user_guid'=>$this->user->guid]);

        if(!isset($model->id))
            throw new \Exception("Документ не найден!",404);

        return $this->render('view',['model'=>$model]);
    }








    public function actionForm($request = null){

        $post = Yii::$app->request->post();
        
        $model = $this->transferMaterialsSaverService->getForm($request);
        $unLoadedMaterials = $model->getUnLoadedStructuredMaterials();
        

        if($request && isset($post['cancel']) && boolval($post['cancel'])){
            try {
                $this->transferMaterialsSaverService->cancel($post,$request);
                Yii::$app->session->setFlash("success","Документ отклонен!");
                return $this->redirect(['material/index']);
            }catch(InvalidPasswordException $e){
                Yii::$app->session->setFlash("error","Введен неправильный пароль!");
                $inValidPassword = true;
                return $this->redirect(['transfer-materials/form','request'=>$request]);
            }catch(EmptyRequiredPropertiesException $e){
                $inValidPassword = true;
                Yii::$app->session->setFlash("error","Рапорт не сохранен. Отсутствуют обязательные данные!");
                return $this->redirect(['transfer-materials/form','request'=>$request]);
            }catch (ValidateErrorsException $e) {
                Yii::$app->session->setFlash("error","Документ не удалось отклонить!");
                return $this->redirect(['transfer-materials/form','request'=>$request]);
                
            } catch (ModelNotFoundException $e) {
                Yii::$app->session->setFlash("error","Документ для отклонения не найден!");
                return $this->redirect(['material/index']);
            }
        }

        if($this->command && is_callable($this->command)){
            return call_user_func($this->command);
        }

        $remnants = [];
        $hasErrors = false;
        $errorsTransfer = [];
        $errors = [];

        if(isset($post['TransferMaterials'])){
            
            try {
                $this->transferMaterialsSaverService->save($post);
                Yii::$app->session->setFlash("success","Документ перевода отправлен на подтверждение!");
                return $this->redirect(['material/index']);
            }catch(InvalidPasswordException $e){
            
                Yii::$app->session->setFlash("error","Введен неправильный пароль!");
                $inValidPassword = true;
            
            }catch(EmptyRequiredPropertiesException $e){
                $inValidPassword = true;
                Yii::$app->session->setFlash("error","Рапорт не сохранен. Отсутствуют обязательные данные!");

            }catch(ValidateErrorsException $e){
                Yii::$app->session->setFlash("error","Обнаружены ошибки при заполнении документа.");
                Yii::warning("Error when validate transfermaterials document","transferMaterialForm");
                Yii::warning(json_encode($model->getErrors()),"transferMaterialForm");
                $errors = $model->getErrors();
            }catch(ErrorRelationEntitiesException $e){
                
                Yii::$app->session->setFlash("error","Обнаружены ошибки при заполнении документа.. Некорректные данные в табличной части документа.");
                Yii::warning("Error when validate transfer tables data","transferMaterialForm");
                Yii::warning(json_encode($model->getMaterialsError()),"transferMaterialForm");
                $errors = $model->getMaterialsError();
                
            }catch(ErrorExportTo1C $e){
                Yii::$app->session->setFlash("warning","Ошибка при попытке отправить документ на подтверждение в 1С");
                return $this->redirect(['material/index']);
            }
                   
            

            if(count($errors)){
                foreach ($errors as $key => $er) {
                    if(!is_array($er)){
                        Yii::$app->session->setFlash("warning",$er);
                        Yii::warning($key.": ",$er,"transferMaterialForm");
                    }else{
                        foreach ($er as $key2 => $e) {
                            Yii::$app->session->setFlash("warning",$e);
                            Yii::warning($key2.": ",$e,"transferMaterialForm");
                        }
                    }
                }
            }

            $hasErrors = true;
            $errorsTransfer = isset($post['TransferMaterials']) ? $post['TransferMaterials'] : [];
            $remnants = isset($post['materials']) ? $post['materials'] : [];
            $unLoadedMaterials = [];
        }else{
            $remnants = \common\modules\ImportRemnantsWithSeries::import($this->user->guid);
        }


        return $this->render('form',[
            'model'=>$model,
            'remnants'=>$remnants,
            'hasErrors'=>$hasErrors,
            'errors'=>$errors,
            'request'=>$request,
            'errorsTransfer'=>$errorsTransfer,
            'unLoadedMaterials'=>$unLoadedMaterials
        ]);
    }
}