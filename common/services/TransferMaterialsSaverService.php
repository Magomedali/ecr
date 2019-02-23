<?php

namespace common\services;

use Yii;
use common\models\{User,Request};
use common\modules\{ExportTransferMaterials,TransferMaterials};
use common\modules\exceptions\{
	InvalidPasswordException,
	EmptyRequiredPropertiesException,
	ValidateErrorsException,
	ErrorRelationEntitiesException,
	ErrorExportTo1C,
	ModelNotFoundException,
	ModelCantUpdateException
};


class TransferMaterialsSaverService{

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




	public function getForm($request = null){
		
        if($request){
            $model = TransferMaterials::loadFromRequest($request);
        }else{
            $model = new TransferMaterials();
        }


        $this->model = $model;
        return $this->model;
	}




	public function getUser(){
		return $this->user;
	}


    public function cancel($post,$request){

        $req = Request::findOne(['id'=>(int)$request,'user_id'=>$this->user->id]);


        if($this->enableGuardValidPassword){
            $user = $this->user;
            if(!isset($post['password']) || !$post['password'])
                throw new EmptyRequiredPropertiesException("password not found");

            $password = trim(strip_tags($post['password']));
            if(!$user->validatePassword($password)){
                throw new InvalidPasswordException("wrong password");    
            }
        }

        if(!isset($req->id) || !$req->id)
            throw new ModelNotFoundException("Model not found");

        $req->result = 0;
        $req->completed = 1;
        if(!$req->save())
            throw new ValidateErrorsException("Error whe validate form data!");


        return true;
    }


	public function save($post){

		$user = $this->user;
		$model = $this->model;

		$data = $post;

		if(boolval($this->onlyOwner)){
            $data['TransferMaterials']['mol_guid']=$user->guid;
		}
        
        if(!$model->load($data)){
            throw new ValidateErrorsException("Error when load form data!");
        }

        if($this->enableGuardValidPassword){
        	if(!isset($post['password']) || !$post['password'])
        		throw new EmptyRequiredPropertiesException("password not found");

        	$password = trim(strip_tags($post['password']));
        	if(!$user->validatePassword($password)){
                throw new InvalidPasswordException("wrong password");    
            }
        }


        if(!$model->validate()){
        	throw new ValidateErrorsException("Error when validate form data!");
        }      

        if(count($model->getMaterialsError())){
        	throw new ErrorRelationEntitiesException("Relation entities has errors!");
        }


		//Отправить в 1С
        if(!ExportTransferMaterials::export($model)){
            throw new ErrorExportTo1C("Error when export to 1C");
        }
                            
        return $model;
	}
}