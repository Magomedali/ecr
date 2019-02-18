<?php

namespace common\models;

use yii\base\Model;

abstract class Document extends Model{

	const STATUS_ACCEPTED = "Принят";

	const STATUS_DONT_ACCEPTED = "НеПринят";

	public $openUrl = 'document/open';

	protected $guid;

	protected $status;

	protected $date;

	protected $number;

	protected $movement_type;

	protected $type_of_operation;

	protected $comment;

	protected $interaction_name;

	protected $materials;

	protected $comment_interaction;

	protected $success;

	protected $error;

	public function setGuid($guid){
		$this->guid = $guid;
	}

	public function getGuid(){
		return $this->guid;
	}


	public function setDate($date){
		$this->date = $date;
	}

	public function getDate(){
		return $this->date;
	}


	public function setNumber($number){
		$this->number = $number;
	}

	public function getNumber(){
		return $this->number;
	}


	public function setComment($comment){
		$this->comment = $comment;
	}

	public function getComment(){
		return $this->comment;
	}


	public function setSuccess($success){
		$this->success = $success;
	}

	public function getSuccess(){
		return $this->success;
	}


	public function setError($error){
		$this->error = $error;
	}

	public function getError(){
		return $this->error;
	}


	public function setStatus($status){
		$this->status = $status;
	}

	public function getStatus(){
		return $this->status;
	}

	public function setMovement_type($value){
		$this->movement_type = $value;
	}


	public function getMovement_type(){
		return $this->movement_type;
	}

	public function getType_of_operation(){
		return $this->type_of_operation;
	}


	public function setInteraction_name($interaction_name){
		$this->interaction_name = $interaction_name;
	}

	public function getInteraction_name(){
		return $this->interaction_name;
	}


	public function setMaterials($materials){
		$this->materials = $materials;
	}

	public function getMaterials(){
		return $this->materials;
	}


	public function setComment_interaction($comment_interaction){
		$this->comment_interaction = $comment_interaction;
	}

	public function getComment_interaction(){
		return $this->comment_interaction;
	}



	
}