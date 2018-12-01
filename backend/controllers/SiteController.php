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
                        'actions' => ['index','reset-password'],
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
        
        $dataProvider = $UserSearch->search(Yii::$app->request->get());
        return $this->render('index',['dataProvider'=>$dataProvider,'UserSearch'=>$UserSearch]);
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
