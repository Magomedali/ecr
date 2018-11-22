<?php
namespace backend\controllers;

use Yii;
use yii\base\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\modules\RequestSearch;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

/**
 * Requests controller
 */
class RequestsController extends Controller
{   

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
                        'actions' => ['index','list','exec-all','exec-unloadremnant','exec-useraccountload','exec-raportload','exec-calcsquare','error'],
                        'allow' => true,
                        'roles'=>['superadmin']
                    ],
                    
                ],
            ]
        ];
    }
    

    public function actionIndex(){
       

        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = yii\web\Response::FORMAT_JSON;

            $RequestSearch = new RequestSearch;
        
            $dataProvider = $RequestSearch->search(Yii::$app->request->get());

            $view = $this->renderPartial('monitoring',['dataProvider'=>$dataProvider,'RequestSearch'=>$RequestSearch]);

            return ['view'=>$view,'date'=>date("d.m.Y H:i:s",time()),'post'=>Yii::$app->request->queryParams];
        
        }else{

            $RequestSearch = new RequestSearch;
        
            $dataProvider = $RequestSearch->search(Yii::$app->request->get());

            $view = $this->renderPartial('monitoring',['dataProvider'=>$dataProvider,'RequestSearch'=>$RequestSearch]);

            return $this->render('index',['view'=>$view]);
        }
       
    }


    public function actionList(){


        return $this->render('list',[]);
    }


    public function actionExecAll(){
        return $this->render('result',[]);
    }



    public function actionExecUnloadRemnant(){
        return $this->render('result',[]);
    }


    public function actionExecUseraccountload(){
        return $this->render('result',[]);
    }


    public function actionExecRaportload(){
        return $this->render('result',[]);
    }


    public function actionExecCalcsquare(){
        return $this->render('result',[]);
    }

    
   
}
