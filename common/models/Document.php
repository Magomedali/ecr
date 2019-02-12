<?php

namespace common\models;

use yii\base\Model;

abstract class Document extends Model{

	const STATUS_ACCEPTED = "Принят";

	const STATUS_DONT_ACCEPTED = "Не принят";

	protected $guid;

	protected $status;

	protected $date;

	protected $number;

	protected $movement_type;

	protected $comment;

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


	public function getMovement_type(){
		return $this->movement_type;
	}

}