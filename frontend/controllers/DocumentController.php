<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use common\dictionaries\DocumentTypes;
use common\modules\LoadDocument;
use common\modules\SendUpdateStatusDocument;
use common\models\{Document,DocumentTransfer};
use common\modules\TransferMaterials;
use common\modules\ExportTransferMaterials;

class DocumentController extends Controller{


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
                        'actions' => ['open','form'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'LoadNotes'=>[
                'class'=>\common\behaviors\LoadNotes::className(),
                'actions'=>['open'],
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
     *
     * @return mixed
     */
    public function actionIndex()
    {   
        return $this->redirect(['material/index']);
    }
    


    public function actionOpen(){
        $get = Yii::$app->request->get();

        if(!isset($get['guid']) || !isset($get['movement_type'])) 
            throw new \Exception("Документ не найден!",404);

        $guid = $get['guid'];
        $movement_type = $get['movement_type'];
        $doc = LoadDocument::import($guid,$movement_type);

        
        if(!$doc || (is_array($doc) && !count($doc)) || !isset($doc['type_of_operation'])){
            return $this->redirect(['material/index']);
        }

        $doc = \common\models\DocumentFactory::create($doc['type_of_operation'],$doc);
        $remnants = [];
        $unLoadedMaterials = [];
        $mol_guid = null;
        if($doc instanceof DocumentTransfer){
            $mol_interaction = \common\models\User::findOne(['name'=>$doc->interaction_name]);
            $mol_guid = isset($mol_interaction->id) ? $mol_interaction->guid : null;
            
            $unLoadedMaterials = $doc->getStructuredMaterials();
            
            $remnants = \common\modules\ImportRemnantsWithSeries::import($this->user->guid);
            if(!is_array($remnants) || !count($remnants)){
                Yii::$app->session->setFlash("warning","У вас не осталось материалов в остатках! Вы можете документ только отменить!");
                // return $this->redirect(['material/index']);
            }
        }
        

        return $this->render('form',[
            'doc'=>$doc,
            'remnants'=>$remnants,
            'mol_guid'=>$mol_guid,
            'unLoadedMaterials'=>$unLoadedMaterials
        ]);
    }








    public function actionForm($id = null){

        $post = Yii::$app->request->post();

        if(isset($post['doc']) && (isset($post['cancel']) || isset($post['commit']))){
            
            if(isset($post['materials']) && isset($post['doc']) && isset($post['doc']['guid']) && $post['doc']['type_of_operation'] == DocumentTypes::TYPE_TRANSFER){

                $model = new TransferMaterials();

                $data = [];
                $data['mol_guid']=$this->user->guid;
                $data['mol_guid_recipient']=$post['doc']['mol_guid_recipient'];
                $data['guid']=$post['doc']['guid'];
                $data['comment']=$post['doc']['comment'];
                $data['date']=$post['doc']['date'];
                $data['status'] = isset($post['cancel']) ? Document::STATUS_DONT_ACCEPTED : Document::STATUS_ACCEPTED;
                $data['materials'] = $post['materials'];

                if($model->load(['TransferMaterials'=>$data]) && $model->validate()){

                    if(count($model->getMaterialsError())){
                        Yii::$app->session->setFlash("error","Обнаружены ошибки при заполнении документа.. Некорректные данные в табличной части документа.");
                        Yii::warning("Error when validate transfer tables data","transferMaterialForm");
                        Yii::warning(json_encode($model->getMaterialsError()),"transferMaterialForm");

                        return $this->redirect(['document/open','guid'=>$model->guid,'movement_type'=>$post['doc']['movement_type']]);
                    }

                    //Отправить заявку в 1С
                    if(ExportTransferMaterials::export($model)){
                        Yii::$app->session->setFlash("success","Документ перевода отправлен на подтверждение!");
                    }else{
                        Yii::$app->session->setFlash("warning","Ошибка при попытке отправить документ на подтверждение в 1С");
                    }

                }

                return $this->redirect(['material/index']);
            }elseif(isset($post['doc']['guid']) && isset($post['doc']['movement_type']) || isset($post['doc']['comment'])){
                $doc_data['document_guid'] = $post['doc']['guid'];
                $doc_data['movement_type'] = $post['doc']['movement_type'];
                $doc_data['status'] = isset($post['cancel']) ? Document::STATUS_DONT_ACCEPTED : Document::STATUS_ACCEPTED;
                $doc_data['comment'] = $post['doc']['comment'];

                SendUpdateStatusDocument::export($doc_data);
                return $this->redirect(['material/index']);
            }

            
        }

        return $this->redirect(['material/index']);
    }




    



}