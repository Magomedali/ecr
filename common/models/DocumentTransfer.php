<?php

namespace common\models;


class DocumentTransfer extends Document{

	protected $interaction_name;

	protected $materials;

	protected $comment_interaction;

	public function setMovement_type(){
		$this->movement_type = DocumentFactory::TYPE_TRANSFER;
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