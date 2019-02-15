<?php

namespace common\modules;

use Yii;
use common\models\{User,Raport};
use common\modules\exceptions\{
	InvalidPasswordException,
	EmptyRequiredPropertiesException,
	ValidateErrorsException,
	ErrorRelationEntitiesException,
	ErrorExportTo1C,
	ModelNotFoundException,
	ModelCantUpdateException
};


class RaportServiceSaver{

	public $enableGuardValidPassword = true;

	protected $user;

	protected $model;

	public $onlyOwner = true;

	public $onlyMaster = false;

	public function __construct(User $user){
		$this->user = $user;

		$this->enableGuardValidPassword = !boolval($user->is_master);
		$this->onlyOwner = !boolval($user->is_master);
		$this->onlyMaster = boolval($user->is_master);
	}


	public function userCan($entity_id = null){

        $brigade_guid = $this->user->brigade_guid;

        return ($this->onlyOwner && $brigade_guid) || (!$this->onlyOwner && $entity_id);
	}




	public function getForm($post = [], $id = null){
		
		$user = $this->user;
		$brigade_guid = $this->user->brigade_guid;

		if($id || isset($post['model_id'])){
            $id = isset($post['model_id']) ? (int)$post['model_id'] : (int)$id;
            $q = Raport::find()->where(['id'=>$id]);
            
            if($this->onlyOwner){
                $q->andWhere(['brigade_guid'=>$brigade_guid]);
            }

            $model =  $q->one();
            if(!isset($model->id))
                throw new ModelNotFoundException("Model not found");

            if(!$model->isCanUpdate && $this->onlyOwner)
                throw new ModelCantUpdateException("Model can not update");

            if($this->onlyMaster && $model->master_guid != $user->guid)
                throw new ModelCantUpdateException("Model can not update");
            
        }else{
           $model = new Raport(); 
        }

        $this->model = $model;
        return $this->model;
	}




	public function getUser(){
		return $this->user;
	}




	public function save($post){

		$user = $this->user;
		$model = $this->model;

		$data = $post;
		if(boolval($this->onlyOwner)){
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


        if(!($model->load($data) && $model->save(1))){
        	throw new ValidateErrorsException("Error whe validate form data!");
        }
        
        $model->saveRelationEntities();       

        if(count($model->getConsistErrors()) || count($model->getWorksErrors()) || count($model->getMaterialsErrors())){
        	throw new ErrorRelationEntitiesException("Relation entities has errors!");
        }

		//Отправить в 1С
        if(!$model->sendToConfirmation()){
            throw new ErrorExportTo1C("Error when export to 1C");
        }
                            
        return $model;
	}
}