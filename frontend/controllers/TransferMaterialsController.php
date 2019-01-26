<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use common\modules\TransferMaterials;
use frontend\modules\MaterialAppFilter;

use common\modules\ExportMaterialsApp;

class TransferMaterialsController extends Controller{


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
                        'actions' => ['view','form'],
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

        $model = new TransferMaterials();
        $remnants = [];
        $hasErrors = false;
        $errorsMaterialsApp=[];
        $errorsMaterialsAppItem = [];
        $errors = [];

        if(isset($post['TransferMaterials'])){
            print_r($post);
            exit;
            $data = $post;
            $data['TransferMaterials']['user_guid']=$this->user->guid;

            if($model->load($data) && $model->save(1)){

                if(count($model->getItemsErrors())){
                    Yii::$app->session->setFlash("error","Заявка не сохранена. Некорректные данные в табличной части заявки имеют не корректные данные");
                    Yii::warning("Error when save raport tables data","materialform");
                    Yii::warning(json_encode($model->getItemsErrors()),"materialform");
                    $errors = $model->getItemsErrors();
                }else{
                    Yii::$app->session->setFlash("success","Заявка сохранена");

                    //Отправить заявку в 1С
                    //ExportMaterialsApp::export($model);

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
                            Yii::$app->session->setFlash("warning",$er);
                            Yii::warning($key2.": ",$e,"materialform");
                        }
                    }
                }
            }

            $hasErrors = true;
            $errorsMaterialsApp = isset($post['MaterialsApp']) ? $post['MaterialsApp'] : [];
            $errorsMaterialsAppItem = isset($post['MaterialsAppItem']) ? $post['MaterialsAppItem'] : [];

        }else{
            $this->user->guid = "07b7112a-40af-11e8-8114-005056b47a2e";
            $remnants = \common\modules\ImportRemnantsWithSeries::import($this->user->guid);
        }


        return $this->render('form',[
            'model'=>$model,
            'remnants'=>$remnants,
            'hasErrors'=>$hasErrors,
            'errors'=>$errors,
            'errorsMaterialsApp'=>$errorsMaterialsApp,
            'errorsMaterialsAppItem'=>$errorsMaterialsAppItem
        ]);
    }




    



}