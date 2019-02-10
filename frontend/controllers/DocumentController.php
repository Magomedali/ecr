<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use common\modules\LoadDocument;
use common\modules\SendUpdateStatusDocument;
use common\models\Document;

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
        
        if(!$doc || (is_array($doc) && !count($doc))){
            return $this->redirect(['material/index']);
        }

        return $this->render('form',['doc'=>$doc]);
    }








    public function actionForm($id = null){

        $post = Yii::$app->request->post();

        if(isset($post['doc']) && (isset($post['cancel']) || isset($post['commit']))){
            
            $doc_data['document_guid'] = $post['doc']['guid'];
            $doc_data['movement_type'] = $post['doc']['movement_type'];
            $doc_data['status'] = isset($post['cancel']) ? Document::STATUS_DONT_ACCEPTED : Document::STATUS_ACCEPTED;

            if(SendUpdateStatusDocument::export($doc_data)){
                return $this->redirect(['material/index']);
            }

            // print_r($post);
            // exit;

        }

        return $this->redirect(['material/index']);
    }




    



}