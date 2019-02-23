<?php

namespace common\services;

use Yii;
use common\models\{User,MaterialsApp};
use common\modules\ExportMaterialsApp;
use common\modules\exceptions\{
	InvalidPasswordException,
	EmptyRequiredPropertiesException,
	ValidateErrorsException,
	ErrorRelationEntitiesException,
	ErrorExportTo1C,
	ModelNotFoundException,
	ModelCantUpdateException
};


class MaterialSaverService{

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
		$user_guid = $this->user->guid;

		if($id || isset($post['model_id'])){
            $id = isset($post['model_id']) ? (int)$post['model_id'] : (int)$id;
            $q = MaterialsApp::find()->where(['id'=>$id]);
            
            if($this->onlyOwner){
                $q->andWhere(['user_guid'=>$user_guid]);
            }

            $model =  $q->one();
            if(!isset($model->id))
                throw new ModelNotFoundException("Model not found");

            if(!$model->isCanUpdate && $this->onlyOwner)
                throw new ModelCantUpdateException("Model can not update");

            if($this->onlyMaster && $model->master_guid != $user->guid)
                throw new ModelCantUpdateException("Model can not update");
            
        }else{
           $model = new MaterialsApp(); 
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
            $data['MaterialsApp']['user_guid']=$user->guid;
		}
        
        if(!$model->load($data)){
            throw new ValidateErrorsException("Error whe validate form data!");
        }

        if($this->enableGuardValidPassword){
        	if(!isset($post['password']) || !$post['password'])
        		throw new EmptyRequiredPropertiesException("password not found");

        	$password = trim(strip_tags($post['password']));
        	if(!$user->validatePassword($password)){
                throw new InvalidPasswordException("wrong password");    
            }
        }


        if(!$model->save(1)){
        	throw new ValidateErrorsException("Error whe validate form data!");
        }
        
        $model->saveRelationEntities();       

        if(count($model->getItemsErrors())){
        	throw new ErrorRelationEntitiesException("Relation entities has errors!");
        }


		//Отправить в 1С
        if(!ExportMaterialsApp::export($model)){
            throw new ErrorExportTo1C("Error when export to 1C");
        }
                            
        return $model;
	}
}