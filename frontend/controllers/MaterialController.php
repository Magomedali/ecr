<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use common\models\MaterialsApp;
use frontend\modules\MaterialAppFilter;

use common\modules\ImportListOfDocuments;
use common\modules\ExportMaterialsApp;
use common\modules\TransferMaterials;

class MaterialController extends Controller{


    protected $user;


    protected $brigade_guid;


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
                        'actions' => ['index','view','form','get-row-material'],
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

        $this->user = Yii::$app->user->identity;
        $this->brigade_guid = isset($this->user->brigade_guid) ? $this->user->brigade_guid : null;
        if(!$this->brigade_guid){
            Yii::$app->user->logout();
            return $this->goHome();
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
        $dataProvider = $modelFilters->filter($params);

        $documents = ImportListOfDocuments::import($user->guid);
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








    public function actionForm($id = null){


        


        $post = Yii::$app->request->post();

        if($id || isset($post['model_id'])){
            $id = isset($post['model_id']) ? (int)$post['model_id'] : (int)$id;

            $model =  MaterialsApp::findOne(['id'=>$id,'user_guid'=>$this->user->guid]);
            if(!isset($model->id))
                throw new \Exception("Документ не найден!",404);

        }else{
           $model = new MaterialsApp(); 
        }
        
        $hasErrors = false;
        $errorsMaterialsApp=[];
        $errorsMaterialsAppItem = [];
        $errors = [];
        if(isset($post['MaterialsApp'])){

            $data = $post;
            $data['MaterialsApp']['user_guid']=Yii::$app->user->identity->guid;

            if($model->load($data) && $model->save(1)){
                
                $model->saveRelationEntities();

                if(count($model->getItemsErrors())){
                    Yii::$app->session->setFlash("error","Заявка не сохранена. Некорректные данные в табличной части заявки имеют не корректные данные");
                    Yii::warning("Error when save raport tables data","materialform");
                    Yii::warning(json_encode($model->getItemsErrors()),"materialform");
                    $errors = $model->getItemsErrors();
                }else{
                    Yii::$app->session->setFlash("success","Заявка сохранена");

                    //Отправить заявку в 1С
                    ExportMaterialsApp::export($model);

                    return $this->redirect(['material/index']);
                }
                   
            }else{
                Yii::$app->session->setFlash("error","Возникла ошибка при сохранении заявки. Заявка не сохранена!");
                Yii::warning("Error when save raport","materialform");
                Yii::warning(json_encode($model->getErrors()),"materialform");
                $errors = $model->getErrors();
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