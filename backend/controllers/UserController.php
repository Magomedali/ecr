<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use backend\modules\UserSearch;
use backend\modules\RaportFilter;
use backend\models\ChangePassForm;
use common\models\User;

/**
 * User controller
 */
class UserController extends Controller
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
                        'actions' => ['view','change-user-password','change-status'],
                        'allow' => true,
                        'roles' => ['superadmin','administrator'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    'change-status'=>['post']
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




    public function actionView($id = null)
    {   
        $get = Yii::$app->request->get();
        if(!(int)$id && !isset($get['guid'])) 
            throw new \Exception("Бригадир не найден!",404);
        
        if((int)$id)
            $model = User::findOne(['id'=>(int)$id]);
        else
            $model = User::findOne(['guid'=>$get['guid']]);

        if(!isset($model->id) || !$model->brigade_guid)
            throw new \Exception("Бригадир не найден!",404);

        Yii::$app->getSession()->set(Yii::$app->getUser()->returnUrlParam, ['user/view','id'=>$model->id]);

        $RaportFilter = new RaportFilter;
        $params = Yii::$app->request->queryParams;
        $params['RaportFilter']['brigade_guid']=$model->brigade_guid;
        $params['RaportFilter']['user_guid']=$model->guid;
        $dataProvider = $RaportFilter->filter($params);

        $changePassModel = new ChangePassForm();

        return $this->render('view',[
            'model'=>$model,
            'RaportFilter'=>$RaportFilter,
            'dataProvider'=>$dataProvider,
            'changePassModel'=>$changePassModel
        ]);
    }




    public function actionChangeUserPassword()
    {   
        $model = new ChangePassForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'Пароль изменен');
            return $this->redirect(['user/view','id'=>$model->user_id]);
        }else{
            Yii::$app->session->setFlash('success', 'Произошла ошибка');
            return $this->goHome();
        }
    }



    public function actionChangeStatus(){

        $post = Yii::$app->request->post();
        if(!isset($post['id']) || !(int)$post['id']){
            Yii::$app->session->setFlash("error","Неправильный запрос!");
            return $this->goBack();
        }

        $user = User::findOne((int)$post['id']);
        if(!isset($user['id']) || !(int)$user['id']){
            Yii::$app->session->setFlash("error","Пользователь не существует!");
            return $this->goBack();
        }

        $user->status = $user->status == User::STATUS_ACTIVE ? User::STATUS_DELETED : User::STATUS_ACTIVE;
        if($user->save(false)){
            $msg = $user->status == User::STATUS_ACTIVE ? "Пользователь восстановлен" : "Пользователь переведен в архив";
            Yii::$app->session->setFlash("success",$msg);

            //Выгрузка в 1С

        }else{
            Yii::$app->session->setFlash("error","Ошибка при смене статуса пользователя");
        }

        return $this->goBack();
    }
   
}
