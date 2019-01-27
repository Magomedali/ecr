<?php

namespace common\modules;

use yii\base\Model;
use common\models\User;


class TransferMaterials extends Model{

    public $mol_guid;
    
    public $mol_guid_recipient;
    
    public $created_at;
    
    public $comment;

	/**
	* Associative multiple array
	* key: nomenclature_guid,count,series_guid
	*/
	protected $materials;

	protected $materialsError = [];

	public function rules(){
        return [
            // name, email, subject and body are required
            [['mol_guid','mol_guid_recipient'], 'required'],
            ['created_at','filter','filter'=>function($v){
                $date = $v ? date("Y-m-d\TH:i:s",strtotime($v)) : date("Y-m-d\TH:i:s",time());

                return $date;
            }],
            ['created_at','default','value'=>date("Y-m-d\TH:i:s",time())],
            [['comment'], 'filter','filter'=>function($v){return trim(strip_tags($v));}],
            [['comment',],'default','value'=>null],
            [['mol_guid','mol_guid_recipient'],'string','max'=>36],
            
        ];
    }



    public function load($data, $formName = null){
        
        if(parent::load($data, $formName)){

            //Проверяем есть ли гуид бригады в базе
            if($this->mol_guid){
                $user = User::findOne(['guid'=>$this->mol_guid]);
                if(!isset($user->id)){
                    $this->addError('mol_guid',"User '".$this->mol_guid."' not exists on the site");
                    return false;
                }
            }

            if($this->mol_guid_recipient){
                $m = User::findOne(['guid'=>$this->mol_guid_recipient,'is_master'=>0]);
                if(!isset($m->id)){
                    $this->addError('mol_guid_recipient',"Master ".$this->mol_guid_recipient." not exists on the site");
                    return false;
                }
            }

            $scope = $formName === null ? $this->formName() : $formName;
            
            if(isset($data[$scope]['materials']) && is_array($data[$scope]['materials'])){
                $this->materials = $data[$scope]['materials'];
            }elseif(isset($data['materials']) && is_array($data['materials'])){
                $this->materials = $data['materials'];
            }else{
                $this->materials = [];
            }

            if(!count($this->materials)){
                $this->addError('materials',"doesn`t have materials");
                return false;
            }else{
                if(!$this->checkAndFilterMaterials()) return false;
            }

            return true;
        }

        return false;
    }

    public function checkAndFilterMaterials(){
        if(!count($this->materials)){
            $this->addError('materials',"Не указаны материалы для передачи!");
            return false;
        }

        $this->materialsError = [];
        $filterMaterials = [];
        foreach ($this->materials as $key => $material) {
            if(!isset($material['nomenclature_guid']) || !$material['nomenclature_guid']
                 || !isset($material['series_guid']) || !$material['series_guid'] 
                 || !isset($material['send'])){
                array_push($this->materialsError, "Отсутствуют обязательные параметры!");
                continue;
            }

            if($material['send'] > 0){
                if(isset($material['count']) && $material['send'] > $material['count']){
                    array_push($this->materialsError, "Недостаточное количество материала");
                    continue;
                }

                $filterMaterials[$key]['nomenclature_guid'] = $material['nomenclature_guid'];
                $filterMaterials[$key]['series_guid'] = $material['series_guid'];
                $filterMaterials[$key]['count'] = $material['send'];
            }

        }

        if(!count($filterMaterials)){
            $this->addError('materials',"Не указаны материалы для передачи!");
            return false;
        }

        $this->materials = $filterMaterials;

        return count($this->materials);
    }

    
    public function getMaterialsError(){
        return $this->materialsError;
    }

    public function getMaterials(){
        return $this->materials;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
        return array(
            'id'=>'Id',
            'guid'=>'Идентификатор в 1С',
            'created_at'=>'Дата создания документа',
            'comment'=>'Комментарий',
        );
    }




    public function getActualTransfersFromMe(){

    }

    public function getActualTransfersToMe(){
        
    }

}
?>