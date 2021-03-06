<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use common\dictionaries\AppStatuses;
use frontend\models\LoginForm;
use frontend\models\ResetPasswordForm;
use frontend\modules\TotalOutputFilter;

use frontend\modules\RaportFilter;
use frontend\modules\MaterialAppFilter;
use frontend\modules\RaportRegulatoryFilter;
use common\models\{User,Setting};

use common\base\Controller;


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
            'LoadNotes'=>[
                'class'=>\common\behaviors\LoadNotes::className(),
                'actions'=>['index'],
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

            $RaportFilter = new RaportFilter;
            $params = Yii::$app->request->queryParams;
            $params['RaportFilter']['master_guid']=$user->guid;
            $dataProviderRaport = $RaportFilter->filter($params);


            $RaportRegulatoryFilter = new RaportRegulatoryFilter;
            $params = Yii::$app->request->queryParams;
            $params['RaportRegulatoryFilter']['master_guid']=$user->guid;
            $dataProviderRaportRegulatory = $RaportRegulatoryFilter->filter($params);

            $MaterialAppFilter = new MaterialAppFilter;
            $params = Yii::$app->request->queryParams;
            $params['MaterialAppFilter']['master_guid']=$user->guid;
            $params['MaterialAppFilter']['statusCode']=[AppStatuses::IN_CONFIRMING,AppStatuses::CREATED,AppStatuses::CONFIRMED,AppStatuses::DELETED];
            $dataProviderMaterialApp = $MaterialAppFilter->filter($params);

            return $this->render('master',[
                'dataProviderRaport'=>$dataProviderRaport,
                'RaportFilter'=>$RaportFilter,
                'dataProviderRaportRegulatory'=>$dataProviderRaportRegulatory,
                'RaportRegulatoryFilter'=>$RaportRegulatoryFilter,
                'MaterialAppFilter'=>$MaterialAppFilter,
                'dataProviderMaterialApp'=>$dataProviderMaterialApp,
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
