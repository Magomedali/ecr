<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use common\models\User;
use common\models\Technic;
use common\models\TypeOfWork;
use common\models\Line;
use common\models\Objects;
use common\models\Boundary;
use common\models\StockRoom;
use common\models\Nomenclature;
use common\models\{Project,NomenclatureOfTypeOfWorks,ProjectStandard};
use soapclient\methods\Calcsquare;

class AutocompleteController extends Controller{


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
                        'actions' => ['masters','users','brigadier','technics','lines','works','objects','projects','calcsquare','stockroom','nomenclature','project-standarts'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }





	public function actionMasters(){

        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $data = [];
            $get = Yii::$app->request->get();
            $key = isset($get['key']) ? trim(strip_tags($get['key'])) : null;

            $query = User::find()->where(['is_master'=>1])
                        ->andWhere("`guid` is not null")
                        ->andWhere(['status'=>User::STATUS_ACTIVE]);
            if($key){
                $query->andWhere("`name` LIKE '%{$key}%'");
            }
            
            $results = $query->asArray()->all();

            foreach ($results as $key => $value) {
                $data[] = ['value'=>$value['guid'],'title'=>$value['name']]; 
            }
            
            return ['data'=>$data];
        }else{
            return $this->redirect(['site/index']);
        } 
    }






    public function actionUsers(){

        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $data = [];
            $get = Yii::$app->request->get();
            $key = isset($get['key']) ? trim(strip_tags($get['key'])) : null;
            $users_extends = isset($get['users_extends']) && is_array($get['users_extends']) ? $get['users_extends'] : array();

            $query = (new Query())->select(['u.guid','u.name','u.ktu'])
                                ->from(['u'=>User::tableName()])
                                ->where(['u.is_master'=>0])
                                ->andWhere("u.`guid` is not null")
                                ->andWhere(['status'=>User::STATUS_ACTIVE]);
            if($key){
                $query->andWhere("u.`name` LIKE '%{$key}%'");
            }

            if(count($users_extends)){
                $notIn = array();
                foreach ($users_extends as $guid) {
                    if (!$guid) continue;

                    $notIn[] = "'{$guid}'";
                }
                if(count($notIn)){
                    $notIn = implode(",", $notIn);
                    $query->andWhere("u.`guid` NOT IN ($notIn)");
                }
            }

            $results = $query->all();

            foreach ($results as $key => $value) {
                $data[] = [
                    'value'=>$value['guid'],
                    'title'=>$value['name'],
                    'ktu'=>$value['ktu']
                ]; 
            }
            
            return ['data'=>$data];
        }else{
            return $this->redirect(['site/index']);
        } 
    }







    public function actionBrigadier(){

        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $data = [];
            $get = Yii::$app->request->get();
            $key = isset($get['key']) ? trim(strip_tags($get['key'])) : null;

            $cUser = Yii::$app->user->identity;

            $query = (new Query())->select(['u.guid','u.name','u.ktu'])
                                ->from(['u'=>User::tableName()])
                                ->where(['u.is_master'=>0])
                                ->andWhere("u.`guid` is not null and `login` IS NOT NULL")
                                ->andWhere(['status'=>User::STATUS_ACTIVE]);

            if(!boolval($cUser->is_master)){
                $query->andWhere("u.`guid` != '{$cUser->guid}'");
            }

            if($key){
                $query->andWhere("u.`name` LIKE '%{$key}%'");
            }
            $results = $query->all();

            foreach ($results as $key => $value) {
                $data[] = [
                    'value'=>$value['guid'],
                    'title'=>$value['name']
                ]; 
            }
            
            return ['data'=>$data];
        }else{
            return $this->redirect(['site/index']);
        } 
    }





    public function actionNomenclature(){

        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $data = [];
            $get = Yii::$app->request->get();
            $key = isset($get['key']) ? trim(strip_tags($get['key'])) : null;
            $extends = isset($get['extends']) && is_array($get['extends']) ? $get['extends'] : array();

            $query = (new Query())->select(['u.guid','u.name','u.unit'])
                                ->from(['u'=>Nomenclature::tableName()])
                                ->andWhere("u.`guid` is not null");
            if($key){
                $query->andWhere("u.`name` LIKE '%{$key}%'");
            }

            if(count($extends)){
                $notIn = array();
                foreach ($extends as $guid) {
                    if (!$guid) continue;

                    $notIn[] = "'{$guid}'";
                }
                if(count($notIn)){
                    $notIn = implode(",", $notIn);
                    $query->andWhere("u.`guid` NOT IN ($notIn)");
                }
            }

            $results = $query->all();

            foreach ($results as $key => $value) {
                $data[] = [
                    'value'=>$value['guid'],
                    'title'=>$value['name'],
                    'unit'=>$value['unit']
                ]; 
            }
            
            return ['data'=>$data];
        }else{
            return $this->redirect(['site/index']);
        } 
    }


    public function actionStockroom(){

        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $data = [];
            $get = Yii::$app->request->get();
            $key = isset($get['key']) ? trim(strip_tags($get['key'])) : null;

            if(!$key){
                $results = StockRoom::find()->asArray()->all();
            }else{
                $results = StockRoom::find()->where("`name` LIKE '%{$key}%'")->asArray()->all();//
            }
            
            foreach ($results as $key => $value) {
                $data[] = ['value'=>$value['guid'],'title'=>$value['name']]; 
            }
            
            return ['data'=>$data];
        }else{
            return $this->redirect(['site/index']);
        } 
    }


    public function actionTechnics(){

        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $data = [];
            $get = Yii::$app->request->get();
            $key = isset($get['key']) ? trim(strip_tags($get['key'])) : null;

            if(!$key){
                $results = Technic::find()->asArray()->all();
            }else{
                $results = Technic::find()->where("`name` LIKE '%{$key}%'")->asArray()->all();//
            }
            
            foreach ($results as $key => $value) {
                $data[] = ['value'=>$value['guid'],'title'=>$value['name']]; 
            }
            
            return ['data'=>$data];
        }else{
            return $this->redirect(['site/index']);
        } 
    }


    public function actionLines(){

        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $data = [];
            $get = Yii::$app->request->get();
            $key = isset($get['key']) ? trim(strip_tags($get['key'])) : null;

            $query = Line::find();

            if($key){
                $query->where("`name` LIKE '%{$key}%'");//
            }
            
            $results = $query->asArray()->all();//

            foreach ($results as $key => $value) {
                $data[] = [
                    'value'=>$value['guid'],
                    'title'=>$value['name'],
                    'is_countable'=>$value['is_countable'],
                    'hint_count'=>$value['hint_count'],
                    'hint_length'=>$value['hint_length'],
                ]; 
            }
            
            return ['data'=>$data];
        }else{
            return $this->redirect(['site/index']);
        } 
    }






    public function actionWorks(){

        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $data = [];
            $get = Yii::$app->request->get();
            $key = isset($get['key']) ? trim(strip_tags($get['key'])) : null;
            
            $is_regulatory = isset($get['extends']) &&  isset($get['extends']['is_regulatory']) ? boolval($get['extends']['is_regulatory']) : null;

            $query = (new Query())->select(['guid as `value`','name as title','GROUP_CONCAT(rtn.nomenclature_guid SEPARATOR "|") as work_nomenclatures'])
                                    ->from(TypeOfWork::tableName())
                                    ->leftJoin(['rtn'=>NomenclatureOfTypeOfWorks::tableName()],'rtn.typeofwork_guid = guid')
                                    ->groupBy(['guid']);

            if($key){
                $query->where("`name` LIKE '%{$key}%'");
            }

            if($is_regulatory !== null){
                $query->andWhere(['is_regulatory'=>$is_regulatory]);
            }

            $results = $query->all();
            
            
            return ['data'=>$results];
        }else{
            return $this->redirect(['site/index']);
        } 
    }







    public function actionObjects(){

        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $data = [];
            $get = Yii::$app->request->get();
            $key = isset($get['key']) ? trim(strip_tags($get['key'])) : null;


            $query = (new Query())->select(['o.*','b.name as boundary_name','u.name as master_name'])
                                ->from(['o'=>Objects::tableName()])
                                ->leftJoin(['b'=>Boundary::tableName()]," o.boundary_guid = b.guid ")
                                ->leftJoin(['u'=>User::tableName()], " o.master_guid = u.guid");
            if($key){
                $query = $query->where("o.`name` LIKE '%{$key}%'");
            }

            $results = $query->all();

            foreach ($results as $key => $value) {
                $data[] = [
                    'value'=>$value['guid'],
                    'title'=>$value['name'],
                    'boundary_guid'=>$value['boundary_guid'],
                    'boundary_name'=>$value['boundary_name'],
                    'master_guid'=>$value['master_guid'],
                    'master_name'=>$value['master_name']
                ]; 
            }
            
            return ['data'=>$data];
        }else{
            return $this->redirect(['site/index']);
        } 
    }






    public function actionProjects(){

        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $data = [];
            $get = Yii::$app->request->get();
            $key = isset($get['key']) ? trim(strip_tags($get['key'])) : null;
            $object_guid = isset($get['object_guid']) ? trim(strip_tags($get['object_guid'])) : null;

            $query = (new Query())->select(['p.*'])->from(['p'=>Project::tableName()]);

            if($object_guid){
                $query->innerJoin(['po'=>Project::tableNameRelObjects()]," po.project_guid = p.guid ")->andWhere(['po.object_guid'=>$object_guid]);
            }

            if($key){
                $query = $query->andWhere("p.`name` LIKE '%{$key}%'");
            }

            $results = $query->all();
            
            foreach ($results as $key => $value) {
                $data[] = ['value'=>$value['guid'],'title'=>$value['name']]; 
            }
            
            return ['data'=>$data];
        }else{
            return $this->redirect(['site/index']);
        }
    }



    public function actionProjectStandarts(){

        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $data = [];
            $get = Yii::$app->request->get();
            $guid = isset($get['guid']) ? trim(strip_tags($get['guid'])) : null;

            $result = !$guid ? [] : (new Query())->select(['p.*','t.name as typeofwork_name'])
                            ->from(['p'=>ProjectStandard::tableName()])
                            ->innerJoin(['t'=>TypeOfWork::tableName()]," t.guid = p.typeofwork_guid")
                            ->where(['project_guid'=>$guid])
                            ->all();

            
            
            return $result;
        }else{
            return $this->redirect(['site/index']);
        }
    }




    public function actionCalcsquare(){
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            
            $get = Yii::$app->request->get();
            $line_guid = isset($get['line_guid']) ? trim(strip_tags($get['line_guid'])) : null;
            $length = isset($get['length']) ? trim(strip_tags($get['length'])) : null;
            $count = isset($get['count']) ? trim(strip_tags($get['count'])) : null;
            $result = false;
            $error = $errorMessage = null;
            $responce = null;
            if($length && $line_guid){
                try {
                    
                    $method = new Calcsquare([
                        'lineguid'=>$line_guid,
                        'length'=>$length,
                        'count'=>$count
                    ]);
                    if($method->validate()){
                        $responce = Yii::$app->webservice1C->send($method);

                        $responce = json_decode(json_encode($responce),1);
                        if(isset($responce['return']) && isset($responce['return']['success']) && boolval($responce['return']['success']) && isset($responce['return']['result']) && $responce['return']['result']){
                            $result = sprintf("%.3f",$responce['return']['result']);
                        }
                    }else{
                        $error = "ModelValidateError";
                        $errorMessage = $method->getErrors();
                    }

                }catch(\SoapFault $e){
                    $error = "SoapFault";
                    $errorMessage = $e->getMessage();
                }catch(\Exception $e){
                    $error = "ServerError";
                    $errorMessage = $e->getMessage();
                }
            }
            return [
                'result'=>$result,
                'error'=>$error,
                'errorMessage'=>$errorMessage,
                'responce'=>$responce
            ];
        }else{
            return $this->redirect(['site/index']);
        }
    }

}