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

use common\models\Raport;
use common\dictionaries\ExchangeStatuses;

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
        
        if($request){
            $model = TransferMaterials::loadFromRequest($request);
            $unLoadedMaterials = $model->getUnLoadedStructuredMaterials();
        }else{
            $model = new TransferMaterials();
            $unLoadedMaterials = [];
        }

        //Проверка наличии не подтвержденных рапортов;
        $raports = Raport::find()->where(['user_guid'=>$this->user->guid])->andFilterWhere(['in','status',Raport::getUnconfirmedStatuses()])->all();
        
        if(count($raports)){
            Yii::$app->session->setFlash("warning","У вас есть неподтвержденные рапорта (".count($raports)."). При наличии неподтвержденных рапортов, нельзя создать документ передачи материалов на другого мол!!!");
            return $this->redirect(['material/index']);
        }

        $remnants = [];
        $hasErrors = false;
        $errorsTransfer = [];
        $errors = [];

        if(isset($post['TransferMaterials'])){
            
            $data = $post;
            $data['TransferMaterials']['mol_guid']=$this->user->guid;

            if($model->load($data) && $model->validate()){

                if(count($model->getMaterialsError())){
                    Yii::$app->session->setFlash("error","Обнаружены ошибки при заполнении документа.. Некорректные данные в табличной части документа имеют не корректные данные");
                    Yii::warning("Error when validate transfer tables data","transferMaterialForm");
                    Yii::warning(json_encode($model->getMaterialsError()),"transferMaterialForm");
                    $errors = $model->getMaterialsError();
                }else{
                    //Отправить заявку в 1С
                    if(ExportTransferMaterials::export($model)){
                        Yii::$app->session->setFlash("success","Документ перевода отправлен на подтверждение!");
                        return $this->redirect(['material/index']);
                    }else{
                        Yii::$app->session->setFlash("warning","Ошибка при попытке отправить документ на проверку в 1С");
                    }
                }
                   
            }else{
                Yii::$app->session->setFlash("error","Обнаружены ошибки при заполнении документа.");
                Yii::warning("Error when validate transfermaterials document","transferMaterialForm");
                Yii::warning(json_encode($model->getErrors()),"transferMaterialForm");
                $errors = $model->getErrors();
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
            // $this->user->guid = "07b7112a-40af-11e8-8114-005056b47a2e";
            $remnants = \common\modules\ImportRemnantsWithSeries::import($this->user->guid);
        }


        return $this->render('form',[
            'model'=>$model,
            'remnants'=>$remnants,
            'hasErrors'=>$hasErrors,
            'errors'=>$errors,
            'errorsTransfer'=>$errorsTransfer,
            'unLoadedMaterials'=>$unLoadedMaterials
        ]);
    }




    



}