<?php

namespace common\modules;

use Yii;
use common\models\{User,Raport};
use common\modules\exceptions\{
	InvalidPasswordException,
	EmptyRequiredPropertiesException
};

class RaportServiceSaver{

	public $enableGuardValidPassword = true;

	protected $user;

	protected $model;

	public function __construct(User $user){
		$this->user = $user;

		$this->enableGuardValidPassword = !boolval($user->is_master);
	}


	public function userCant($entity_id = null){

        $brigade_guid = $this->user->brigade_guid;

        return !$brigade_guid && (!$this->user->is_master && !$entity_id);
	}




	public function getForm($post = [], $id = null){
		
		$user = $this->user;
		$brigade_guid = $this->user->brigade_guid;

		if($id || isset($post['model_id'])){
            $id = isset($post['model_id']) ? (int)$post['model_id'] : (int)$id;
            $q = Raport::find()->where(['id'=>$id]);
            
            if(!$user->is_master){
                $q->andWhere(['brigade_guid'=>$brigade_guid]);
            }

            $model =  $q->one();
            if(!isset($model->id))
                throw new \Exception("Документ не найден!",404);

            if(!$model->isCanUpdate && !$user->is_master)
                throw new \Exception("Нет доступа к редактированию документа!",404);

        }else{
           $model = new Raport(); 
        }

        $this->model = $model;
        return $this->model;
	}




	public function getUser(){
		return $this->user;
	}








	public function save(){

		$user = $this->user;
		$model = $this->model;

		$data = $post;
		if(!boolval($user->is_master)){
			$data['Raport']['user_guid']= $user->guid;
        	$data['Raport']['brigade_guid']= $user->brigade_guid;	
		}
        

        if($this->enableGuardValidPassword){
        	if(!isset($post['password']))
        		throw new EmptyRequiredPropertiesException("password not found");

        	$password = trim(strip_tags($post['password']));
        	if(!$user->validatePassword($password)){
                throw new InvalidPasswordException("wrong password");    
            }
        }

        if($model->load($data)){

        }
               

        if($model->save(1)){
                        
                        $model->saveRelationEntities();

                        if(count($model->getConsistErrors()) || count($model->getWorksErrors()) || count($model->getMaterialsErrors())){
                            Yii::$app->session->setFlash("error","Рапорт не сохранен. Некорректные данные в табличной части рапорта имеют не корректные данные");
                            Yii::warning("Error when save raport tables data","raportform");
                            Yii::warning(json_encode($model->getConsistErrors()),"raportform");
                            Yii::warning(json_encode($model->getWorksErrors()),"raportform");
                            Yii::warning(json_encode($model->getMaterialsErrors()),"raportform");
                            $errors = count($errors) ? $errors : $model->getConsistErrors();
                            $errors = count($errors) ? $errors : $model->getWorksErrors();
                            $errors = count($errors) ? $errors : $model->getMaterialsErrors();
                        }else{
                            Yii::$app->session->setFlash("success","Рапорт успешно сохранен!");

                            //Отправить в 1С
                            if($model->sendToConfirmation()){
                                Yii::$app->session->setFlash("success","Рапорт успешно отправлен на проверку!");  
                            }else{
                                Yii::$app->session->setFlash("error","Ошибка, при отправлении рапорта на проверку");
                            }
                            

                            return $this->redirect(['raport/index']);
                        }
                    }else{
                        Yii::$app->session->setFlash("error","Рапорт не сохранен!");
                        Yii::warning("Error when save raport","raportform");
                        Yii::warning(json_encode($model->getErrors()),"raportform");
                        $errors = $model->getErrors();
                    }

                }

            if(count($errors)){
                foreach ($errors as $key => $er) {
                    if(!is_array($er)){
                        Yii::$app->session->setFlash("warning",$er);
                        Yii::warning($key.": ",$er,"raportform");
                    }else{
                        foreach ($er as $key2 => $e) {
                            Yii::$app->session->setFlash("warning",$e);
                            Yii::warning($key2.": ",$e,"raportform");
                        }
                    }
                }
            }
	}
}