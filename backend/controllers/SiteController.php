<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use backend\models\LoginForm;
use backend\models\ApiTest;
use backend\modules\UserSearch;
use backend\models\ResetPasswordForm;

use common\models\User;

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
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index','archive','reset-password'],
                        'allow' => true,
                        'roles' => ['superadmin','administrator'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
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
            ],
        ];
    }




    public function actionIndex()
    {   

        $UserSearch = new UserSearch;
        $filters = Yii::$app->request->get();
        $filters['UserSearch']['status'] = User::STATUS_ACTIVE;
        $dataProvider = $UserSearch->search($filters);
        
        $this->view->title = "Бригадиры";

        Yii::$app->getSession()->set(Yii::$app->getUser()->returnUrlParam, ['site/index']);

        return $this->render('index',[
            'dataProvider'=>$dataProvider,
            'UserSearch'=>$UserSearch
        ]);
    }


    public function actionArchive(){
        $UserSearch = new UserSearch;
        $filters = Yii::$app->request->get();
        $filters['UserSearch']['status'] = User::STATUS_DELETED;
        $dataProvider = $UserSearch->search($filters);


        Yii::$app->getSession()->set(Yii::$app->getUser()->returnUrlParam, ['site/archive']);

        $this->view->title = "Архив бригадиров";
        return $this->render('index',[
            'dataProvider'=>$dataProvider,
            'UserSearch'=>$UserSearch
        ]);
    }




    public function actionView()
    {   
        return $this->render('view');
    }




    public function actionLogin()
    {   
        $this->layout = 'login';
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
            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
   
}
