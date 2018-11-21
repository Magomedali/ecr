<?php
namespace backend\controllers;

use Yii;
use yii\base\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;


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
       

       return $this->render('index',[]);
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
