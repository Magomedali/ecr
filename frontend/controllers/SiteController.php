<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use frontend\models\LoginForm;
use frontend\models\ResetPasswordForm;
use frontend\modules\TotalOutputFilter;

use common\models\{User,Setting};


/**
 * Site controller
 */
class SiteController extends Controller
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
                        'actions' => ['index','reset-password','logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    //'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {   
        $user = Yii::$app->user->identity;
        


        if($user->is_master){
            return $this->render('master',[

            ]);
        }else{

            $startTime = Setting::getStartShiftTime();
            
            $this->view->params['shift_start'] = $startTime;

            $actualBrigadeRemnants = $user->getActualBrigadeRemnants();
            $brigadeConsist = $user->getBrigadeConsist();
            
            
            $TotalOutputFilter = new TotalOutputFilter;
            $params = Yii::$app->request->queryParams;
            $params['TotalOutputFilter']['brigade_guid']=$user->brigade_guid;
            $dataProviderTotalOutput = $TotalOutputFilter->filter($params);

            return $this->render('index',[
                'model'=>$user,
                'brigadeConsist'=>$brigadeConsist,
                'actualBrigadeRemnants'=>$actualBrigadeRemnants,
                'TotalOutputFilter'=>$TotalOutputFilter,
                'dataProviderTotalOutput'=>$dataProviderTotalOutput
            ]);
        }
    }




    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {   
        $this->layout = "login";
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
           
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }



    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

 
    




    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword()
    {   
        
        $model = new ResetPasswordForm();
        // Yii::$app->webservice1C;
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'Пароль изменен');

            //1. Создать запрос о изменении пароля
            //2. Отправить запрос в 1С

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}
